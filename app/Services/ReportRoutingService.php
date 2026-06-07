<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Events\ReportCreated;
use App\Exceptions\ReportRoutingException;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ReportRoutingService
{
    public function determineDepartment(string $reportType, ?float $latitude = null, ?float $longitude = null): Department
    {
        return match ($reportType) {
            'security_threat' => Department::LocalPolice,
            'traffic_jam' => Department::TrafficPolice,
            'accident', 'hazard' => $this->isOutsideCityLimits($latitude, $longitude)
                ? Department::HighwayPatrol
                : Department::TrafficPolice,
            default => throw ReportRoutingException::unknownReportType($reportType),
        };
    }

    public function createReport(array $data): Report
    {
        $latitude = isset($data['latitude']) && is_numeric($data['latitude']) ? (float) $data['latitude'] : null;
        $longitude = isset($data['longitude']) && is_numeric($data['longitude']) ? (float) $data['longitude'] : null;

        $department = $this->determineDepartment(
            $data['report_type'],
            $latitude,
            $longitude
        );

        return DB::transaction(function () use ($data, $department, $latitude, $longitude): Report {
            $report = Report::create([
                'citizen_id' => $data['citizen_id'],
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'assigned_department' => $department,
                'report_type' => $data['report_type'],
                'description' => $data['description'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_text' => $data['location_text'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'status' => ReportStatus::New,
            ]);

            ReportCreated::dispatch($report);

            return $report;
        });
    }

    private function isOutsideCityLimits(?float $latitude, ?float $longitude): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        return $latitude > 24.8 && $longitude > 46.8;
    }
}
