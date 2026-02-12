<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'owner_name',
        'nickname',
    ];

    public function getNameAttribute(): string
    {
        return $this->nickname ?: $this->bank_name;
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function expenditures(): HasMany
    {
        return $this->hasMany(Expenditure::class);
    }
}
