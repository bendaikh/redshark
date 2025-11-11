<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
	protected $fillable = [
		'name',
		'contact_email',
		'phone',
		'country_id',
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}
}

