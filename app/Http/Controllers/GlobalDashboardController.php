<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
		$totalExpenses = AdsCampaign::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->sum('amount_spent')
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
				// Sum of ads spent in range
				'adsCampaigns as ads_spent' => function ($q) use ($from, $to) {
					$q->when($from, fn($qq) => $qq->whereDate('date', '>=', $from))
					  ->when($to, fn($qq) => $qq->whereDate('date', '<=', $to))
					  ->select(\DB::raw('coalesce(sum(amount_spent),0)'));
				},
				// Products count
				'products',
				// Stock value
				'products as stock_value' => function ($q) {
					$q->select(\DB::raw('coalesce(sum(quantity * cost),0)'));
				},
			])->get();

		return view('dashboards.global', [
			'totalCountries' => $totalCountries,
			'totalInvoices' => $totalInvoices,
			'totalExpenses' => $totalExpenses,
			'totalStockValue' => $totalStockValue,
			'byCountry' => $byCountry,
		]);
	}
}

