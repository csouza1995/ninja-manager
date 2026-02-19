<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceStatus: string
{
    case Negotiating = 'negotiating';
    case Approved = 'approved';
    case InProgress = 'in_progress';
    case Delivered = 'delivered';
    case Finalized = 'finalized';

    public function label(): string
    {
        return match ($this) {
            self::Negotiating => 'Em Negociação',
            self::Approved => 'Aprovado',
            self::InProgress => 'Em Progresso',
            self::Delivered => 'Entregue',
            self::Finalized => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Negotiating => 'warning',
            self::Approved => 'info',
            self::InProgress => 'primary',
            self::Delivered => 'secondary',
            self::Finalized => 'success',
        };
    }
}
