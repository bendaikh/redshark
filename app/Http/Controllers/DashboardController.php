<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Balance;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $countryId = (int) $request->session()->get('current_country_id');
        
        // Calculate total balance (sum of all balance entries)
        $totalBalance = Balance::when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->sum('amount') ?? 0;
        
        // Calculate total expenses (sum of all expense entries)
        $totalExpenses = Expense::when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->sum('amount') ?? 0;
        
        // Calculate net profit balance (total balance - all expenses)
        $netProfitBalance = $totalBalance - $totalExpenses;

        // Ads & sales performance metrics
        $adsStatsBaseQuery = DB::table('ads_campaign_product')
            ->join('ads_campaigns', 'ads_campaign_product.ads_campaign_id', '=', 'ads_campaigns.id')
            ->when($countryId, fn($q) => $q->where('ads_campaigns.country_id', $countryId));

        $totalLeads = (clone $adsStatsBaseQuery)->sum('ads_campaign_product.leads') ?? 0;
        $totalAdsSpend = (clone $adsStatsBaseQuery)->sum('ads_campaign_product.amount_spent') ?? 0;

        $totalOrders = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->when($countryId, fn($q) => $q->where('invoices.country_id', $countryId))
            ->sum('invoice_items.total_orders') ?? 0;

        $costPerLead = $totalLeads > 0 ? $totalAdsSpend / $totalLeads : null;
        $costPerDelivered = $totalOrders > 0 ? $totalAdsSpend / $totalOrders : null;
        $deliveryRate = $totalLeads > 0 ? ($totalOrders / $totalLeads) * 100 : null;

        $totalSpendByPlatform = (clone $adsStatsBaseQuery)
            ->join('ads_platforms', 'ads_campaigns.platform_id', '=', 'ads_platforms.id')
            ->select('ads_platforms.name as platform', DB::raw('SUM(ads_campaign_product.amount_spent) as total_spend'))
            ->groupBy('ads_platforms.name')
            ->orderByDesc('total_spend')
            ->get();
        
        return view('dashboard', compact(
            'totalBalance',
            'netProfitBalance',
            'totalExpenses',
            'totalLeads',
            'totalAdsSpend',
            'totalOrders',
            'costPerLead',
            'costPerDelivered',
            'deliveryRate',
            'totalSpendByPlatform'
        ));
    }
}
