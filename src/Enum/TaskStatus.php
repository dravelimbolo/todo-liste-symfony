<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatus: string
{
    case TODO        = 'TODO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case DONE        = 'DONE';

    public function label(): string
    {
        return match($this) {
            self::TODO        => 'À faire',
            self::IN_PROGRESS => 'En cours',
            self::DONE        => 'Terminé',
        };
    }

    public function badgeClasses(): string
    {
        return match($this) {
            self::TODO        => 'bg-surface-container-high text-on-surface-variant',
            self::IN_PROGRESS => 'bg-secondary-container text-on-secondary-container',
            self::DONE        => 'bg-primary text-on-primary',
        };
    }
}