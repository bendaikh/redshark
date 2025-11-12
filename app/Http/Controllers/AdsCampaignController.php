<?php

namespace App\Http\Controllers;

use App\Models\AdsCampaign;
use App\Models\AdsPlatform;
use App\Models\Country;
use App\Models\Product;
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

		$query = AdsCampaign::with(['platform', 'country', 'products'])
			->when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date_from', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date_to', '<=', $to))
			->orderByDesc('date_from');

		$campaigns = $query->paginate(15)->withQueryString();
		$totalSpent = (clone $query)->get()->sum('total_amount_spent');
		$countries = Country::orderBy('name')->get();

		return view('ads.index', compact('campaigns', 'totalSpent', 'countries', 'countryId', 'from', 'to'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$countries = Country::orderBy('name')->get();
		$platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();
		$products = Product::orderBy('name')->get();
		return view('ads.create', compact('countries', 'platforms', 'products'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$data = $request->validate([
			'platform_id' => 'required|exists:ads_platforms,id',
			'country_id' => 'required|exists:countries,id',
			'date_from' => 'required|date',
			'date_to' => 'required|date|after_or_equal:date_from',
			'products' => 'required|array|min:1',
			'products.*.id' => 'required|exists:products,id',
			'products.*.amount_spent' => 'required|numeric|min:0',
		]);

		$campaign = AdsCampaign::create([
			'name' => 'Campaign ' . now()->format('Y-m-d H:i'),
			'platform_id' => $data['platform_id'],
			'country_id' => $data['country_id'],
			'date_from' => $data['date_from'],
			'date_to' => $data['date_to'],
		]);

		// Attach products with amount spent
		$productsData = [];
		foreach ($data['products'] as $product) {
			$productsData[$product['id']] = ['amount_spent' => $product['amount_spent']];
		}
		$campaign->products()->attach($productsData);

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
		$adsCampaign->load('products');
		$countries = Country::orderBy('name')->get();
		$platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();
		$products = Product::orderBy('name')->get();
		return view('ads.edit', compact('adsCampaign', 'countries', 'platforms', 'products'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, AdsCampaign $adsCampaign)
	{
		$data = $request->validate([
			'platform_id' => 'required|exists:ads_platforms,id',
			'country_id' => 'required|exists:countries,id',
			'date_from' => 'required|date',
			'date_to' => 'required|date|after_or_equal:date_from',
			'products' => 'required|array|min:1',
			'products.*.id' => 'required|exists:products,id',
			'products.*.amount_spent' => 'required|numeric|min:0',
		]);

		$adsCampaign->update([
			'platform_id' => $data['platform_id'],
			'country_id' => $data['country_id'],
			'date_from' => $data['date_from'],
			'date_to' => $data['date_to'],
		]);

		// Sync products with amount spent
		$productsData = [];
		foreach ($data['products'] as $product) {
			$productsData[$product['id']] = ['amount_spent' => $product['amount_spent']];
		}
		$adsCampaign->products()->sync($productsData);

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

