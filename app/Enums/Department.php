<?php

namespace App\Enums;

enum Department: string
{
    case HighwayPatrol = 'highway_patrol';
    case TrafficPolice = 'traffic_police';
    case LocalPolice = 'local_police';

    public function label(): string
    {
        return match($this) {
            self::HighwayPatrol => 'Highway Patrol',
            self::TrafficPolice => 'Traffic Police',
            self::LocalPolice => 'Local Police',
        };
    }
}