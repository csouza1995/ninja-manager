<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expenditure extends Model
{
    protected $fillable = [
        'destination',
        'description',
        'classification',
        'bank_account_id',
        'due_date',
        'amount',
        'paid_at',
        'model_id',
        'model_type',
        'reference_date',
        'adjustment_amount',
        'adjustment_reason',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'reference_date' => 'date',
        'amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
