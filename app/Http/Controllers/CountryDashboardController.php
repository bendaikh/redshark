<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\AdsCampaign;
use App\Models\Country;

class CountryDashboardController extends Controller
{
	public function index(Request $request)
	{
		$countryId = (int) $request->session()->get('current_country_id');
		$country = $countryId ? Country::find($countryId) : null;
		$from = $request->input('from');
		$to = $request->input('to');

		$totalInvoices = Invoice::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->count();
		$totalSales = 0;
		$totalStockQty = Product::when($countryId, fn($q) => $q->where('country_id', $countryId))->sum('quantity');
		$lowStock = Product::with('category')
			->when($countryId, fn($q) => $q->where('country_id', $countryId))
			->whereColumn('quantity', '<=', 'low_stock_threshold')->get();
		// Calculate ads spent from pivot table
		$adsSpent = DB::table('ads_campaign_product')
			->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
			->when($countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('ads_campaigns.date_from', '>=', $from))
			->when($to, fn($q) => $q->whereDate('ads_campaigns.date_to', '<=', $to))
			->sum('ads_campaign_product.amount_spent') ?: 0;
		$stockValue = Product::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->sum(DB::raw('quantity * cost'));
		$profitPotential = 0; // Profit potential calculation removed as selling_price is no longer used

		return view('dashboards.country', compact(
			'country',
			'totalInvoices',
			'totalSales',
			'totalStockQty',
			'lowStock',
			'adsSpent',
			'stockValue',
			'profitPotential'
		));
	}
}

