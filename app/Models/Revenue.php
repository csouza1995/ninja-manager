<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends Model
{
    protected $fillable = [
        'service_id',
        'origin_name',
        'description',
        'classification',
        'bank_account_id',
        'due_date',
        'gross_amount',
        'tax_percentage',
        'invoice_id',
        'paid_at',
        'notes',
        'linkable_type',
        'linkable_id',
        'adjustment_amount',
        'adjustment_reason',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'gross_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
    ];

    /**
     * Computed attribute for tax amount
     */
    protected function taxAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->gross_amount * ((float) $this->tax_percentage / 100),
        );
    }

    /**
     * Computed attribute for net amount (including adjustment)
     */
    protected function netAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => ((float) $this->gross_amount + (float) ($this->adjustment_amount ?? 0)) - (float) $this->tax_amount,
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expenditure(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Expenditure::class, 'model');
    }

    public function linkable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
