<?php

namespace App\Services;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Events\ReportCreated;
use App\Models\Report;

class ReportCreationService
{
    public function determineDepartment(string $reportType, ?float $latitude = null, ?float $longitude = null): Department
    {
        $isOutsideCity = $this->isOutsideCityLimits($latitude, $longitude);

        return match ($reportType) {
            'accident', 'hazard' => $isOutsideCity ? Department::HighwayPatrol : Department::TrafficPolice,
            'traffic_jam' => Department::TrafficPolice,
            'security_threat' => Department::LocalPolice,
            default => Department::TrafficPolice,
        };
    }

    public function createReport(array $data): Report
    {
        $department = $this->determineDepartment(
            $data['report_type'],
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        );

        $report = Report::create([
            'citizen_id' => $data['citizen_id'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'assigned_department' => $department,
            'report_type' => $data['report_type'],
            'description' => $data['description'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'location_text' => $data['location_text'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'status' => ReportStatus::New,
        ]);

        ReportCreated::dispatch($report);

        return $report;
    }

    private function isOutsideCityLimits(?float $latitude, ?float $longitude): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        $cityCenterLat = 29.3759;
        $cityCenterLng = 47.9774;
        $radiusKm = 20;

        $latDiff = $latitude - $cityCenterLat;
        $lngDiff = $longitude - $cityCenterLng;

        $distance = sqrt(($latDiff * $latDiff) + ($lngDiff * $lngDiff)) * 111;

        return $distance > $radiusKm;
    }
}
