<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\TrafficViolationResource\Pages;

use App\Filament\Police\Resources\TrafficViolationResource as TrafficViolationResourceClass;
use Filament\Resources\Pages\CreateRecord;

class CreateTrafficViolation extends CreateRecord
{
    protected static string $resource = TrafficViolationResourceClass::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        if ($user && $user->policeData) {
            $data['police_id'] = $user->policeData->id;
        }

        return $data;
    }
}
