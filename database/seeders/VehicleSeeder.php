<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $vehiclesData = [
        [
            'citizen_index' => 0,
            'plate_number' => 'ABC 1234',
            'vehicle_type' => 'sedan',
            'make' => 'Toyota',
            'model_year' => 2020,
            'color' => '#ffffff',
        ],
        [
            'citizen_index' => 0,
            'plate_number' => 'DEF 5678',
            'vehicle_type' => 'suv',
            'make' => 'Hyundai',
            'model_year' => 2022,
            'color' => '#000000',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'GHI 9012',
            'vehicle_type' => 'sedan',
            'make' => 'Honda',
            'model_year' => 2019,
            'color' => '#c0c0c0',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'JKL 3456',
            'vehicle_type' => 'van',
            'make' => 'Toyota',
            'model_year' => 2021,
            'color' => '#ffffff',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'MNO 7890',
            'vehicle_type' => 'motorcycle',
            'make' => 'BMW',
            'model_year' => 2023,
            'color' => '#ff0000',
        ],
        [
            'citizen_index' => 2,
            'plate_number' => 'PQR 2345',
            'vehicle_type' => 'truck',
            'make' => 'Ford',
            'model_year' => 2018,
            'color' => '#0000ff',
        ],
        [
            'citizen_index' => 2,
            'plate_number' => 'STU 6789',
            'vehicle_type' => 'sedan',
            'make' => 'Mercedes',
            'model_year' => 2024,
            'color' => '#000000',
        ],
        [
            'citizen_index' => 3,
            'plate_number' => 'VWX 1357',
            'vehicle_type' => 'suv',
            'make' => 'Toyota',
            'model_year' => 2023,
            'color' => '#c0c0c0',
        ],
        [
            'citizen_index' => 3,
            'plate_number' => 'YZA 2468',
            'vehicle_type' => 'sedan',
            'make' => 'Honda',
            'model_year' => 2020,
            'color' => '#ff0000',
        ],
        [
            'citizen_index' => 4,
            'plate_number' => 'BCD 3579',
            'vehicle_type' => 'sedan',
            'make' => 'Hyundai',
            'model_year' => 2021,
            'color' => '#ffffff',
        ],
        [
            'citizen_index' => 4,
            'plate_number' => 'EFG 4680',
            'vehicle_type' => 'suv',
            'make' => 'Ford',
            'model_year' => 2022,
            'color' => '#0000ff',
        ],
        [
            'citizen_index' => 4,
            'plate_number' => 'HIJ 5791',
            'vehicle_type' => 'motorcycle',
            'make' => 'BMW',
            'model_year' => 2024,
            'color' => '#000000',
        ],
    ];

    public function run(array $citizens): array
    {
        $vehicles = [];

        foreach ($this->vehiclesData as $data) {
            $vehicles[] = Vehicle::create([
                'citizen_id' => $citizens[$data['citizen_index']]->id,
                'plate_number' => $data['plate_number'],
                'vehicle_type' => $data['vehicle_type'],
                'make' => $data['make'],
                'model_year' => $data['model_year'],
                'color' => $data['color'],
            ]);
        }

        return $vehicles;
    }
}
