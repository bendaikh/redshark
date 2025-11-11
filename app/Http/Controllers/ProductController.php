<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Country;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
		$q = trim((string) $request->input('q'));

		$products = Product::with(['supplier', 'country'])
			->when($countryId, fn($query) => $query->where('country_id', $countryId))
			->when($q, fn($query) => $query->where(function ($qq) use ($q) {
				$qq->where('name', 'like', "%{$q}%")
				   ->orWhere('category', 'like', "%{$q}%");
			}))
			->orderBy('name')
			->paginate(15)
			->withQueryString();

		$countries = Country::orderBy('name')->get();
		return view('products.index', compact('products', 'countries', 'countryId', 'q'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		return view('products.create', compact('suppliers', 'countries'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'category' => 'nullable|string|max:255',
			'quantity' => 'required|integer|min:0',
			'cost' => 'required|numeric|min:0',
			'selling_price' => 'required|numeric|min:0',
			'supplier_id' => 'nullable|exists:suppliers,id',
			'country_id' => 'required|exists:countries,id',
			'low_stock_threshold' => 'nullable|integer|min:0',
		]);
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
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		return view('products.edit', compact('product', 'suppliers', 'countries'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Product $product)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'category' => 'nullable|string|max:255',
			'quantity' => 'required|integer|min:0',
			'cost' => 'required|numeric|min:0',
			'selling_price' => 'required|numeric|min:0',
			'supplier_id' => 'nullable|exists:suppliers,id',
			'country_id' => 'required|exists:countries,id',
			'low_stock_threshold' => 'nullable|integer|min:0',
		]);
		$product->update($data);
		return redirect()->route('products.index')->with('status', 'Product updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Product $product)
	{
		$product->delete();
		return redirect()->route('products.index')->with('status', 'Product deleted.');
	}
}

