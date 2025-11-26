<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestingProduct extends Model
{
    protected $fillable = [
        'product_name',
        'product_link',
        'facebook_library_link',
        'country_ids',
        'video_url',
    ];

    protected $casts = [
        'country_ids' => 'array',
    ];

    /**
     * Get the countries associated with this testing product.
     */
    public function countries()
    {
        if (empty($this->country_ids)) {
            return collect();
        }
        return Country::whereIn('id', $this->country_ids)->get();
    }

    /**
     * Get the media buyers assigned to this testing product.
     */
    public function mediaBuyers()
    {
        return $this->belongsToMany(User::class, 'media_buyer_testing_product', 'testing_product_id', 'user_id')
            ->withPivot('status', 'leads', 'ads_spend')
            ->withTimestamps();
    }
}
