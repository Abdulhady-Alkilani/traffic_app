<?php

namespace App\Filament\Admin\Resources\TrafficViolationResource\Pages;

use App\Filament\Admin\Resources\TrafficViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrafficViolation extends ViewRecord
{
    protected static string $resource = TrafficViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
