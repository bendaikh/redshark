<?php

namespace App\Http\Controllers;

use App\Models\Sourcing;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class SourcingController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$productId = $request->input('product_id');
		$q = trim((string) $request->input('q'));

		$sourcings = Sourcing::with(['product'])
			->when($productId, fn($query) => $query->where('product_id', $productId))
			->when($q, fn($query) => $query->whereHas('product', function ($productQuery) use ($q) {
				$productQuery->where('name', 'like', "%{$q}%");
			}))
			->orderBy('sourcing_date', 'desc')
			->orderBy('created_at', 'desc')
			->paginate(15)
			->withQueryString();

		$products = Product::orderBy('name')->get();
		return view('sourcings.index', compact('sourcings', 'products', 'productId', 'q'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$products = Product::orderBy('name')->get();
		$shippingMethods = ShippingMethod::where('active', true)->orderBy('name')->get();
		return view('sourcings.create', compact('products', 'shippingMethods'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'product_id' => 'required|exists:products,id',
			'shipping_cost' => 'required|numeric|min:0',
			'shipping_method' => 'nullable|string|max:255',
			'sourcing_date' => 'nullable|date',
			'notes' => 'nullable|string',
		]);
		Sourcing::create($data);
		return redirect()->route('sourcings.index')->with('status', 'Sourcing created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Sourcing $sourcing)
	{
		return view('sourcings.show', compact('sourcing'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Sourcing $sourcing)
	{
		$products = Product::orderBy('name')->get();
		$shippingMethods = ShippingMethod::where('active', true)->orderBy('name')->get();
		return view('sourcings.edit', compact('sourcing', 'products', 'shippingMethods'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Sourcing $sourcing)
	{
		$data = $request->validate([
			'product_id' => 'required|exists:products,id',
			'shipping_cost' => 'required|numeric|min:0',
			'shipping_method' => 'nullable|string|max:255',
			'sourcing_date' => 'nullable|date',
			'notes' => 'nullable|string',
		]);
		$sourcing->update($data);
		return redirect()->route('sourcings.index')->with('status', 'Sourcing updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Sourcing $sourcing)
	{
		$sourcing->delete();
		return redirect()->route('sourcings.index')->with('status', 'Sourcing deleted.');
	}
}
