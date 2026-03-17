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
        'tax_amount',
        'net_amount',
        'issued_at',
        'competence_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'competence_date' => 'date',
    ];

    public function revenues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function expenditure(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Expenditure::class, 'model');
    }
}
