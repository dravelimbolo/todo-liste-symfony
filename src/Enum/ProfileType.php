<?php

declare(strict_types=1);

namespace App\Enum;

enum ProfileType: string
{
    case SOLO = 'SOLO';
    case TEAM_LEAD = 'TEAM_LEAD';

    public function label(): string
    {
        return match ($this) {
            self::SOLO => 'Solo',
            self::TEAM_LEAD => 'Team Lead',
        };
    }

    public function role(): string
    {
        return match ($this) {
            self::SOLO => 'ROLE_SOLO',
            self::TEAM_LEAD => 'ROLE_TEAM_LEAD',
        };
    }
}
