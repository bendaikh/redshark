<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
		$lowStock = Product::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->whereColumn('quantity', '<=', 'low_stock_threshold')->get();
		$adsSpent = AdsCampaign::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->sum('amount_spent');
		$stockValue = Product::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->sum(\DB::raw('quantity * cost'));
		$profitPotential = Product::when($countryId, fn($q) => $q->where('country_id', $countryId))
			->sum(\DB::raw('quantity * (selling_price - cost)'));

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

