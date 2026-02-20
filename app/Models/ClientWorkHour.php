<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkHourMode;
use App\Enums\WorkHourType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClientWorkHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'mode',
        'contract_minutes',
        'executed_minutes',
        'weeks',
        'days',
        'hours',
        'minutes',
        'base_d',
        'base_w',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => WorkHourType::class,
            'mode' => WorkHourMode::class,
            'contract_minutes' => 'integer',
            'executed_minutes' => 'integer',
            'weeks' => 'integer',
            'days' => 'integer',
            'hours' => 'integer',
            'minutes' => 'integer',
            'base_d' => 'integer',
            'base_w' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'client_work_hour_service');
    }

    /**
     * Returns executed_minutes as "HHH:MM" string.
     */
    public function toFormattedHhMm(): string
    {
        $totalMinutes = $this->executed_minutes;
        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        return sprintf('%d:%02d', $h, $m);
    }

    /**
     * Decomposes executed_minutes into W:D:H:M using this record's base_d and base_w.
     *
     * @return array{w: int, d: int, h: int, m: int}
     */
    public function toWdhm(): array
    {
        $remaining = $this->executed_minutes;

        $minutesPerDay = $this->base_d * 60;
        $minutesPerWeek = $this->base_w * $minutesPerDay;

        $w = intdiv($remaining, $minutesPerWeek);
        $remaining -= $w * $minutesPerWeek;

        $d = intdiv($remaining, $minutesPerDay);
        $remaining -= $d * $minutesPerDay;

        $h = intdiv($remaining, 60);
        $m = $remaining % 60;

        return compact('w', 'd', 'h', 'm');
    }

    /**
     * Converts WDHM components to total minutes given base_d and base_w.
     */
    public static function wdhmToMinutes(int $w, int $d, int $h, int $m, int $baseD = 8, int $baseW = 5): int
    {
        return ($w * $baseW * $baseD * 60)
            + ($d * $baseD * 60)
            + ($h * 60)
            + $m;
    }

    /**
     * Converts a "HHH:MM" string to total minutes.
     */
    public static function hhMmToMinutes(string $hhMm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhMm));

        return ($h * 60) + $m;
    }
}
