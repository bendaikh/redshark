<?php

namespace App\Http\Controllers;

use App\Models\AdsCampaign;
use App\Models\Country;
use Illuminate\Http\Request;

class AdsCampaignController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
		$from = $request->input('from');
		$to = $request->input('to');

		$query = AdsCampaign::query()
			->when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->orderByDesc('date');

		$campaigns = $query->paginate(15)->withQueryString();
		$totalSpent = (clone $query)->sum('amount_spent');
		$countries = Country::orderBy('name')->get();

		return view('ads.index', compact('campaigns', 'totalSpent', 'countries', 'countryId', 'from', 'to'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$countries = Country::orderBy('name')->get();
		return view('ads.create', compact('countries'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'platform' => 'required|string|max:50',
			'amount_spent' => 'required|numeric|min:0',
			'country_id' => 'required|exists:countries,id',
			'date' => 'required|date',
			'notes' => 'nullable|string',
		]);
		AdsCampaign::create($data);
		return redirect()->route('ads-campaigns.index')->with('status', 'Campaign created.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(AdsCampaign $adsCampaign)
	{
		return view('ads.show', compact('adsCampaign'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(AdsCampaign $adsCampaign)
	{
		$countries = Country::orderBy('name')->get();
		return view('ads.edit', compact('adsCampaign', 'countries'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, AdsCampaign $adsCampaign)
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'platform' => 'required|string|max:50',
			'amount_spent' => 'required|numeric|min:0',
			'country_id' => 'required|exists:countries,id',
			'date' => 'required|date',
			'notes' => 'nullable|string',
		]);
		$adsCampaign->update($data);
		return redirect()->route('ads-campaigns.index')->with('status', 'Campaign updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(AdsCampaign $adsCampaign)
	{
		$adsCampaign->delete();
		return redirect()->route('ads-campaigns.index')->with('status', 'Campaign deleted.');
	}
}

