<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    protected $fillable = [
        'code',
        'description',
        'internal_description',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
    ];
}
