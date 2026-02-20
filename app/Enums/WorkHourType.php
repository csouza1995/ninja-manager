<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkHourType: string
{
    case Executed = 'executed';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Executed => 'Executado',
            self::Paid => 'Pago',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Executed => 'info',
            self::Paid => 'success',
        };
    }
}
