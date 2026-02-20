<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkHourContractType;
use App\Enums\WorkHourMode;
use App\Enums\WorkHourType;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'contract_type',
        'contract_minutes',
        'executed_minutes',
        'hourly_rate',
        'weeks',
        'days',
        'hours',
        'minutes',
        'contract_weeks',
        'contract_days',
        'contract_hours_raw',
        'contract_minutes_raw',
        'base_d',
        'base_w',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => WorkHourType::class,
            'mode' => WorkHourMode::class,
            'contract_type' => WorkHourContractType::class,
            'contract_minutes' => 'integer',
            'executed_minutes' => 'integer',
            'hourly_rate' => 'decimal:2',
            'weeks' => 'integer',
            'days' => 'integer',
            'hours' => 'integer',
            'minutes' => 'integer',
            'contract_weeks' => 'integer',
            'contract_days' => 'integer',
            'contract_hours_raw' => 'integer',
            'contract_minutes_raw' => 'integer',
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
     * Accessor for executed_value (monetary value based on executed_minutes and hourly_rate)
     */
    protected function executedValue(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->executed_minutes / 60) * (float) $this->hourly_rate,
        );
    }

    /**
     * Accessor for contract_value (monetary capacity based on contract_minutes and hourly_rate)
     */
    protected function contractValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contract_minutes
                ? ($this->contract_minutes / 60) * (float) $this->hourly_rate
                : 0,
        );
    }

    /**
     * Calculates the achievement percentage (executed / contract)
     */
    public function achievementPercent(): float
    {
        if (! $this->contract_minutes || $this->contract_minutes === 0) {
            return 0;
        }

        return min(100, round(($this->executed_minutes / $this->contract_minutes) * 100, 1));
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
     * Returns contract_minutes as "HHH:MM" string.
     */
    public function toFormattedContractHhMm(): string
    {
        if ($this->contract_minutes === null) {
            return '—';
        }

        $totalMinutes = $this->contract_minutes;
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
     * Decomposes contract_minutes into W:D:H:M using this record's base_d and base_w.
     *
     * @return array{w: int, d: int, h: int, m: int}
     */
    public function toContractWdhm(): array
    {
        if ($this->contract_minutes === null) {
            return ['w' => 0, 'd' => 0, 'h' => 0, 'm' => 0];
        }

        $remaining = $this->contract_minutes;

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
        if (! str_contains($hhMm, ':')) {
            return (int) $hhMm * 60;
        }

        [$h, $m] = array_map('intval', explode(':', $hhMm));

        return ($h * 60) + $m;
    }
}
