<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Services\ReportAiAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AnalyzeReportWithAi implements ShouldQueue
{
    public function __construct(
        private readonly ReportAiAnalyzer $analyzer,
    ) {}

    public function handle(ReportCreated $event): void
    {
        $report = $event->report;

        // Only analyze if the report has a description, image, or video
        if (!$report->description && !$report->image_url && !$report->video_url) {
            return;
        }

        try {
            $this->analyzer->analyze($report);
        } catch (\Exception $e) {
            // Never let AI analysis failure break the report creation flow
            Log::error('AnalyzeReportWithAi: Listener failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
