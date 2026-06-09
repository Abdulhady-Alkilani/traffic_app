<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficViolationResource\Pages;

use App\Filament\Admin\Resources\TrafficViolationResource;
use Filament\Resources\Pages\ListRecords;

class ListTrafficViolations extends ListRecords
{
    protected static string $resource = TrafficViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
