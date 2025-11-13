<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
	protected $fillable = [
		'invoice_id',
		'product_id',
		'quantity',
		'unit_cost',
		'total_cost',
		'revenue',
		'total_orders',
		'quantity_sold',
		'delivery_fee_id',
		'ads_cost',
		'net_profit',
	];

	protected $casts = [
		'unit_cost' => 'decimal:2',
		'total_cost' => 'decimal:2',
		'revenue' => 'decimal:2',
		'ads_cost' => 'decimal:2',
		'net_profit' => 'decimal:2',
	];

	public function deliveryFee()
	{
		return $this->belongsTo(\App\Models\DeliveryFee::class);
	}

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}

