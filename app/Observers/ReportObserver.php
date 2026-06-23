<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Report;
use App\Services\ActivityLogger;

class ReportObserver
{
    public function __construct(
        private readonly ActivityLogger $logger,
    ) {}

    /**
     * Log status changes on reports (typically done by police).
     */
    public function updated(Report $report): void
    {
        if (!$report->wasChanged('status')) {
            return;
        }

        $old = $report->getOriginal('status');
        $oldValue = $old instanceof \BackedEnum ? $old->value : (string) $old;
        $new = $report->status->value;

        // Skip the initial creation transition handled elsewhere
        if ($oldValue === $new) {
            return;
        }

        $this->logger->log(
            'status_change',
            'reports',
            "تحديث حالة البلاغ #{$report->id} من «{$oldValue}» إلى «{$new}»",
        );
    }
}
