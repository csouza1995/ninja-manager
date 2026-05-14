<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'owner_name',
        'nickname',
        'opening_balance',
        'opening_balance_date',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
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

    public function calculateBalance(?string $limitDate = null): float
    {
        $limitDate ??= now()->endOfDay()->toDateTimeString();

        $rev = DB::table('revenues')
            ->where('bank_account_id', $this->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('gross_amount + COALESCE(adjustment_amount, 0)'));

        $exp = DB::table('expenditures')
            ->where('bank_account_id', $this->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0)'));

        $out = DB::table('outflows')
            ->where('origin_bank_account_id', $this->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0) - COALESCE(tax_amount, 0)'));

        $trans = DB::table('outflows')
            ->where('destination_bank_account_id', $this->id)
            ->where('type', 'Transferência')
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $limitDate)
            ->sum(DB::raw('amount + COALESCE(adjustment_amount, 0) - COALESCE(tax_amount, 0)'));

        return (float) round(($this->opening_balance + $rev + $trans) - ($exp + $out), 2);
    }
}
