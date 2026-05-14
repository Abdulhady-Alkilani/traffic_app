<?php

namespace App\Filament\Police\Resources\TrafficViolationResource\Pages;

use App\Filament\Police\Resources\TrafficViolationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrafficViolation extends CreateRecord
{
    protected static string $resource = TrafficViolationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['police_id'] = auth()->user()->policeData->id;
        $data['issued_at'] = now();

        return $data;
    }
}
