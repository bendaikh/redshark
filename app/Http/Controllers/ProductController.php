<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Country;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
		$q = trim((string) $request->input('q'));
		$dateFrom = $request->input('date_from');
		$dateTo = $request->input('date_to');

		$products = Product::with(['country', 'category', 'sourcings'])
			->when($countryId, fn($query) => $query->where('country_id', $countryId))
			->when($q, fn($query) => $query->where(function ($qq) use ($q) {
				$qq->where('name', 'like', "%{$q}%")
				   ->orWhereHas('category', function ($categoryQuery) use ($q) {
					   $categoryQuery->where('name', 'like', "%{$q}%");
				   });
			}))
			->orderBy('name')
			->paginate(15)
			->withQueryString();

		$countries = Country::orderBy('name')->get();
		return view('products.index', compact('products', 'countries', 'countryId', 'q', 'dateFrom', 'dateTo'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$countries = Country::orderBy('name')->get();
		$categories = Category::orderBy('name')->get();
		return view('products.create', compact('countries', 'categories'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'import_id' => 'nullable|string|max:255',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'category_id' => 'nullable|exists:categories,id',
			'country_id' => 'required|exists:countries,id',
			'low_stock_threshold' => 'nullable|integer|min:0',
		]);

		if (array_key_exists('import_id', $data) && $data['import_id'] === '') {
			$data['import_id'] = null;
		}

		if ($request->hasFile('image')) {
			$data['image'] = $request->file('image')->store('products', 'public');
		}

		Product::create($data);
		return redirect()->route('products.index')->with('status', 'Product created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Product $product)
	{
		return view('products.show', compact('product'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Product $product)
	{
		$countries = Country::orderBy('name')->get();
		$categories = Category::orderBy('name')->get();
		return view('products.edit', compact('product', 'countries', 'categories'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Product $product)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'import_id' => 'nullable|string|max:255',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'category_id' => 'nullable|exists:categories,id',
			'country_id' => 'required|exists:countries,id',
			'low_stock_threshold' => 'nullable|integer|min:0',
		]);

		if (array_key_exists('import_id', $data) && $data['import_id'] === '') {
			$data['import_id'] = null;
		}

		if ($request->hasFile('image')) {
			if ($product->image) {
				Storage::disk('public')->delete($product->image);
			}
			$data['image'] = $request->file('image')->store('products', 'public');
		}

		$product->update($data);
		return redirect()->route('products.index')->with('status', 'Product updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Product $product)
	{
		if ($product->image) {
			Storage::disk('public')->delete($product->image);
		}
		$product->delete();
		return redirect()->route('products.index')->with('status', 'Product deleted.');
	}

	/**
	 * Get product statistics data
	 */
	public function statistics(Product $product)
	{
		$product->load(['invoiceItems', 'adsCampaigns']);
		
		// Get time-based data for charts
		$invoiceItems = $product->invoiceItems()
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
			->selectRaw('DATE(invoices.date) as date, SUM(total_orders) as orders, SUM(quantity_sold) as sold, SUM(revenue) as revenue')
			->groupBy('date')
			->orderBy('date')
			->get();

		$adsData = DB::table('ads_campaign_product')
			->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
			->where('ads_campaign_product.product_id', $product->id)
			->selectRaw('DATE(ads_campaigns.date_from) as date, SUM(ads_campaign_product.amount_spent) as spent, SUM(ads_campaign_product.leads) as leads')
			->groupBy('date')
			->orderBy('date')
			->get();

		return response()->json([
			'product' => [
				'id' => $product->id,
				'name' => $product->name,
				'total_leads' => $product->total_leads,
				'total_orders' => $product->total_orders,
				'total_ads_cost' => $product->total_ads_cost,
				'delivery_rate' => $product->delivery_rate,
				'cost_per_lead' => $product->cost_per_lead,
				'cost_per_delivered' => $product->cost_per_delivered,
				'quantity' => $product->quantity,
				'remaining_qty' => $product->remaining_qty,
				'total_amount' => $product->total_amount,
				'net_profit' => $product->net_profit,
			],
			'invoiceItems' => $invoiceItems,
			'adsData' => $adsData,
		]);
	}

	/**
	 * Show the form for assigning media buyers to a product.
	 */
	public function assignMediaBuyers(Product $product)
	{
		$mediaBuyers = User::role('media_buyer')->orderBy('name')->get();
		$assignedMediaBuyers = $product->mediaBuyers->pluck('id')->toArray();
		
		// Get current cost_total values for assigned media buyers
		$mediaBuyerData = [];
		foreach ($product->mediaBuyers as $mb) {
			$mediaBuyerData[$mb->id] = [
				'cost_total' => $mb->pivot->cost_total ?? ''
			];
		}
		
		return view('products.assign-media-buyers', compact('product', 'mediaBuyers', 'assignedMediaBuyers', 'mediaBuyerData'));
	}

	/**
	 * Update the media buyers assigned to a product.
	 */
	public function updateMediaBuyers(Request $request, Product $product)
	{
		$request->validate([
			'media_buyers' => 'nullable|array',
			'media_buyers.*' => 'exists:users,id',
			'cost_totals' => 'nullable|array',
			'cost_totals.*' => 'nullable|numeric|min:0',
		]);

		$mediaBuyers = $request->input('media_buyers', []);
		$costTotals = $request->input('cost_totals', []);
		
		// Prepare sync data with cost_total values
		$syncData = [];
		foreach ($mediaBuyers as $userId) {
			$syncData[$userId] = [
				'cost_total' => $costTotals[$userId] ?? null
			];
		}
		
		$product->mediaBuyers()->sync($syncData);
		
		return redirect()->route('products.index')->with('status', 'Media buyers assigned successfully.');
	}
}

