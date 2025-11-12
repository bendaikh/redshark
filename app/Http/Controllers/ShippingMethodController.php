<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
	/**
	 * Display a listing of shipping methods.
	 */
	public function index()
	{
		// Get all managed shipping methods
		$managedMethods = ShippingMethod::orderBy('name')->get();

		return view('shipping-methods.index', compact('managedMethods'));
	}

	/**
	 * Show the form for creating a new shipping method.
	 */
	public function create()
	{
		return view('shipping-methods.create');
	}

	/**
	 * Store a newly created shipping method.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255|unique:shipping_methods,name',
			'description' => 'nullable|string',
			'active' => 'nullable',
		]);

		$data['active'] = $request->has('active') ? true : false;
		ShippingMethod::create($data);
		return redirect()->route('shipping-methods.index')->with('status', 'Shipping method created.');
	}

	/**
	 * Show the form for editing the specified shipping method.
	 */
	public function edit(ShippingMethod $shippingMethod)
	{
		return view('shipping-methods.edit', compact('shippingMethod'));
	}

	/**
	 * Update the specified shipping method.
	 */
	public function update(Request $request, ShippingMethod $shippingMethod)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255|unique:shipping_methods,name,' . $shippingMethod->id,
			'description' => 'nullable|string',
			'active' => 'nullable',
		]);

		$data['active'] = $request->has('active') ? true : false;
		$shippingMethod->update($data);
		return redirect()->route('shipping-methods.index')->with('status', 'Shipping method updated.');
	}

	/**
	 * Remove the specified shipping method.
	 */
	public function destroy(ShippingMethod $shippingMethod)
	{
		$shippingMethod->delete();
		return redirect()->route('shipping-methods.index')->with('status', 'Shipping method deleted.');
	}
}
