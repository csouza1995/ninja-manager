<?php

namespace App\Models;

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
        'invoice_id',
        'tax_percentage',
        'tax_amount',
        'net_amount',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'gross_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

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
}
