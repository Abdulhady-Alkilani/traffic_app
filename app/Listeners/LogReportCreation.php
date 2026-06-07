<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Models\ActivityLog;

class LogReportCreation
{
    public function handle(ReportCreated $event): void
    {
        ActivityLog::create([
            'admin_id' => $event->report->citizen?->user?->adminData?->id ?? 1,
            'action_type' => 'create',
            'target_table' => 'reports',
            'description' => "New report #{$event->report->id} created by citizen ID {$event->report->citizen_id}. Type: {$event->report->report_type}",
        ]);
    }
}
