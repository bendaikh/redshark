<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$countries = Country::orderBy('name')->paginate(15);
		return view('countries.index', compact('countries'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return view('countries.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'code' => 'required|string|max:3|unique:countries,code',
			'currency' => 'nullable|string|max:10',
			'timezone' => 'nullable|string|max:100',
			'active' => 'boolean',
		]);
		$country = Country::create($data);
		if ($request->input('redirect_to') === 'settings') {
			return redirect()->route('admin.settings')->with('status', 'Country created.');
		}
		return redirect()->route('countries.index')->with('status', 'Country created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Country $country)
	{
		return view('countries.show', compact('country'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Country $country)
	{
		return view('countries.edit', compact('country'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Country $country)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'code' => 'required|string|max:3|unique:countries,code,'.$country->id,
			'currency' => 'nullable|string|max:10',
			'timezone' => 'nullable|string|max:100',
			'active' => 'boolean',
		]);
		$country->update($data);
		if ($request->input('redirect_to') === 'settings') {
			return redirect()->route('admin.settings')->with('status', 'Country updated.');
		}
		return redirect()->route('countries.index')->with('status', 'Country updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request, Country $country)
	{
		$country->delete();
		if ($request->input('redirect_to') === 'settings') {
			return redirect()->route('admin.settings')->with('status', 'Country deleted.');
		}
		return redirect()->route('countries.index')->with('status', 'Country deleted.');
	}
}

