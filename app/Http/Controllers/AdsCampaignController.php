<?php

namespace App\Http\Controllers;

use App\Models\AdsCampaign;
use App\Models\AdsPlatform;
use App\Models\Country;
use App\Models\Product;
use App\Services\AdsCampaignImportService;
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
		$platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();

		return view('ads.index', compact('campaigns', 'totalSpent', 'countries', 'countryId', 'from', 'to', 'platforms'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$countries = Country::orderBy('name')->get();
		$platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();
		return view('ads.create', compact('countries', 'platforms'));
	}

	/**
	 * Get products by country
	 */
	public function getProductsByCountry(Request $request)
	{
		$countryId = $request->input('country_id');
		$products = Product::where('country_id', $countryId)
			->orderBy('name')
			->get(['id', 'name']);
		return response()->json($products);
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
			'products.*.leads' => 'nullable|integer|min:0',
		]);

		$campaign = AdsCampaign::create([
			'name' => 'Campaign ' . now()->format('Y-m-d H:i'),
			'platform_id' => $data['platform_id'],
			'country_id' => $data['country_id'],
			'date_from' => $data['date_from'],
			'date_to' => $data['date_to'],
		]);

		// Attach products with amount spent and leads
		$productsData = [];
		foreach ($data['products'] as $product) {
			$productsData[$product['id']] = [
				'amount_spent' => $product['amount_spent'],
				'leads' => $product['leads'] ?? 0,
			];
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
		// Load products for the campaign's country
		$products = Product::where('country_id', $adsCampaign->country_id)
			->orderBy('name')
			->get();
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
			'products.*.leads' => 'nullable|integer|min:0',
		]);

		$adsCampaign->update([
			'platform_id' => $data['platform_id'],
			'country_id' => $data['country_id'],
			'date_from' => $data['date_from'],
			'date_to' => $data['date_to'],
		]);

		// Sync products with amount spent and leads
		$productsData = [];
		foreach ($data['products'] as $product) {
			$productsData[$product['id']] = [
				'amount_spent' => $product['amount_spent'],
				'leads' => $product['leads'] ?? 0,
			];
		}
		$adsCampaign->products()->sync($productsData);

		return redirect()->route('ads-campaigns.index')->with('status', 'Campaign updated.');
	}

	/**
	 * Import campaigns from an Excel/CSV file.
	 */
	public function import(Request $request, AdsCampaignImportService $importService)
	{
		$countryId = (int) $request->session()->get('current_country_id');

		if (! $countryId) {
			return back()->withErrors([
				'file' => __('Please select a country from the top-right dropdown before importing.'),
			]);
		}

		$request->validate([
			'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
		]);

		try {
			$rows = $importService->parse($request->file('file'));
		} catch (\InvalidArgumentException $e) {
			return back()->withErrors(['file' => $e->getMessage()]);
		} catch (\Throwable $e) {
			return back()->withErrors(['file' => __('Unable to read the uploaded file. Please check the format and try again.')]);
		}

		if (empty($rows)) {
			return back()->withErrors(['file' => __('No valid campaign rows were found in the file.')]);
		}

		$productsByImportId = Product::where('country_id', $countryId)
			->whereNotNull('import_id')
			->get()
			->keyBy(fn (Product $product) => strtolower(trim($product->import_id)));

		$totalAmountSpent = array_sum(array_column($rows, 'amount_spent'));
		$totalLeads = array_sum(array_column($rows, 'leads'));

		$campaign = AdsCampaign::create([
			'name' => __('Imported Campaign :date', ['date' => now()->format('Y-m-d H:i')]),
			'amount_spent' => $totalAmountSpent,
			'leads' => $totalLeads,
			'country_id' => $countryId,
			'platform_id' => null,
			'date_from' => null,
			'date_to' => null,
		]);

		$productsData = [];
		$unmatchedRows = [];

		foreach ($rows as $row) {
			$importId = AdsCampaignImportService::extractImportId($row['name']);
			$product = $importId
				? $productsByImportId->get(strtolower($importId))
				: null;

			if (! $product) {
				$unmatchedRows[] = $row['name'];
				continue;
			}

			$productId = $product->id;

			if (isset($productsData[$productId])) {
				$productsData[$productId]['amount_spent'] += $row['amount_spent'];
				$productsData[$productId]['leads'] += $row['leads'];
			} else {
				$productsData[$productId] = [
					'amount_spent' => $row['amount_spent'],
					'leads' => $row['leads'],
				];
			}
		}

		if (! empty($productsData)) {
			$campaign->products()->attach($productsData);
		}

		$matchedCount = count($productsData);

		if ($matchedCount === 0) {
			$campaign->delete();

			return back()->withErrors([
				'file' => __('No products could be matched. Make sure each product has an Import ID matching the code before the hyphen in the campaign name.'),
			]);
		}

		$statusMessage = __('1 campaign imported with :count product(s). Assign platform and date range to the selected row.', [
			'count' => $matchedCount,
		]);

		if (! empty($unmatchedRows)) {
			$statusMessage .= ' '.__(':count row(s) could not be matched to a product (check the import ID before the hyphen in the campaign name).', [
				'count' => count($unmatchedRows),
			]);
		}

		return redirect()
			->route('ads-campaigns.index', ['imported' => (string) $campaign->id])
			->with('status', $statusMessage);
	}

	/**
	 * Bulk update platform and/or date range for selected campaigns.
	 */
	public function bulkUpdate(Request $request)
	{
		$data = $request->validate([
			'campaign_ids' => 'required|array|min:1',
			'campaign_ids.*' => 'integer|exists:ads_campaigns,id',
			'platform_id' => 'nullable|exists:ads_platforms,id',
			'date_from' => 'nullable|date',
			'date_to' => 'nullable|date|after_or_equal:date_from',
		]);

		if (empty($data['platform_id']) && empty($data['date_from']) && empty($data['date_to'])) {
			return back()->withErrors([
				'bulk' => __('Please select a platform and/or date range to apply.'),
			]);
		}

		if (! empty($data['date_from']) xor ! empty($data['date_to'])) {
			return back()->withErrors([
				'bulk' => __('Please provide both start and end dates.'),
			]);
		}

		$updates = [];

		if (! empty($data['platform_id'])) {
			$updates['platform_id'] = $data['platform_id'];
		}

		if (! empty($data['date_from']) && ! empty($data['date_to'])) {
			$updates['date_from'] = $data['date_from'];
			$updates['date_to'] = $data['date_to'];
		}

		AdsCampaign::whereIn('id', $data['campaign_ids'])->update($updates);

		return redirect()
			->route('ads-campaigns.index', $request->only(['from', 'to', 'country_id']))
			->with('status', __(':count campaigns updated.', ['count' => count($data['campaign_ids'])]));
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

