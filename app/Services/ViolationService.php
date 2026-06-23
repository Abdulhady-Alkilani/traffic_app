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
    public function __construct(
        private readonly ActivityLogger $logger,
    ) {}

    public function issueFromReport(Report $report, PoliceData $police, array $data): TrafficViolation
    {
        $violation = DB::transaction(function () use ($report, $police, $data): TrafficViolation {
            return TrafficViolation::create([
                'citizen_id' => $report->vehicle ? $report->vehicle->citizen_id : $report->citizen_id,
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

        $this->logger->log(
            'create',
            'traffic_violations',
            "إصدار مخالفة #{$violation->id} بقيمة {$data['fine_amount']} ل.س — النوع: {$data['violation_type']}، مرتبطة بالبلاغ #{$report->id} — الضابط: {$police->full_name} (شارة: {$police->badge_number})",
        );

        return $violation;
    }

    public function pay(TrafficViolation $violation): TrafficViolation
    {
        $result = DB::transaction(function () use ($violation): TrafficViolation {
            $violation->update(['status' => ViolationStatus::Paid]);

            return $violation->fresh();
        });

        $this->logger->log(
            'payment',
            'traffic_violations',
            "تأكيد دفع المخالفة #{$violation->id} بقيمة {$violation->fine_amount} ل.س",
        );

        return $result;
    }
}
