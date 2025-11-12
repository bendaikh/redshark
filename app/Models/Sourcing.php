<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sourcing extends Model
{
	protected $fillable = [
		'product_id',
		'shipping_cost',
		'shipping_method',
		'sourcing_date',
		'notes',
	];

	protected $casts = [
		'shipping_cost' => 'decimal:2',
		'sourcing_date' => 'date',
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}

