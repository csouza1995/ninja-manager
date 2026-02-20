<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkHourMode: string
{
    case HoursMinutes = 'hhh_mm';
    case Wdhm = 'wdhm';

    public function label(): string
    {
        return match ($this) {
            self::HoursMinutes => 'HHH:MM',
            self::Wdhm => 'W:D:H:M',
        };
    }
}
