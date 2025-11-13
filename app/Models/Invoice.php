<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $fillable = [
		'supplier_id',
		'invoice_number',
		'total_amount',
		'currency',
		'date',
		'date_from',
		'date_to',
		'country_id',
		'attachment_path',
	];

	protected $casts = [
		'date' => 'date',
		'date_from' => 'date',
		'date_to' => 'date',
		'total_amount' => 'decimal:2',
	];

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function items()
	{
		return $this->hasMany(InvoiceItem::class);
	}
}

