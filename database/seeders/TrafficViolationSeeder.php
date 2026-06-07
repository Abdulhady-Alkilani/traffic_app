<?php

namespace Database\Seeders;

use App\Models\TrafficViolation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrafficViolationSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $violationsData = [
        ['citizen_index' => 0, 'vehicle_index' => 0, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'Exceeding speed limit by 30 km/h on Highway 15.', 'fine_amount' => '100.00', 'status' => 'unpaid', 'issued_at' => '2026-04-20 10:30:00', 'due_date' => '2026-07-20'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'police_index' => 1, 'report_index' => 1, 'violation_type' => 'reckless_driving', 'description' => 'Reckless lane changing and tailgating on Queen Rania Street.', 'fine_amount' => '500.00', 'status' => 'paid', 'issued_at' => '2026-03-15 14:45:00', 'due_date' => '2026-06-15'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'Ran a red light at the intersection near City Mall.', 'fine_amount' => '300.00', 'status' => 'unpaid', 'issued_at' => '2026-05-01 08:15:00', 'due_date' => '2026-08-01'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'police_index' => 0, 'report_index' => 11, 'violation_type' => 'illegal_parking', 'description' => 'Vehicle parked in a no-parking zone blocking emergency access.', 'fine_amount' => '150.00', 'status' => 'unpaid', 'issued_at' => '2026-04-28 16:20:00', 'due_date' => '2026-07-28'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'police_index' => 1, 'report_index' => 4, 'violation_type' => 'speeding', 'description' => 'Truck exceeding speed limit in construction zone.', 'fine_amount' => '1000.00', 'status' => 'canceled', 'issued_at' => '2026-02-10 09:00:00', 'due_date' => '2026-05-10'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'Driver observed not wearing seatbelt on Shmeisani Road.', 'fine_amount' => '100.00', 'status' => 'paid', 'issued_at' => '2026-04-05 11:30:00', 'due_date' => '2026-07-05'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'police_index' => 0, 'report_index' => 6, 'violation_type' => 'using_phone', 'description' => 'Driver caught using mobile phone while driving at Third Circle.', 'fine_amount' => '300.00', 'status' => 'unpaid', 'issued_at' => '2026-05-10 13:00:00', 'due_date' => '2026-08-10'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'Speeding in a residential area near schools.', 'fine_amount' => '2000.00', 'status' => 'unpaid', 'issued_at' => '2026-04-22 07:45:00', 'due_date' => '2026-07-22'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'Double-parked in Sweifieh commercial area blocking traffic flow.', 'fine_amount' => '150.00', 'status' => 'paid', 'issued_at' => '2026-03-30 18:10:00', 'due_date' => '2026-06-30'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'police_index' => 0, 'report_index' => 9, 'violation_type' => 'reckless_driving', 'description' => 'Reckless motorcycle riding on Rainbow Street performing stunts.', 'fine_amount' => '500.00', 'status' => 'unpaid', 'issued_at' => '2026-05-12 20:30:00', 'due_date' => '2026-08-12'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'Driver not wearing seatbelt at routine checkpoint.', 'fine_amount' => '100.00', 'status' => 'paid', 'issued_at' => '2026-01-20 09:30:00', 'due_date' => '2026-04-20'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'using_phone', 'description' => 'Using phone while stopped at traffic light.', 'fine_amount' => '300.00', 'status' => 'unpaid', 'issued_at' => '2026-05-08 17:00:00', 'due_date' => '2026-08-08'],
        // Violations 13-22 additional data
        ['citizen_index' => 0, 'vehicle_index' => 0, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'Driver ran red light at Sport City Circle intersection during peak hour.', 'fine_amount' => '300.00', 'status' => 'unpaid', 'issued_at' => '2026-05-15 08:00:00', 'due_date' => '2026-08-15'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'Van caught exceeding speed limit by 45 km/h in school zone.', 'fine_amount' => '750.00', 'status' => 'paid', 'issued_at' => '2026-04-10 07:30:00', 'due_date' => '2026-07-10'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'Truck parked on sidewalk blocking pedestrian access.', 'fine_amount' => '200.00', 'status' => 'unpaid', 'issued_at' => '2026-05-18 15:30:00', 'due_date' => '2026-08-18'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'Rear seat passengers without seatbelts during routine stop.', 'fine_amount' => '100.00', 'status' => 'canceled', 'issued_at' => '2026-03-25 12:00:00', 'due_date' => '2026-06-25'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'using_phone', 'description' => 'Driver texting while driving on Mecca Street.', 'fine_amount' => '300.00', 'status' => 'unpaid', 'issued_at' => '2026-05-20 16:45:00', 'due_date' => '2026-08-20'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'police_index' => 1, 'report_index' => 13, 'violation_type' => 'reckless_driving', 'description' => 'Dangerous overtaking on blind curve of highway.', 'fine_amount' => '500.00', 'status' => 'unpaid', 'issued_at' => '2026-05-22 10:15:00', 'due_date' => '2026-08-22'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'Ran a red light while turning left at Abdali Boulevard.', 'fine_amount' => '300.00', 'status' => 'paid', 'issued_at' => '2026-04-15 09:30:00', 'due_date' => '2026-07-15'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'Exceeding speed limit by 20 km/h on Zahran Street.', 'fine_amount' => '150.00', 'status' => 'unpaid', 'issued_at' => '2026-05-25 14:00:00', 'due_date' => '2026-08-25'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'Parked in disabled person spot without valid permit at City Mall.', 'fine_amount' => '500.00', 'status' => 'unpaid', 'issued_at' => '2026-05-28 19:00:00', 'due_date' => '2026-08-28'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'reckless_driving', 'description' => 'Drifting on wet road near residential area at night.', 'fine_amount' => '1500.00', 'status' => 'unpaid', 'issued_at' => '2026-05-30 23:00:00', 'due_date' => '2026-08-30'],
    ];

    public function run(array $citizens, array $vehicles, array $officers, array $reports): array
    {
        $violations = [];

        foreach ($this->violationsData as $data) {
            $vehicleId = null;
            if ($data['vehicle_index'] !== null && isset($vehicles[$data['vehicle_index']])) {
                $vehicleId = $vehicles[$data['vehicle_index']]->id;
            }

            $reportId = null;
            if ($data['report_index'] !== null && isset($reports[$data['report_index']])) {
                $reportId = $reports[$data['report_index']]->id;
            }

            $violations[] = TrafficViolation::create([
                'citizen_id' => $citizens[$data['citizen_index']]->id,
                'vehicle_id' => $vehicleId,
                'police_id' => $officers[$data['police_index']]->id,
                'report_id' => $reportId,
                'violation_type' => $data['violation_type'],
                'description' => $data['description'],
                'fine_amount' => $data['fine_amount'],
                'status' => $data['status'],
                'issued_at' => $data['issued_at'],
                'due_date' => $data['due_date'],
            ]);
        }

        return $violations;
    }
}
