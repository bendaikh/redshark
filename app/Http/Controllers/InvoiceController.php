<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Supplier;
use App\Models\Country;
use App\Models\Product;
use App\Models\DeliveryFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
		$from = $request->input('from');
		$to = $request->input('to');

		$query = Invoice::query()
			->with('country', 'items.product', 'items.deliveryFee')
			->when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->where(function($q) use ($from) {
				$q->whereDate('date_from', '>=', $from)
				  ->orWhereDate('date_to', '>=', $from)
				  ->orWhereNull('date_from');
			}))
			->when($to, fn($q) => $q->where(function($q) use ($to) {
				$q->whereDate('date_from', '<=', $to)
				  ->orWhereDate('date_to', '<=', $to)
				  ->orWhereNull('date_to');
			}))
			->orderByDesc('created_at');

		$invoices = $query->paginate(15)->withQueryString();
		$countries = Country::orderBy('name')->get();

		return view('invoices.index', compact('invoices', 'countries', 'countryId', 'from', 'to'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$countries = Country::orderBy('name')->get();
		$products = Product::with('sourcings')->orderBy('name')->get();
		$deliveryFees = DeliveryFee::where('active', true)->orderBy('name')->get();
		return view('invoices.create', compact('countries', 'products', 'deliveryFees'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'date_from' => 'nullable|date',
			'date_to' => 'nullable|date',
			'country_id' => 'required|exists:countries,id',
			'products' => 'required|array|min:1',
			'products.*.id' => 'required|exists:products,id',
			'products.*.revenue' => 'nullable|numeric|min:0',
			'products.*.total_orders' => 'nullable|integer|min:0',
			'products.*.quantity_sold' => 'nullable|integer|min:0',
			'products.*.delivery_fee_id' => 'nullable|exists:delivery_fees,id',
		]);

		// Set default values for removed fields
		$data['supplier_id'] = null;
		$data['invoice_number'] = 'INV-' . now()->format('YmdHis');
		$data['total_amount'] = 0;
		$data['currency'] = 'USD';
		$data['date'] = now()->toDateString();

	DB::transaction(function () use ($data, $request) {
		$invoice = Invoice::create($data);

		foreach ($request->input('products', []) as $productData) {
			$product = Product::with('sourcings')->find($productData['id']);
			$deliveryFee = $productData['delivery_fee_id'] ? DeliveryFee::find($productData['delivery_fee_id']) : null;
			
			$revenue = $productData['revenue'] ?? 0;
			$productCost = $product->average_cost ?? 0;
			$totalOrders = $productData['total_orders'] ?? 0;
			$quantitySold = $productData['quantity_sold'] ?? 0;
			$deliveryFeePerUnit = $deliveryFee ? $deliveryFee->fee_per_unit : 0;
			
			// Calculate Total Amount: Revenue - (Total Orders × Delivery Fees) - (Quantity Sold × Cost)
			$totalDeliveryFee = $totalOrders * $deliveryFeePerUnit;
			$totalProductCost = $quantitySold * $productCost;
			$totalAmount = $revenue - $totalDeliveryFee - $totalProductCost;

			InvoiceItem::create([
				'invoice_id' => $invoice->id,
				'product_id' => $productData['id'],
				'quantity' => $productData['quantity_sold'] ?? 0,
				'unit_cost' => $product->average_cost ?? 0,
				'total_cost' => ($product->average_cost ?? 0) * ($productData['quantity_sold'] ?? 0),
				'revenue' => $revenue,
				'total_orders' => $productData['total_orders'] ?? 0,
				'quantity_sold' => $productData['quantity_sold'] ?? 0,
				'delivery_fee_id' => $productData['delivery_fee_id'] ?? null,
				'ads_cost' => 0,
				'net_profit' => $totalAmount, // Store Total Amount in net_profit field for backward compatibility
			]);
		}
	});

	return redirect()->route('invoices.index')->with('status', 'Invoice created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Invoice $invoice)
	{
		return view('invoices.show', compact('invoice'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Invoice $invoice)
	{
		$invoice->load('items.product', 'items.deliveryFee');
		$countries = Country::orderBy('name')->get();
		$products = Product::with('sourcings')->orderBy('name')->get();
		$deliveryFees = DeliveryFee::where('active', true)->orderBy('name')->get();
		return view('invoices.edit', compact('invoice', 'countries', 'products', 'deliveryFees'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Invoice $invoice)
	{
		$data = $request->validate([
			'date_from' => 'nullable|date',
			'date_to' => 'nullable|date',
			'country_id' => 'required|exists:countries,id',
			'products' => 'required|array|min:1',
			'products.*.id' => 'required|exists:products,id',
			'products.*.revenue' => 'nullable|numeric|min:0',
			'products.*.total_orders' => 'nullable|integer|min:0',
			'products.*.quantity_sold' => 'nullable|integer|min:0',
			'products.*.delivery_fee_id' => 'nullable|exists:delivery_fees,id',
		]);

		// Keep existing values for removed fields (don't update them)
		$data['supplier_id'] = $invoice->supplier_id;
		$data['invoice_number'] = $invoice->invoice_number;
		$data['total_amount'] = $invoice->total_amount;
		$data['currency'] = $invoice->currency;
		$data['date'] = $invoice->date;

	DB::transaction(function () use ($invoice, $data, $request) {
		$invoice->update($data);
		
		// Delete existing items
		$invoice->items()->delete();

		// Create new items
		foreach ($request->input('products', []) as $productData) {
			$product = Product::with('sourcings')->find($productData['id']);
			$deliveryFee = $productData['delivery_fee_id'] ? DeliveryFee::find($productData['delivery_fee_id']) : null;
			
			$revenue = $productData['revenue'] ?? 0;
			$productCost = $product->average_cost ?? 0;
			$totalOrders = $productData['total_orders'] ?? 0;
			$quantitySold = $productData['quantity_sold'] ?? 0;
			$deliveryFeePerUnit = $deliveryFee ? $deliveryFee->fee_per_unit : 0;
			
			// Calculate Total Amount: Revenue - (Total Orders × Delivery Fees) - (Quantity Sold × Cost)
			$totalDeliveryFee = $totalOrders * $deliveryFeePerUnit;
			$totalProductCost = $quantitySold * $productCost;
			$totalAmount = $revenue - $totalDeliveryFee - $totalProductCost;

			InvoiceItem::create([
				'invoice_id' => $invoice->id,
				'product_id' => $productData['id'],
				'quantity' => $productData['quantity_sold'] ?? 0,
				'unit_cost' => $product->average_cost ?? 0,
				'total_cost' => ($product->average_cost ?? 0) * ($productData['quantity_sold'] ?? 0),
				'revenue' => $revenue,
				'total_orders' => $productData['total_orders'] ?? 0,
				'quantity_sold' => $productData['quantity_sold'] ?? 0,
				'delivery_fee_id' => $productData['delivery_fee_id'] ?? null,
				'ads_cost' => 0,
				'net_profit' => $totalAmount, // Store Total Amount in net_profit field for backward compatibility
			]);
		}
	});

	return redirect()->route('invoices.index')->with('status', 'Invoice updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invoice $invoice)
	{
		if ($invoice->attachment_path) {
			Storage::disk('public')->delete($invoice->attachment_path);
		}
		$invoice->delete();
		return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
	}
}

