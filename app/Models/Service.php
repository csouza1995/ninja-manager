<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'client_id',
        'executor_id',
        'role_id',
        'status',
        'is_paid',
        'is_documented',
        'is_invoiced',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_documented' => 'boolean',
        'is_invoiced' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(Executor::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceItemPivot::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function revenue(): HasOne
    {
        return $this->hasOne(Revenue::class);
    }

    // Computed Properties
    public function getTotalAttribute(): float
    {
        return $this->items->sum('total_price');
    }

    // Business Logic
    public function canEdit(): bool
    {
        return !($this->status === 'finalized' || $this->is_invoiced);
    }

    public function canDelete(): bool
    {
        return $this->status === 'negotiating' && !$this->is_invoiced && !$this->is_documented;
    }

    // Status Helpers
    public function markAsPaid(): void
    {
        $this->update(['is_paid' => true]);
    }

    public function markAsDocumented(): void
    {
        $this->update(['is_documented' => true]);
    }

    public function markAsInvoiced(): void
    {
        $this->update(['is_invoiced' => true]);
    }
}
