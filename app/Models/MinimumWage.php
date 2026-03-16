<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinimumWage extends Model
{
    use HasFactory;

    protected $fillable = [
        'effective_date',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the minimum wage effective for a given date.
     * Returns the most recent entry with effective_date <= $date.
     */
    public static function getForDate(Carbon|string $date): float
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        $wage = static::where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();

        return $wage ? (float) $wage->amount : 0;
    }
}
