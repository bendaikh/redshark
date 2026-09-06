<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdsCampaign extends Model
{
	protected $fillable = [
		'user_id',
		'name',
		'amount_spent',
		'leads',
		'platform_id',
		'country_id',
		'date_from',
		'date_to',
		'notes',
	];

	protected $casts = [
		'date_from' => 'date',
		'date_to' => 'date',
		'amount_spent' => 'decimal:2',
		'leads' => 'integer',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

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
			->withPivot('amount_spent', 'leads')
			->withTimestamps();
	}

	public function getTotalAmountSpentAttribute()
	{
		$pivotTotal = 0;

		if ($this->relationLoaded('products')) {
			$pivotTotal = $this->products->sum(function ($product) {
				return $product->pivot->amount_spent ?? 0;
			});
		} else {
			$pivotTotal = DB::table('ads_campaign_product')
				->where('ads_campaign_id', $this->id)
				->sum('amount_spent') ?: 0;
		}

		if ($pivotTotal > 0) {
			return $pivotTotal;
		}

		return $this->amount_spent ?? 0;
	}

	public function getTotalLeadsAttribute()
	{
		$pivotTotal = 0;

		if ($this->relationLoaded('products')) {
			$pivotTotal = $this->products->sum(function ($product) {
				return $product->pivot->leads ?? 0;
			});
		} else {
			$pivotTotal = DB::table('ads_campaign_product')
				->where('ads_campaign_id', $this->id)
				->sum('leads') ?: 0;
		}

		if ($pivotTotal > 0) {
			return $pivotTotal;
		}

		return $this->leads ?? 0;
	}
}

