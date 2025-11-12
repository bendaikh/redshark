<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsPlatform extends Model
{
	protected $fillable = [
		'name',
		'description',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function adsItems()
	{
		return $this->hasMany(AdsItem::class);
	}
}
