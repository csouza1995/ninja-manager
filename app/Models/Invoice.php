<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'service_id',
        'number',
        'access_key',
        'xml_path',
        'pdf_path',
        'description',
        'ctn',
        'ctm',
        'amount',
        'tax_rate',
        'issued_at',
        'competence_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'issued_at' => 'datetime',
        'competence_date' => 'date',
    ];

    public function revenue(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Revenue::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
