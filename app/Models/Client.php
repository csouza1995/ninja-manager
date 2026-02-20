<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'nickname',
        'document',
        'street',
        'number',
        'complement',
        'zip_code',
        'neighborhood',
        'city',
        'state',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->number ? "nº {$this->number}" : null,
            $this->complement,
            $this->neighborhood,
            $this->city,
            $this->state,
            $this->zip_code ? "CEP: {$this->zip_code}" : null,
        ]);

        return implode(', ', $parts);
    }

    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    public function isCompany(): bool
    {
        return $this->type === 'company';
    }
}
