<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
			->with('supplier', 'country')
			->when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->orderByDesc('date');

		$invoices = $query->paginate(15)->withQueryString();
		$totalAmount = (clone $query)->sum('total_amount');
		$countries = Country::orderBy('name')->get();

		return view('invoices.index', compact('invoices', 'totalAmount', 'countries', 'countryId', 'from', 'to'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		return view('invoices.create', compact('suppliers', 'countries'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'supplier_id' => 'nullable|exists:suppliers,id',
			'invoice_number' => 'required|string|max:255',
			'total_amount' => 'required|numeric|min:0',
			'currency' => 'required|string|max:10',
			'date' => 'required|date',
			'country_id' => 'required|exists:countries,id',
			'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
		]);
		if ($request->hasFile('attachment')) {
			$data['attachment_path'] = $request->file('attachment')->store('invoices', 'public');
		}
		Invoice::create($data);
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
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		return view('invoices.edit', compact('invoice', 'suppliers', 'countries'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Invoice $invoice)
	{
		$data = $request->validate([
			'supplier_id' => 'nullable|exists:suppliers,id',
			'invoice_number' => 'required|string|max:255',
			'total_amount' => 'required|numeric|min:0',
			'currency' => 'required|string|max:10',
			'date' => 'required|date',
			'country_id' => 'required|exists:countries,id',
			'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
		]);
		if ($request->hasFile('attachment')) {
			if ($invoice->attachment_path) {
				Storage::disk('public')->delete($invoice->attachment_path);
			}
			$data['attachment_path'] = $request->file('attachment')->store('invoices', 'public');
		}
		$invoice->update($data);
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

