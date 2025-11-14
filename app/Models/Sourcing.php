<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sourcing extends Model
{
	protected $fillable = [
		'product_name',
		'product_image',
		'category_id',
		'quantity',
		'country_id',
		'price',
		'cost',
		'shipping_type',
		'additional_fees',
		'testing_fees',
		'supplier_id',
		'shipping_cost',
		'shipping_method',
		'sourcing_date',
		'notes',
		'validated',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'cost' => 'decimal:2',
		'shipping_cost' => 'decimal:2',
		'additional_fees' => 'decimal:2',
		'testing_fees' => 'decimal:2',
		'sourcing_date' => 'date',
		'validated' => 'boolean',
	];

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	/**
	 * Calculate Cost Total: (Price Total + Additional Fees + Testing Fees + Shipping Cost) / Quantity
	 */
	public function getCostTotalAttribute()
	{
		$priceTotal = $this->cost ?? 0; // Price Total
		$additionalFees = $this->additional_fees ?? 0;
		$testingFees = $this->testing_fees ?? 0;
		$shippingCost = $this->shipping_cost ?? 0;
		$quantity = $this->quantity ?? 1;

		if ($quantity == 0) {
			return 0;
		}

		return ($priceTotal + $additionalFees + $testingFees + $shippingCost) / $quantity;
	}
}

