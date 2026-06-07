<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $reportsData = [
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Two-car collision on the highway near the Amman entrance ramp. Minor injuries reported.', 'latitude' => '31.9453680', 'longitude' => '35.9303740', 'location_text' => 'Highway 15, Near Amman Entrance', 'status' => 'resolved'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Large pothole in the middle lane causing vehicles to swerve dangerously.', 'latitude' => '31.9875420', 'longitude' => '35.8621340', 'location_text' => 'Queen Rania Street, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Suspicious vehicle abandoned near the school zone with hazard lights flashing.', 'latitude' => '31.9234560', 'longitude' => '35.8789120', 'location_text' => 'Al-Bayader, Amman', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Severe traffic congestion lasting over 45 minutes due to construction work.', 'latitude' => '31.9567890', 'longitude' => '35.8456780', 'location_text' => 'Mecca Street, Amman', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Truck overturned on the highway shoulder blocking the right lane partially.', 'latitude' => '32.0123450', 'longitude' => '35.8234560', 'location_text' => 'Desert Highway, Near Zarqa Exit', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'Fallen tree branch blocking half of the residential road.', 'latitude' => '31.9789010', 'longitude' => '35.8912340', 'location_text' => 'Shmeisani, Amman', 'status' => 'resolved'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'Rear-end collision at traffic signal. Both vehicles have minor damage.', 'latitude' => '31.9645670', 'longitude' => '35.8534560', 'location_text' => 'Third Circle, Amman', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'Heavy congestion reported due to a music festival event nearby.', 'latitude' => '31.9012340', 'longitude' => '35.9123450', 'location_text' => 'Airport Road, Near Festival City', 'status' => 'rejected'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Group of individuals blocking traffic and causing disturbance at the roundabout.', 'latitude' => '31.9345670', 'longitude' => '35.8567890', 'location_text' => 'Sweifieh Roundabout, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Motorcycle debris scattered across the road after a minor crash.', 'latitude' => '31.9456780', 'longitude' => '35.8678900', 'location_text' => 'Rainbow Street, Amman', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'Standstill traffic reported for 30 minutes near the Dead Sea junction.', 'latitude' => '31.8765430', 'longitude' => '35.7890120', 'location_text' => 'Dead Sea Highway, Junction 12', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'assigned_department' => 'local_police', 'report_type' => 'accident', 'description' => 'Pedestrian hit by a van at a crosswalk. Ambulance called to the scene.', 'latitude' => '31.9123450', 'longitude' => '35.8345670', 'location_text' => 'Jabal Al-Hussein, Amman', 'status' => 'resolved'],
        // Reports 13-50 additional data
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Oil spill on the road surface near the gas station making it extremely slippery.', 'latitude' => '31.9512340', 'longitude' => '35.8712340', 'location_text' => 'Gardens Street, Amman', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Multi-vehicle pileup on the highway during rush hour. Three cars involved.', 'latitude' => '32.0234560', 'longitude' => '35.9012340', 'location_text' => 'Highway 35, Km 45', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Traffic signal malfunction causing gridlock at a major intersection.', 'latitude' => '31.9623450', 'longitude' => '35.8523450', 'location_text' => 'Abdali Boulevard, Amman', 'status' => 'resolved'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Unattended bag left at the bus stop near the university.', 'latitude' => '31.9756780', 'longitude' => '35.8634560', 'location_text' => 'University of Jordan Gate', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Broken glass scattered across multiple lanes after a minor accident.', 'latitude' => '31.9534560', 'longitude' => '35.8456780', 'location_text' => 'Zahran Street, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Car hit the guardrail and is stranded on the shoulder of the highway.', 'latitude' => '31.8923450', 'longitude' => '35.9234560', 'location_text' => 'Highway 15, Km 82', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Road narrowing due to utility works causing major delays.', 'latitude' => '31.9678900', 'longitude' => '35.8567890', 'location_text' => 'Paris Circle, Amman', 'status' => 'new'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'Street light pole leaning dangerously over the road after strong winds.', 'latitude' => '31.9812340', 'longitude' => '35.8923450', 'location_text' => 'Khalda, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'Bus and taxi collision at an intersection. No serious injuries.', 'latitude' => '31.9523450', 'longitude' => '35.8645670', 'location_text' => 'Seventh Circle, Amman', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'Large rock debris from hillside fell onto the highway lane.', 'latitude' => '31.8834560', 'longitude' => '35.7945670', 'location_text' => 'Dead Sea Road, Amman', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Road rage incident between two drivers blocking the entire street.', 'latitude' => '31.9345670', 'longitude' => '35.8678900', 'location_text' => 'Tabarbour, Amman', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'School zone congestion during morning drop-off hours.', 'latitude' => '31.9612340', 'longitude' => '35.8512340', 'location_text' => 'Al-Madina Al-Munawwara St', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Livestock on the highway caused a minor collision. One vehicle damaged.', 'latitude' => '32.0345670', 'longitude' => '35.8123450', 'location_text' => 'Desert Highway, North Section', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Water pipe burst flooding the road with ankle-deep water.', 'latitude' => '31.9567890', 'longitude' => '35.8723450', 'location_text' => 'Wasfi Al-Tal Street, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Vandalism reported on parked vehicles in the commercial area.', 'latitude' => '31.9423450', 'longitude' => '35.8534560', 'location_text' => 'Sweifieh Mall Area', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Tire blowout caused car to spin. No injuries but blocking lane.', 'latitude' => '31.9134560', 'longitude' => '35.9345670', 'location_text' => 'Highway 15, Km 60', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Major delay due to VIP motorcade passing through the area.', 'latitude' => '31.9567890', 'longitude' => '35.8623450', 'location_text' => 'Fourth Circle, Amman', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'Manhole cover missing on a busy pedestrian walkway.', 'latitude' => '31.9723450', 'longitude' => '35.8812340', 'location_text' => 'Downtown Amman', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Vehicle rolled over on a curve. Driver is injured and emergency services needed.', 'latitude' => '31.8712340', 'longitude' => '35.7834560', 'location_text' => 'Airport Highway Curve', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Football match traffic causing severe delays near the stadium.', 'latitude' => '31.9645670', 'longitude' => '35.8712340', 'location_text' => 'Al-Hussein Stadium Area', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Car with broken windows found abandoned in residential area.', 'latitude' => '31.9823450', 'longitude' => '35.8534560', 'location_text' => 'Tla Al-Ali, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Construction debris left on the road after work hours.', 'latitude' => '31.9534560', 'longitude' => '35.8423450', 'location_text' => 'King Hussein Street, Amman', 'status' => 'new'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Collision between a truck and a sedan near the toll booth.', 'latitude' => '32.0012340', 'longitude' => '35.8912340', 'location_text' => 'Northern Highway Toll', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Broken down truck blocking the intersection causing massive delay.', 'latitude' => '31.9478900', 'longitude' => '35.8534560', 'location_text' => 'Sport City Circle, Amman', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'Ice formation on the road during cold night causing slippery conditions.', 'latitude' => '31.9312340', 'longitude' => '35.8634560', 'location_text' => 'Dabouq, Amman', 'status' => 'resolved'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'Fog reducing visibility to less than 50 meters on the highway.', 'latitude' => '31.8912340', 'longitude' => '35.9456780', 'location_text' => 'Highway 15, Southern Section', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'Delivery van rear-ended a parked car. Driver fled the scene.', 'latitude' => '31.9545670', 'longitude' => '35.8312340', 'location_text' => 'Wadi Saqra, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Illegal street racing observed on empty roads during late night.', 'latitude' => '31.9612340', 'longitude' => '35.8745670', 'location_text' => 'King Abdullah II Street', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Water tanker broke down in the middle of the road during peak hours.', 'latitude' => '31.9423450', 'longitude' => '35.8612340', 'location_text' => 'Al-Bayader Main Road', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'Bus lost control and went off the road near the highway exit.', 'latitude' => '31.8623450', 'longitude' => '35.9012340', 'location_text' => 'Airport Highway, Exit 7', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'Loose gravel on the newly paved road causing poor traction.', 'latitude' => '31.9734560', 'longitude' => '35.8523450', 'location_text' => 'Al-Rabiyah, Amman', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'Unauthorized road blockade set up by protesters near the government building.', 'latitude' => '31.9523450', 'longitude' => '35.8834560', 'location_text' => 'Al-Abdali, Amman', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'Holiday rush causing extended delays on the main highway to Aqaba.', 'latitude' => '31.8534560', 'longitude' => '35.9234560', 'location_text' => 'Desert Highway South', 'status' => 'rejected'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'Motorcycle slid on wet road and hit a traffic barrier. Rider conscious.', 'latitude' => '31.9645670', 'longitude' => '35.8423450', 'location_text' => 'Fifth Circle, Amman', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'Stray animals on the road causing traffic to slow down significantly.', 'latitude' => '31.9234560', 'longitude' => '35.8534560', 'location_text' => 'Abu Nsair, Amman', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'Double parking on both sides narrowing the street to single lane.', 'latitude' => '31.9412340', 'longitude' => '35.8712340', 'location_text' => 'Marj Al-Hamam, Amman', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'Cargo spill from a truck covering the highway with produce.', 'latitude' => '32.0112340', 'longitude' => '35.8345670', 'location_text' => 'Northern Highway, Km 120', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'accident', 'description' => 'Child on bicycle hit by a car in the residential area. Minor injuries.', 'latitude' => '31.9534560', 'longitude' => '35.8912340', 'location_text' => 'Um Al-Summaq, Amman', 'status' => 'new'],
    ];

    public function run(array $citizens, array $vehicles): array
    {
        $reports = [];

        foreach ($this->reportsData as $data) {
            $vehicleId = null;
            if ($data['vehicle_index'] !== null && isset($vehicles[$data['vehicle_index']])) {
                $vehicleId = $vehicles[$data['vehicle_index']]->id;
            }

            $reports[] = Report::create([
                'citizen_id' => $citizens[$data['citizen_index']]->id,
                'vehicle_id' => $vehicleId,
                'assigned_department' => $data['assigned_department'],
                'report_type' => $data['report_type'],
                'description' => $data['description'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'location_text' => $data['location_text'],
                'status' => $data['status'],
            ]);
        }

        return $reports;
    }
}
