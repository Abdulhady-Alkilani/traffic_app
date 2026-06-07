<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\TrafficViolationResource\Pages;

use App\Filament\Police\Resources\TrafficViolationResource as TrafficViolationResourceClass;
use Filament\Resources\Pages\CreateRecord;

class CreateTrafficViolation extends CreateRecord
{
    protected static string $resource = TrafficViolationResourceClass::class;
}
