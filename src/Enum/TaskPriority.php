<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskPriority: string
{
    case LOW    = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH   = 'HIGH';
    case URGENT = 'URGENT';

    public function label(): string
    {
        return match($this) {
            self::LOW    => 'Faible',
            self::MEDIUM => 'Moyenne',
            self::HIGH   => 'Haute',
            self::URGENT => 'Urgente',
        };
    }

    public function badgeClasses(): string
    {
        return match($this) {
            self::LOW    => 'bg-surface-container-high text-on-surface-variant',
            self::MEDIUM => 'bg-surface-container text-on-surface',
            self::HIGH   => 'bg-secondary-container text-on-secondary-container',
            self::URGENT => 'bg-error-container text-error',
        };
    }
}