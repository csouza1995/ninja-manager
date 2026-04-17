<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Receipt extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'service_id',
        'receipt_number',
        'year',
        'sequence',
        'status',
        'is_signed',
        'is_sent',
    ];

    protected $casts = [
        'year' => 'integer',
        'sequence' => 'integer',
        'is_signed' => 'boolean',
        'is_sent' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate receipt number on creation
        static::creating(function ($receipt) {
            $year = now()->year;
            $yearShort = now()->format('y');

            // Get next sequence number for this year
            $sequence = static::whereYear('created_at', $year)
                ->max('sequence') + 1;

            $receipt->year = $year;
            $receipt->sequence = $sequence;
            $receipt->receipt_number = sprintf('%03d/%s', $sequence, $yearShort);
        });
    }

    // Relationships
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Media Library Configuration
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipts')
            ->useDisk('public')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('signed_receipts')
            ->useDisk('public')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    // Helpers
    public function getReceiptNumberSlugAttribute(): string
    {
        return str_replace('/', '-', $this->receipt_number);
    }

    public function getPdfUrl(): ?string
    {
        return $this->getFirstMediaUrl('receipts');
    }

    public function hasPdf(): bool
    {
        return $this->hasMedia('receipts');
    }

    public function getSignedPdfUrl(): ?string
    {
        return $this->getFirstMediaUrl('signed_receipts');
    }

    public function hasSignedPdf(): bool
    {
        return $this->hasMedia('signed_receipts');
    }
}
