<?php

namespace App\Domain\Strava\Activity;

enum ActivityType: string
{
    case RIDE = 'Ride';
    case RUN = 'Run';
    case WALK = 'Walk';
    case WATER_SPORTS = 'WaterSports';
    case WINTER_SPORTS = 'WinterSports';
    case OTHER = 'Other';

    public function supportsEddington(): bool
    {
        return match ($this) {
            self::RUN, self::RIDE, => true,
            default => false,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            ActivityType::RIDE => 'emerald-600',
            ActivityType::RUN => 'orange-500',
            ActivityType::WALK => 'yellow-300',
            ActivityType::WATER_SPORTS => 'blue-600',
            ActivityType::WINTER_SPORTS => 'red-600',
            default => 'grey-500',
        };
    }
}
