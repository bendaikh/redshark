<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdsCampaign extends Model
{
	protected $fillable = [
		'name',
		'platform_id',
		'country_id',
		'date_from',
		'date_to',
		'notes',
	];

	protected $casts = [
		'date_from' => 'date',
		'date_to' => 'date',
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function platform()
	{
		return $this->belongsTo(AdsPlatform::class);
	}

	public function products()
	{
		return $this->belongsToMany(Product::class, 'ads_campaign_product')
			->withPivot('amount_spent')
			->withTimestamps();
	}

	public function getTotalAmountSpentAttribute()
	{
		if ($this->relationLoaded('products')) {
			return $this->products->sum(function($product) {
				return $product->pivot->amount_spent ?? 0;
			});
		}
		return DB::table('ads_campaign_product')
			->where('ads_campaign_id', $this->id)
			->sum('amount_spent') ?: 0;
	}
}

