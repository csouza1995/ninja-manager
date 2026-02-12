<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceItemPivot extends Model
{
    protected $table = 'service_items_pivot';

    protected $fillable = [
        'service_id',
        'service_item_id',
        'code',
        'description',
        'unit_price',
        'quantity',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-calculate total_price before saving
        static::saving(function ($item) {
            $item->total_price = $item->unit_price * $item->quantity;
        });
    }

    // Relationships
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceItem(): BelongsTo
    {
        return $this->belongsTo(ServiceItem::class);
    }

    // Helper to create from ServiceItem
    public static function createFromServiceItem(Service $service, ServiceItem $serviceItem, float $quantity = 1): self
    {
        return static::create([
            'service_id' => $service->id,
            'service_item_id' => $serviceItem->id,
            'code' => $serviceItem->code,
            'description' => $serviceItem->description,
            'unit_price' => $serviceItem->unit_price,
            'quantity' => $quantity,
        ]);
    }
}
