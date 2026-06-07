<?php

declare(strict_types=1);

namespace App\Enums;

enum Department: string
{
    case HighwayPatrol = 'highway_patrol';
    case TrafficPolice = 'traffic_police';
    case LocalPolice = 'local_police';

    public function label(): string
    {
        return __('filament.enums.department.' . $this->value);
    }
}
