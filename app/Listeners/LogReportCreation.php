<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Services\ActivityLogger;

class LogReportCreation
{
    public function __construct(
        private readonly ActivityLogger $logger,
    ) {}

    public function handle(ReportCreated $event): void
    {
        $report = $event->report;

        $this->logger->log(
            'create',
            'reports',
            "بلاغ جديد #{$report->id} — النوع: {$report->report_type}، القسم: " . ($report->assigned_department->value ?? 'غير محدد'),
        );
    }
}
