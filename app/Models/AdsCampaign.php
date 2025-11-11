<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsCampaign extends Model
{
	protected $fillable = [
		'name',
		'platform',
		'amount_spent',
		'country_id',
		'date',
		'notes',
	];

	protected $casts = [
		'date' => 'date',
		'amount_spent' => 'decimal:2',
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}
}

