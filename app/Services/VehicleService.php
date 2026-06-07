<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CitizenData;
use App\Models\Vehicle;

class VehicleService
{
    public function create(CitizenData $citizen, array $data): Vehicle
    {
        return Vehicle::create([
            'citizen_id' => $citizen->id,
            ...$data,
        ]);
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->update($data);

        return $vehicle->fresh();
    }

    public function delete(Vehicle $vehicle): void
    {
        $vehicle->delete();
    }
}
