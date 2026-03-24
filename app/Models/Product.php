<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
	protected $fillable = [
		'name',
		'image',
		'category_id',
		'quantity',
		'cost',
		'supplier_id',
		'country_id',
		'low_stock_threshold',
	];

	protected $casts = [
		'cost' => 'decimal:2',
	];

	protected $appends = [
		'average_cost',
	];

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function invoiceItems()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function sourcings()
	{
		return $this->hasMany(Sourcing::class);
	}

	public function adsCampaigns()
	{
		return $this->belongsToMany(AdsCampaign::class, 'ads_campaign_product')
			->withPivot('amount_spent', 'leads')
			->withTimestamps();
	}

	/**
	 * Get the media buyers assigned to this product.
	 */
	public function mediaBuyers()
	{
		return $this->belongsToMany(User::class, 'media_buyer_product', 'product_id', 'user_id')
			->withPivot('cost_total')
			->withTimestamps();
	}

	/**
	 * Calculate the true initial quantity from all validated sourcings
	 */
	public function getInitialQtyAttribute()
	{
		// Sum all quantities from validated sourcings
		$totalFromSourcings = $this->sourcings()->where('validated', true)->sum('quantity');
		
		// If there are validated sourcings, use that total; otherwise fall back to the product's quantity field
		return $totalFromSourcings > 0 ? $totalFromSourcings : $this->quantity;
	}

	public function getRemainingQtyAttribute()
	{
		$quantitySold = $this->invoiceItems()->sum('quantity_sold');
		return max(0, $this->initial_qty - ($quantitySold ?? 0));
	}

	public function getTotalAdsCostAttribute()
	{
		return $this->adsCampaigns()->sum('ads_campaign_product.amount_spent');
	}

	public function getTotalLeadsAttribute()
	{
		return $this->adsCampaigns()->sum('ads_campaign_product.leads');
	}

	public function getTotalOrdersAttribute()
	{
		return $this->invoiceItems()->sum('total_orders');
	}

	public function getDeliveryRateAttribute()
	{
		$totalLeads = $this->getTotalLeadsAttribute();
		if ($totalLeads == 0) {
			return 0;
		}
		$totalOrders = $this->getTotalOrdersAttribute();
		return ($totalOrders / $totalLeads) * 100;
	}

	public function getCostPerLeadAttribute()
	{
		$totalLeads = $this->getTotalLeadsAttribute();
		if ($totalLeads == 0) {
			return 0;
		}
		$totalAdsCost = $this->getTotalAdsCostAttribute();
		return $totalAdsCost / $totalLeads;
	}

	public function getCostPerDeliveredAttribute()
	{
		$totalOrders = $this->getTotalOrdersAttribute();
		if ($totalOrders == 0) {
			return 0;
		}
		$totalAdsCost = $this->getTotalAdsCostAttribute();
		return $totalAdsCost / $totalOrders;
	}

	public function getTotalRevenueAttribute()
	{
		return $this->invoiceItems()->sum('revenue');
	}

	public function getTotalAmountAttribute()
	{
		// Total Amount (Auto) = sum of (revenue - (total_orders × delivery_fee) - (quantity_sold × unit_cost)) for all invoice items
		$total = 0;
		$invoiceItems = $this->invoiceItems()->with('deliveryFee')->get();
		
		foreach ($invoiceItems as $item) {
			$deliveryFeePerUnit = $item->deliveryFee ? $item->deliveryFee->fee_per_unit : 0;
			$totalDeliveryFee = $item->total_orders * $deliveryFeePerUnit;
			$totalProductCost = $item->quantity_sold * $item->unit_cost;
			$totalAmount = $item->revenue - $totalDeliveryFee - $totalProductCost;
			$total += $totalAmount;
		}
		
		return $total;
	}

	public function getNetProfitAttribute()
	{
		$totalAmount = $this->getTotalAmountAttribute();
		$totalAdsCost = $this->getTotalAdsCostAttribute();
		return $totalAmount - $totalAdsCost;
	}

	/**
	 * Get total ads cost filtered by date range
	 */
	public function getFilteredAdsCost($dateFrom = null, $dateTo = null)
	{
		$query = $this->adsCampaigns();
		if ($dateFrom) {
			$query->whereDate('ads_campaigns.date_from', '>=', $dateFrom);
		}
		if ($dateTo) {
			$query->whereDate('ads_campaigns.date_to', '<=', $dateTo);
		}
		return $query->sum('ads_campaign_product.amount_spent');
	}

	/**
	 * Get total leads filtered by date range
	 */
	public function getFilteredLeads($dateFrom = null, $dateTo = null)
	{
		$query = $this->adsCampaigns();
		if ($dateFrom) {
			$query->whereDate('ads_campaigns.date_from', '>=', $dateFrom);
		}
		if ($dateTo) {
			$query->whereDate('ads_campaigns.date_to', '<=', $dateTo);
		}
		return $query->sum('ads_campaign_product.leads');
	}

	/**
	 * Get total orders filtered by date range
	 */
	public function getFilteredOrders($dateFrom = null, $dateTo = null)
	{
		$query = $this->invoiceItems()
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id');
		if ($dateFrom) {
			$query->whereDate('invoices.date', '>=', $dateFrom);
		}
		if ($dateTo) {
			$query->whereDate('invoices.date', '<=', $dateTo);
		}
		return $query->sum('invoice_items.total_orders');
	}

	/**
	 * Get delivery rate filtered by date range
	 */
	public function getFilteredDeliveryRate($dateFrom = null, $dateTo = null)
	{
		$totalLeads = $this->getFilteredLeads($dateFrom, $dateTo);
		if ($totalLeads == 0) {
			return 0;
		}
		$totalOrders = $this->getFilteredOrders($dateFrom, $dateTo);
		return ($totalOrders / $totalLeads) * 100;
	}

	/**
	 * Get cost per lead filtered by date range
	 */
	public function getFilteredCostPerLead($dateFrom = null, $dateTo = null)
	{
		$totalLeads = $this->getFilteredLeads($dateFrom, $dateTo);
		if ($totalLeads == 0) {
			return 0;
		}
		$totalAdsCost = $this->getFilteredAdsCost($dateFrom, $dateTo);
		return $totalAdsCost / $totalLeads;
	}

	/**
	 * Get cost per delivered filtered by date range
	 */
	public function getFilteredCostPerDelivered($dateFrom = null, $dateTo = null)
	{
		$totalOrders = $this->getFilteredOrders($dateFrom, $dateTo);
		if ($totalOrders == 0) {
			return 0;
		}
		$totalAdsCost = $this->getFilteredAdsCost($dateFrom, $dateTo);
		return $totalAdsCost / $totalOrders;
	}

	/**
	 * Get total amount filtered by date range
	 */
	public function getFilteredTotalAmount($dateFrom = null, $dateTo = null)
	{
		$query = $this->invoiceItems()
			->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
			->leftJoin('delivery_fees', 'invoice_items.delivery_fee_id', '=', 'delivery_fees.id');
		
		if ($dateFrom) {
			$query->whereDate('invoices.date', '>=', $dateFrom);
		}
		if ($dateTo) {
			$query->whereDate('invoices.date', '<=', $dateTo);
		}
		
		return $query->selectRaw('SUM(invoice_items.revenue - (invoice_items.total_orders * COALESCE(delivery_fees.fee_per_unit, 0)) - (invoice_items.quantity_sold * invoice_items.unit_cost)) as total')
			->value('total') ?? 0;
	}

	/**
	 * Get net profit filtered by date range
	 */
	public function getFilteredNetProfit($dateFrom = null, $dateTo = null)
	{
		$totalAmount = $this->getFilteredTotalAmount($dateFrom, $dateTo);
		$totalAdsCost = $this->getFilteredAdsCost($dateFrom, $dateTo);
		return $totalAmount - $totalAdsCost;
	}

	/**
	 * Calculate average cost total from all sourcings (including restocks)
	 * Formula: Total Final Price Total / Total Quantity
	 */
	public function getAverageCostAttribute()
	{
		$sourcings = $this->sourcings;
		
		if ($sourcings->count() == 0) {
			return $this->cost ?? 0; // Fallback to product's cost field if no sourcings
		}

		// Sum up all quantities and final price totals from sourcings
		$totalQuantity = 0;
		$totalFinalPriceTotal = 0;
		
		foreach ($sourcings as $sourcing) {
			$totalQuantity += $sourcing->quantity ?? 0;
			$totalFinalPriceTotal += $sourcing->final_price_total;
		}

		// Avoid division by zero
		if ($totalQuantity == 0) {
			return 0;
		}

		return $totalFinalPriceTotal / $totalQuantity;
	}

	/**
	 * Recalculate and sync the product's quantity field from all validated sourcings
	 */
	public function syncQuantityFromSourcings()
	{
		$totalQuantity = $this->sourcings()->where('validated', true)->sum('quantity');
		$this->quantity = $totalQuantity;
		$this->save();
	}
}

