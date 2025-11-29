<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, HasRoles;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	public function countries()
	{
		return $this->belongsToMany(Country::class)->withTimestamps();
	}

	/**
	 * Get the testing products assigned to this user (if media buyer).
	 */
	public function testingProducts()
	{
		return $this->belongsToMany(TestingProduct::class, 'media_buyer_testing_product', 'user_id', 'testing_product_id')
			->withPivot('status', 'leads', 'ads_spend')
			->withTimestamps();
	}

	/**
	 * Get the products assigned to this user (if media buyer).
	 */
	public function products()
	{
		return $this->belongsToMany(Product::class, 'media_buyer_product', 'user_id', 'product_id')
			->withPivot('cost_total')
			->withTimestamps();
	}

	/**
	 * Get the countries accessible to this media buyer based on their assigned products.
	 */
	public function getAccessibleCountries()
	{
		if ($this->hasRole('superadmin')) {
			return Country::where('name', '!=', 'Global')->orderBy('name')->get();
		}

		if ($this->hasRole('media_buyer')) {
			// Get distinct countries from products assigned to this media buyer
			return Country::whereIn('id', function($query) {
				$query->select('country_id')
					->from('products')
					->whereIn('id', function($subQuery) {
						$subQuery->select('product_id')
							->from('media_buyer_product')
							->where('user_id', $this->id);
					});
			})
			->where('name', '!=', 'Global')
			->orderBy('name')
			->get();
		}

		// For other roles, return empty collection
		return collect();
	}
}

