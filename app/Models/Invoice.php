<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'number',
        'access_key',
        'amount',
        'issued_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'date',
    ];

    public function revenue(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Revenue::class);
    }
}
