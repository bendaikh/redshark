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

