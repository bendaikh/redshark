<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $fillable = [
		'name',
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
}

