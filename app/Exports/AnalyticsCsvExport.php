<?php

declare(strict_types=1);

namespace App\Exports;

use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class AnalyticsCsvExport implements FromArray
{
    use Exportable;

    public function __construct(
        protected AnalyticsService $service,
        protected Carbon $start,
        protected Carbon $end,
    ) {}

    public function array(): array
    {
        $service = $this->service;
        $kpis = $service->kpis($this->start, $this->end);
        $trend = $service->monthlyTrend($this->start, $this->end);
        $regions = $service->regionCompliance($this->start, $this->end);

        $rows = [[__('analytics.export.section'), __('analytics.export.metric'), __('analytics.export.value')]];

        $rows[] = [__('analytics.export.kpi_sheet'), __('analytics.export.total_reports'), $kpis['total_reports']];
        $rows[] = ['', __('analytics.export.total_violations'), $kpis['total_violations']];
        $rows[] = ['', __('analytics.export.avg_response'), round($kpis['avg_response_minutes'], 2)];
        $rows[] = ['', __('analytics.export.resolution_rate'), $kpis['resolution_rate']];
        $rows[] = ['', __('analytics.export.violation_rate'), $kpis['violation_rate_per_driver']];
        $rows[] = ['', __('analytics.export.collection_rate'), $kpis['collection_rate']];
        $rows[] = ['', __('analytics.export.total_fines'), $kpis['total_fines']];
        $rows[] = ['', __('analytics.export.collected_fines'), $kpis['collected_fines']];

        foreach ($trend as $month => $data) {
            $rows[] = [__('analytics.export.trend_sheet'), $month, $data['total']];
        }

        foreach ($regions as $region) {
            $rows[] = [__('analytics.export.region_sheet'), $region['region'], $region['incidents']];
        }

        return $rows;
    }
}
