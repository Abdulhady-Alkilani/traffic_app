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
            'plate_number' => 'دمشق 123456',
            'vehicle_type' => 'sedan',
            'make' => 'Kia',
            'model_name' => 'Cerato',
            'model_year' => 2010,
            'color' => '#ffffff',
            'chassis_number' => 'KNA12345678901234',
            'engine_number' => 'G4FC123456',
            'registration_expiry' => '2027-01-15',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 0,
            'plate_number' => 'ريف دمشق 654321',
            'vehicle_type' => 'suv',
            'make' => 'Hyundai',
            'model_name' => 'Tucson',
            'model_year' => 2022,
            'color' => '#000000',
            'chassis_number' => 'KMH98765432109876',
            'engine_number' => 'G4NA987654',
            'registration_expiry' => '2026-11-20',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'حلب 112233',
            'vehicle_type' => 'sedan',
            'make' => 'Toyota',
            'model_name' => 'Camry',
            'model_year' => 2019,
            'color' => '#c0c0c0',
            'chassis_number' => 'JTN11223344556677',
            'engine_number' => '2AR112233',
            'registration_expiry' => '2025-05-10',
            'insurance_status' => 'expired',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'حمص 445566',
            'vehicle_type' => 'van',
            'make' => 'Toyota',
            'model_name' => 'Hiace',
            'model_year' => 2021,
            'color' => '#ffffff',
            'chassis_number' => 'JT111222333444555',
            'engine_number' => '2KD445566',
            'registration_expiry' => '2026-08-30',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 1,
            'plate_number' => 'طرطوس 998877',
            'vehicle_type' => 'motorcycle',
            'make' => 'Suzuki',
            'model_name' => 'GN125',
            'model_year' => 2020,
            'color' => '#ff0000',
            'chassis_number' => 'LC612345678901234',
            'engine_number' => 'F401123456',
            'registration_expiry' => '2025-12-01',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 2,
            'plate_number' => 'اللاذقية 776655',
            'vehicle_type' => 'truck',
            'make' => 'Mitsubishi',
            'model_name' => 'Canter',
            'model_year' => 2018,
            'color' => '#ffffff',
            'chassis_number' => 'JMB77665544332211',
            'engine_number' => '4D33776655',
            'registration_expiry' => '2025-01-20',
            'insurance_status' => 'expired',
        ],
        [
            'citizen_index' => 2,
            'plate_number' => 'حماة 223344',
            'vehicle_type' => 'sedan',
            'make' => 'Mercedes',
            'model_name' => 'C200',
            'model_year' => 2024,
            'color' => '#000000',
            'chassis_number' => 'WDD22334455667788',
            'engine_number' => 'M274223344',
            'registration_expiry' => '2027-06-15',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 3,
            'plate_number' => 'دمشق 990011',
            'vehicle_type' => 'suv',
            'make' => 'Nissan',
            'model_name' => 'Patrol',
            'model_year' => 2023,
            'color' => '#c0c0c0',
            'chassis_number' => 'JN199001122334455',
            'engine_number' => 'VK56990011',
            'registration_expiry' => '2026-09-09',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 3,
            'plate_number' => 'درعا 554433',
            'vehicle_type' => 'sedan',
            'make' => 'Kia',
            'model_name' => 'Rio',
            'model_year' => 2020,
            'color' => '#ff0000',
            'chassis_number' => 'KNA55443322110099',
            'engine_number' => 'G4FA554433',
            'registration_expiry' => '2025-03-25',
            'insurance_status' => 'expired',
        ],
        [
            'citizen_index' => 4,
            'plate_number' => 'السويداء 102030',
            'vehicle_type' => 'sedan',
            'make' => 'Hyundai',
            'model_name' => 'Elantra',
            'model_year' => 2021,
            'color' => '#ffffff',
            'chassis_number' => 'KMH10203040506070',
            'engine_number' => 'G4FG102030',
            'registration_expiry' => '2026-02-14',
            'insurance_status' => 'valid',
        ],
        [
            'citizen_index' => 4,
            'plate_number' => 'حلب 708090',
            'vehicle_type' => 'suv',
            'make' => 'Kia',
            'model_name' => 'Sportage',
            'model_year' => 2022,
            'color' => '#0000ff',
            'chassis_number' => 'KNC70809010203040',
            'engine_number' => 'G4NA708090',
            'registration_expiry' => '2027-04-30',
            'insurance_status' => 'valid',
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
                'model_name' => $data['model_name'],
                'model_year' => $data['model_year'],
                'color' => $data['color'],
                'chassis_number' => $data['chassis_number'],
                'engine_number' => $data['engine_number'],
                'registration_expiry' => $data['registration_expiry'],
                'insurance_status' => $data['insurance_status'],
            ]);
        }

        return $vehicles;
    }
}
