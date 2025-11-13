<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryFee extends Model
{
    protected $fillable = [
        'name',
        'fee_per_unit',
        'active',
    ];

    protected $casts = [
        'fee_per_unit' => 'decimal:2',
        'active' => 'boolean',
    ];
}
