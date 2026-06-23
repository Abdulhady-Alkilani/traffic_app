<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\TrafficViolationResource\Pages;

use App\Filament\Police\Resources\TrafficViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrafficViolation extends EditRecord
{
    protected static string $resource = TrafficViolationResource::class;

    protected function getHeaderActions(): array
    {
        // No delete action — only admins can delete violations.
        return [];
    }
}
