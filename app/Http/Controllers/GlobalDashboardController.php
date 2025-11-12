<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\AdsCampaign;

class GlobalDashboardController extends Controller
{
	public function index(Request $request)
	{
		$countryId = (int) $request->session()->get('current_country_id');
		$from = $request->input('from');
		$to = $request->input('to');
		$isGlobal = $countryId === 0;

		// If not global, show only selected country
		$totalCountries = $isGlobal ? Country::count() : Country::where('id', $countryId)->count();
		$totalInvoices = Invoice::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->count();
		// Calculate ads spent from pivot table
		$adsSpentQuery = DB::table('ads_campaign_product')
			->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('ads_campaigns.date_from', '>=', $from))
			->when($to, fn($q) => $q->whereDate('ads_campaigns.date_to', '<=', $to));
		
		$totalExpenses = ($adsSpentQuery->sum('ads_campaign_product.amount_spent') ?: 0)
			+ Invoice::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
				->when($from, fn($q) => $q->whereDate('date', '>=', $from))
				->when($to, fn($q) => $q->whereDate('date', '<=', $to))
				->sum('total_amount');
		$totalStockValue = Product::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->sum(\DB::raw('quantity * cost'));

		$byCountry = Country::when(!$isGlobal && $countryId, fn($q) => $q->where('id', $countryId))
			->withCount([
				// Sum of invoice totals in range
				'invoices as total_invoices_amount' => function ($q) use ($from, $to) {
					$q->when($from, fn($qq) => $qq->whereDate('date', '>=', $from))
					  ->when($to, fn($qq) => $qq->whereDate('date', '<=', $to))
					  ->select(\DB::raw('coalesce(sum(total_amount),0)'));
				},
				// Count of invoices in range
				'invoices as invoices_count' => function ($q) use ($from, $to) {
					$q->when($from, fn($qq) => $qq->whereDate('date', '>=', $from))
					  ->when($to, fn($qq) => $qq->whereDate('date', '<=', $to));
				},
				// Products count
				'products',
				// Stock value
				'products as stock_value' => function ($q) {
					$q->select(\DB::raw('coalesce(sum(quantity * cost),0)'));
				},
			])
			->with(['adsCampaigns' => function ($q) use ($from, $to) {
				$q->when($from, fn($qq) => $qq->whereDate('date_from', '>=', $from))
				  ->when($to, fn($qq) => $qq->whereDate('date_to', '<=', $to))
				  ->with('products');
			}])
			->get()
			->map(function ($country) {
				$country->ads_spent = $country->adsCampaigns->sum('total_amount_spent');
				return $country;
			});

		return view('dashboards.global', [
			'totalCountries' => $totalCountries,
			'totalInvoices' => $totalInvoices,
			'totalExpenses' => $totalExpenses,
			'totalStockValue' => $totalStockValue,
			'byCountry' => $byCountry,
		]);
	}
}

