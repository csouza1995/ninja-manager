<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outflow extends Model
{
    protected $fillable = [
        'type',
        'description',
        'origin_bank_account_id',
        'destination_bank_account_id',
        'person_name',
        'amount',
        'tax_percentage',
        'tax_amount',
        'due_date',
        'paid_at',
        'reference_date',
        'adjustment_amount',
        'adjustment_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
        'reference_date' => 'date',
        'adjustment_amount' => 'decimal:2',
    ];

    public function originBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'origin_bank_account_id');
    }

    public function destinationBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'destination_bank_account_id');
    }

    public function expenditure(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Expenditure::class, 'model');
    }
}
