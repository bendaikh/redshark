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
	];

	protected $casts = [
		'unit_cost' => 'decimal:2',
		'total_cost' => 'decimal:2',
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}

