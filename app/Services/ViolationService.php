<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ViolationStatus;
use App\Models\PoliceData;
use App\Models\Report;
use App\Models\TrafficViolation;
use Illuminate\Support\Facades\DB;

class ViolationService
{
    public function issueFromReport(Report $report, PoliceData $police, array $data): TrafficViolation
    {
        return DB::transaction(function () use ($report, $police, $data): TrafficViolation {
            return TrafficViolation::create([
                'citizen_id' => $report->citizen_id,
                'vehicle_id' => $report->vehicle_id,
                'police_id' => $police->id,
                'report_id' => $report->id,
                'violation_type' => $data['violation_type'],
                'description' => $data['description'] ?? null,
                'fine_amount' => $data['fine_amount'],
                'due_date' => $data['due_date'],
                'status' => ViolationStatus::Unpaid,
                'issued_at' => now(),
            ]);
        });
    }

    public function pay(TrafficViolation $violation): TrafficViolation
    {
        return DB::transaction(function () use ($violation): TrafficViolation {
            $violation->update(['status' => ViolationStatus::Paid]);

            return $violation->fresh();
        });
    }
}
