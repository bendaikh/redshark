<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\AdsCampaign;
use App\Models\Sourcing;
use App\Models\Balance;
use App\Models\Expense;

class GlobalDashboardController extends Controller
{
	public function index(Request $request)
	{
		$countryId = (int) $request->session()->get('current_country_id');
		$from = $request->input('from');
		$to = $request->input('to');
		$productId = $request->input('product_id');
		// Separate date filters for Marketing Performance section
		$marketingFrom = $request->input('marketing_from', $from);
		$marketingTo = $request->input('marketing_to', $to);
		$isGlobal = $countryId === 0;

		// If not global, show only selected country
		$totalCountries = $isGlobal ? Country::count() : Country::where('id', $countryId)->count();
		$totalInvoices = Invoice::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->count();
		// Calculate ads and marketing stats from pivot table (clone query for reuse)
		// Uses marketing-specific date filters for Marketing Performance section
		$adsStatsBaseQuery = DB::table('ads_campaign_product')
			->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
			->when($productId, fn($q) => $q->where('ads_campaign_product.product_id', $productId));
		
		// Apply marketing-specific date filters if provided
		if ($marketingFrom) {
			$adsStatsBaseQuery->whereDate('ads_campaigns.date_from', '>=', $marketingFrom);
		}
		if ($marketingTo) {
			$adsStatsBaseQuery->whereDate('ads_campaigns.date_to', '<=', $marketingTo);
		}

		$totalAdsSpent = (clone $adsStatsBaseQuery)->sum('ads_campaign_product.amount_spent') ?: 0;
		$totalLeads = (clone $adsStatsBaseQuery)->sum('ads_campaign_product.leads') ?? 0;

	$totalExpenses = $totalAdsSpent
		+ Invoice::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->sum('total_amount');
	// Calculate stock value using average cost from sourcings
	$products = Product::with('sourcings')
		->when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
		->get();
	$totalStockValue = $products->sum(function ($product) {
		return $product->quantity * $product->average_cost;
	});

		// Marketing KPIs
		$ordersQuery = DB::table('invoice_items')
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('invoices.country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('invoices.date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('invoices.date', '<=', $to))
			->when($productId, fn($q) => $q->where('invoice_items.product_id', $productId));

		$totalOrders = (clone $ordersQuery)->sum('invoice_items.total_orders') ?? 0;

		$costPerLead = $totalLeads > 0 ? $totalAdsSpent / $totalLeads : null;
		$costPerDelivered = $totalOrders > 0 ? $totalAdsSpent / $totalOrders : null;
		$deliveryRate = $totalLeads > 0 ? ($totalOrders / $totalLeads) * 100 : null;

		$totalSpendByPlatform = (clone $adsStatsBaseQuery)
			->join('ads_platforms', 'ads_campaigns.platform_id', '=', 'ads_platforms.id')
			->select('ads_platforms.name as platform', DB::raw('SUM(ads_campaign_product.amount_spent) as total_spend'))
			->groupBy('ads_platforms.name')
			->orderByDesc('total_spend')
			->get();
		
		// Get selected product name if filtering by product
		$selectedProduct = $productId ? Product::find($productId) : null;

		// Calculate total profits (sum of all products' net_profit)
		$products = Product::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))->get();
		$totalProfits = $products->sum(function($product) {
			return $product->net_profit;
		});

		// Get profitable products (net_profit > 0)
		$profitableProducts = $products->filter(function($product) {
			return $product->net_profit > 0;
		})->sortByDesc('net_profit')->take(10);

		// Stock management
		$totalStockQty = Product::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))->sum('quantity');
		$lowStockProducts = Product::with('category')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->whereColumn('quantity', '<=', 'low_stock_threshold')
			->get();

		// Business KPIs - Initial Quantity (total from all validated sourcings)
		$initialQuantity = Sourcing::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->where('validated', true)
			->sum('quantity');

		// Sold Quantity (total from all invoice items)
		$soldQuantity = DB::table('invoice_items')
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('invoices.country_id', $countryId))
			->sum('invoice_items.quantity_sold') ?? 0;

		// Sold Rate (sold quantity / initial quantity) as percentage
		$soldRate = $initialQuantity > 0 ? ($soldQuantity / $initialQuantity) * 100 : 0;

		// Stock Recovery Sold - The cost value of items sold (quantity_sold × unit_cost)
		// This shows how much of the Total Stock Value has been sold
		$stockRecoverySold = DB::table('invoice_items')
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('invoices.country_id', $countryId))
			->selectRaw('SUM(invoice_items.quantity_sold * invoice_items.unit_cost) as total')
			->value('total') ?? 0;

		// Sourcing management
		$totalSourcings = Sourcing::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))->count();
		$validatedSourcings = Sourcing::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->where('validated', true)->count();
		$pendingSourcings = $totalSourcings - $validatedSourcings;

		// Ads management
		$totalAdsCampaigns = AdsCampaign::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date_from', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date_to', '<=', $to))
			->count();

		// Accounting data
		$accountingBalance = Balance::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->sum('amount') ?? 0;
		
		$accountingExpenses = Expense::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->when($from, fn($q) => $q->whereDate('date', '>=', $from))
			->when($to, fn($q) => $q->whereDate('date', '<=', $to))
			->sum('amount') ?? 0;
		
		$accountingNetProfit = $accountingBalance - $accountingExpenses;

		// Revenue trends by day (last 30 days or filtered range) - Using Accounting Balances
		$revenueTrendsFrom = $from ?: now()->subDays(30)->format('Y-m-d');
		$revenueTrendsTo = $to ?: now()->format('Y-m-d');
		
		$revenueTrends = DB::table('balances')
			->when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->select(
				DB::raw('DATE(date) as date'),
				DB::raw('SUM(amount) as total_revenue')
			)
			->whereBetween('date', [$revenueTrendsFrom, $revenueTrendsTo])
			->groupBy(DB::raw('DATE(date)'))
			->orderBy('date', 'asc')
			->get();

		// Prepare chart data
		$chartDates = $revenueTrends->pluck('date')->map(function($date) {
			return date('M d', strtotime($date));
		})->toArray();
		
		$chartRevenues = $revenueTrends->pluck('total_revenue')->toArray();

		// Chart data - Time-based metrics (always generate, use all-time if no filters)
		$chartData = [];
		$minDate = Invoice::min('date');
		$maxDate = Invoice::max('date');
		$chartFrom = $from ?: ($minDate ?: now()->subMonths(6)->format('Y-m-d'));
		$chartTo = $to ?: ($maxDate ?: now()->format('Y-m-d'));
		
		// Always try to generate chart data, even if empty
		if ($chartFrom && $chartTo) {
			// Get invoice items with calculated total amounts (matching Product model logic exactly)
			$invoiceItems = DB::table('invoice_items')
				->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
				->leftJoin('delivery_fees', 'invoice_items.delivery_fee_id', '=', 'delivery_fees.id')
				->when(!$isGlobal && $countryId, fn($q) => $q->where('invoices.country_id', $countryId))
				->whereBetween('invoices.date', [$chartFrom, $chartTo])
				->selectRaw('DATE_FORMAT(invoices.date, "%Y-%m") as month,
					invoice_items.product_id,
					invoice_items.revenue,
					invoice_items.total_orders,
					invoice_items.quantity_sold,
					invoice_items.unit_cost,
					COALESCE(delivery_fees.fee_per_unit, 0) as delivery_fee')
				->get();

			// Calculate total amount per month (Total Amount Auto = revenue - (total_orders × delivery_fee) - (quantity_sold × unit_cost))
			// This matches Product::getTotalAmountAttribute() exactly
			$profitsByMonth = [];
			foreach ($invoiceItems as $item) {
				$deliveryFeePerUnit = $item->delivery_fee;
				$totalDeliveryFee = $item->total_orders * $deliveryFeePerUnit;
				$totalProductCost = $item->quantity_sold * $item->unit_cost;
				$totalAmount = $item->revenue - $totalDeliveryFee - $totalProductCost;
				
				if (!isset($profitsByMonth[$item->month])) {
					$profitsByMonth[$item->month] = 0;
				}
				$profitsByMonth[$item->month] += $totalAmount;
			}

			// Get ads costs per month - allocate proportionally based on product revenue per month
			// This ensures ads costs are matched to the months when revenue was generated
			$adsByMonth = [];
			
			// Get all products with their total ads costs
			$productsWithAds = DB::table('ads_campaign_product')
				->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
				->when(!$isGlobal && $countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
				->selectRaw('ads_campaign_product.product_id, SUM(ads_campaign_product.amount_spent) as total_ads_cost')
				->groupBy('ads_campaign_product.product_id')
				->get();
			
			// For each product, allocate its ads cost to months based on revenue proportion
			foreach ($productsWithAds as $productAds) {
				// Get revenue per month for this product
				$productRevenueByMonth = DB::table('invoice_items')
					->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
					->when(!$isGlobal && $countryId, fn($q) => $q->where('invoices.country_id', $countryId))
					->where('invoice_items.product_id', $productAds->product_id)
					->whereBetween('invoices.date', [$chartFrom, $chartTo])
					->selectRaw('DATE_FORMAT(invoices.date, "%Y-%m") as month, SUM(invoice_items.revenue) as revenue')
					->groupBy('month')
					->get();
				
				$totalRevenue = $productRevenueByMonth->sum('revenue');
				
				if ($totalRevenue > 0) {
					// Allocate ads cost proportionally based on revenue
					foreach ($productRevenueByMonth as $monthRevenue) {
						$proportion = $monthRevenue->revenue / $totalRevenue;
						$allocatedAdsCost = $productAds->total_ads_cost * $proportion;
						
						if (!isset($adsByMonth[$monthRevenue->month])) {
							$adsByMonth[$monthRevenue->month] = 0;
						}
						$adsByMonth[$monthRevenue->month] += $allocatedAdsCost;
					}
				} else {
					// If no revenue, allocate ads cost to campaign start month
					$campaignMonths = DB::table('ads_campaign_product')
						->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
						->when(!$isGlobal && $countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
						->where('ads_campaign_product.product_id', $productAds->product_id)
						->whereBetween('ads_campaigns.date_from', [$chartFrom, $chartTo])
						->selectRaw('DATE_FORMAT(ads_campaigns.date_from, "%Y-%m") as month, SUM(ads_campaign_product.amount_spent) as ads_cost')
						->groupBy('month')
						->get();
					
					foreach ($campaignMonths as $campaignMonth) {
						if (!isset($adsByMonth[$campaignMonth->month])) {
							$adsByMonth[$campaignMonth->month] = 0;
						}
						$adsByMonth[$campaignMonth->month] += $campaignMonth->ads_cost;
					}
				}
			}
			
			// Get all unique months (from both invoices and ads)
			$allMonths = array_unique(array_merge(array_keys($profitsByMonth), array_keys($adsByMonth)));
			sort($allMonths);

			// Combine to get net profit per month (Total Amount - Ads Cost for that month)
			// This matches Product::getNetProfitAttribute() = Total Amount - Total Ads Cost
			$profitsChart = collect($allMonths)->map(function($month) use ($profitsByMonth, $adsByMonth) {
				$totalAmount = isset($profitsByMonth[$month]) ? $profitsByMonth[$month] : 0;
				$adsCost = isset($adsByMonth[$month]) ? $adsByMonth[$month] : 0;
				$netProfit = $totalAmount - $adsCost;
				return (object)[
					'month' => $month,
					'total_amount' => $totalAmount,
					'ads_cost' => $adsCost,
					'net_profit' => $netProfit
				];
			})->values();

			// Ads spending over time
			$adsChart = DB::table('ads_campaign_product')
				->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
				->when(!$isGlobal && $countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId))
				->whereBetween('ads_campaigns.date_from', [$chartFrom, $chartTo])
				->selectRaw('DATE_FORMAT(ads_campaigns.date_from, "%Y-%m") as month, SUM(ads_campaign_product.amount_spent) as spent')
				->groupBy('month')
				->orderBy('month')
				->get();

			// Invoices over time
			$invoicesChart = Invoice::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
				->whereBetween('date', [$chartFrom, $chartTo])
				->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as total')
				->groupBy('month')
				->orderBy('month')
				->get();

			$chartData = [
				'profits' => $profitsChart,
				'ads' => $adsChart,
				'invoices' => $invoicesChart,
			];
		}

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

		// Get all products for the filter dropdown
		$allProducts = Product::when(!$isGlobal && $countryId, fn($q) => $q->where('country_id', $countryId))
			->orderBy('name')
			->get(['id', 'name']);

		return view('dashboards.global', [
			'totalCountries' => $totalCountries,
			'totalInvoices' => $totalInvoices,
			'totalExpenses' => $totalExpenses,
			'totalStockValue' => $totalStockValue,
			'byCountry' => $byCountry,
			'totalProfits' => $totalProfits,
			'totalAdsSpent' => $totalAdsSpent,
			'totalLeads' => $totalLeads,
			'totalOrders' => $totalOrders,
			'costPerLead' => $costPerLead,
			'costPerDelivered' => $costPerDelivered,
			'deliveryRate' => $deliveryRate,
			'totalSpendByPlatform' => $totalSpendByPlatform,
			'profitableProducts' => $profitableProducts,
			'totalStockQty' => $totalStockQty,
			'lowStockProducts' => $lowStockProducts,
			'totalSourcings' => $totalSourcings,
			'validatedSourcings' => $validatedSourcings,
			'pendingSourcings' => $pendingSourcings,
			'totalAdsCampaigns' => $totalAdsCampaigns,
			'chartData' => $chartData,
			'accountingBalance' => $accountingBalance,
			'accountingExpenses' => $accountingExpenses,
			'accountingNetProfit' => $accountingNetProfit,
			'chartDates' => $chartDates,
			'chartRevenues' => $chartRevenues,
			'from' => $from,
			'to' => $to,
			'marketingFrom' => $marketingFrom,
			'marketingTo' => $marketingTo,
			'allProducts' => $allProducts,
			'productId' => $productId,
			'selectedProduct' => $selectedProduct,
			// New Business KPIs
			'initialQuantity' => $initialQuantity,
			'soldQuantity' => $soldQuantity,
			'soldRate' => $soldRate,
			'stockRecoverySold' => $stockRecoverySold,
		]);
	}
}

