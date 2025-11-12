<?php

namespace App\Http\Controllers;

use App\Models\AdsPlatform;
use Illuminate\Http\Request;

class AdsPlatformController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$platforms = AdsPlatform::orderBy('name')->paginate(15);
		return view('ads-platforms.index', compact('platforms'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return view('ads-platforms.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255|unique:ads_platforms,name',
			'description' => 'nullable|string',
			'is_active' => 'nullable|boolean',
		]);
		$data['is_active'] = $request->has('is_active');
		AdsPlatform::create($data);
		return redirect()->route('ads-platforms.index')->with('status', 'Platform created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(AdsPlatform $adsPlatform)
	{
		return view('ads-platforms.show', compact('adsPlatform'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(AdsPlatform $adsPlatform)
	{
		return view('ads-platforms.edit', compact('adsPlatform'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, AdsPlatform $adsPlatform)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255|unique:ads_platforms,name,' . $adsPlatform->id,
			'description' => 'nullable|string',
			'is_active' => 'nullable|boolean',
		]);
		$data['is_active'] = $request->has('is_active');
		$adsPlatform->update($data);
		return redirect()->route('ads-platforms.index')->with('status', 'Platform updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(AdsPlatform $adsPlatform)
	{
		$adsPlatform->delete();
		return redirect()->route('ads-platforms.index')->with('status', 'Platform deleted.');
	}
}
