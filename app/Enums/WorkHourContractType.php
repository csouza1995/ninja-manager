<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkHourContractType: string
{
    case Fixed = 'fixed';
    case Current = 'current';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Contratada',
            self::Current => 'Corrente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Fixed => 'primary',
            self::Current => 'secondary',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Fixed => 'Responsabilidade de atingir o mínimo contratado.',
            self::Current => 'Limite máximo de horas, sem compromisso de mínimo.',
        };
    }
}
