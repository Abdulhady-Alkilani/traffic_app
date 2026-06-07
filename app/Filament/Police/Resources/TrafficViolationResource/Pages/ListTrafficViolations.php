<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\TrafficViolationResource\Pages;

use App\Filament\Police\Resources\TrafficViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrafficViolations extends ListRecords
{
    protected static string $resource = TrafficViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
