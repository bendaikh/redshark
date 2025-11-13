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

	public function getRemainingQtyAttribute()
	{
		$quantitySold = $this->invoiceItems()->sum('quantity_sold');
		return max(0, $this->quantity - ($quantitySold ?? 0));
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
		$totalOrders = $this->getTotalOrdersAttribute();
		if ($totalOrders == 0) {
			return 0;
		}
		$totalLeads = $this->getTotalLeadsAttribute();
		return ($totalLeads / $totalOrders) * 100;
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
}

