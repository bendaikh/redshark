<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $fillable = [
		'name',
		'active',
	];

	public function users()
	{
		return $this->belongsToMany(User::class)->withTimestamps();
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}

	public function adsCampaigns()
	{
		return $this->hasMany(AdsCampaign::class);
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}
}

