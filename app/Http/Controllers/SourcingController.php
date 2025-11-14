<?php

namespace App\Http\Controllers;

use App\Models\Sourcing;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Country;
use App\Models\Category;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SourcingController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$q = trim((string) $request->input('q'));

		$sourcings = Sourcing::with(['category', 'country', 'supplier'])
			->when($q, fn($query) => $query->where('product_name', 'like', "%{$q}%"))
			->orderBy('sourcing_date', 'desc')
			->orderBy('created_at', 'desc')
			->paginate(15)
			->withQueryString();

		return view('sourcings.index', compact('sourcings', 'q'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		$categories = Category::orderBy('name')->get();
		$shippingMethods = ShippingMethod::where('active', true)->orderBy('name')->get();
		return view('sourcings.create', compact('suppliers', 'countries', 'categories', 'shippingMethods'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'product_name' => 'required|string|max:255',
			'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'category_id' => 'nullable|exists:categories,id',
			'quantity' => 'required|integer|min:0',
			'country_id' => 'required|exists:countries,id',
			'price' => 'required|numeric|min:0', // Unit Price
			'shipping_type' => 'nullable|in:in_transit,arrived',
			'additional_fees' => 'nullable|numeric|min:0',
			'testing_fees' => 'nullable|numeric|min:0',
			'supplier_id' => 'nullable|exists:suppliers,id',
			'shipping_cost' => 'nullable|numeric|min:0',
			'shipping_method' => 'nullable|string|max:255',
			'sourcing_date' => 'nullable|date',
			'notes' => 'nullable|string',
		]);

		// Handle image upload
		if ($request->hasFile('product_image')) {
			$data['product_image'] = $request->file('product_image')->store('sourcings', 'public');
		}

		// Calculate Price Total automatically: Unit Price * Quantity
		$data['cost'] = ($data['price'] ?? 0) * ($data['quantity'] ?? 0);

		// Create sourcing without validating (validated = false by default)
		$data['validated'] = false;
		$sourcing = Sourcing::create($data);
		
		return redirect()->route('sourcings.index')->with('status', 'Sourcing created. Please validate it to create the product.');
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
		$suppliers = Supplier::orderBy('name')->get();
		$countries = Country::orderBy('name')->get();
		$categories = Category::orderBy('name')->get();
		$shippingMethods = ShippingMethod::where('active', true)->orderBy('name')->get();
		return view('sourcings.edit', compact('sourcing', 'suppliers', 'countries', 'categories', 'shippingMethods'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Sourcing $sourcing)
	{
		$data = $request->validate([
			'product_name' => 'required|string|max:255',
			'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'category_id' => 'nullable|exists:categories,id',
			'quantity' => 'required|integer|min:0',
			'country_id' => 'required|exists:countries,id',
			'price' => 'required|numeric|min:0', // Unit Price
			'shipping_type' => 'nullable|in:in_transit,arrived',
			'additional_fees' => 'nullable|numeric|min:0',
			'testing_fees' => 'nullable|numeric|min:0',
			'supplier_id' => 'nullable|exists:suppliers,id',
			'shipping_cost' => 'nullable|numeric|min:0',
			'shipping_method' => 'nullable|string|max:255',
			'sourcing_date' => 'nullable|date',
			'notes' => 'nullable|string',
		]);

		// Handle image upload
		if ($request->hasFile('product_image')) {
			if ($sourcing->product_image) {
				Storage::disk('public')->delete($sourcing->product_image);
			}
			$data['product_image'] = $request->file('product_image')->store('sourcings', 'public');
		}

		// Calculate Price Total automatically: Unit Price * Quantity
		$data['cost'] = ($data['price'] ?? 0) * ($data['quantity'] ?? 0);

		$sourcing->update($data);
		return redirect()->route('sourcings.index')->with('status', 'Sourcing updated.');
	}

	/**
	 * Validate a sourcing and create the product.
	 */
	public function validateSourcing(Sourcing $sourcing)
	{
		// Check if already validated
		if ($sourcing->validated) {
			return redirect()->route('sourcings.index')->with('status', 'This sourcing has already been validated.');
		}

		// Create product from sourcing data
		// Calculate cost_total: (Price Total + Additional Fees + Testing Fees + Shipping Cost) / Quantity
		$priceTotal = $sourcing->cost ?? 0; // Price Total
		$additionalFees = $sourcing->additional_fees ?? 0;
		$testingFees = $sourcing->testing_fees ?? 0;
		$shippingCost = $sourcing->shipping_cost ?? 0;
		$quantity = $sourcing->quantity ?? 1;
		$costTotal = $quantity > 0 ? ($priceTotal + $additionalFees + $testingFees + $shippingCost) / $quantity : 0;

		$productData = [
			'name' => $sourcing->product_name,
			'image' => $sourcing->product_image,
			'category_id' => $sourcing->category_id,
			'country_id' => $sourcing->country_id,
			'quantity' => $sourcing->quantity, // Include quantity from sourcing
			'cost' => $costTotal, // Use Cost Total per unit for product cost
			'low_stock_threshold' => 5, // Default value
		];

		Product::create($productData);

		// Mark sourcing as validated
		$sourcing->update(['validated' => true]);

		return redirect()->route('sourcings.index')->with('status', 'Sourcing validated and product created successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Sourcing $sourcing)
	{
		if ($sourcing->product_image) {
			Storage::disk('public')->delete($sourcing->product_image);
		}
		$sourcing->delete();
		return redirect()->route('sourcings.index')->with('status', 'Sourcing deleted.');
	}
}
