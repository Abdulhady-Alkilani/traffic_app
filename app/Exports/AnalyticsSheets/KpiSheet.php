<?php

declare(strict_types=1);

namespace App\Exports\AnalyticsSheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class KpiSheet implements FromArray, WithTitle
{
    public function __construct(
        protected array $kpis,
        protected array $comparison = [],
    ) {}

    public function title(): string
    {
        return __('analytics.export.kpi_sheet');
    }

    public function array(): array
    {
        $rows = [
            [__('analytics.export.metric'), __('analytics.export.value'), __('analytics.export.previous'), __('analytics.export.change_percent')],
        ];

        $comparison = $this->comparison['metrics'] ?? [];
        $labels = [
            'total_reports' => __('analytics.export.total_reports'),
            'total_violations' => __('analytics.export.total_violations'),
            'avg_response_minutes' => __('analytics.export.avg_response'),
            'resolution_rate' => __('analytics.export.resolution_rate'),
            'violation_rate_per_driver' => __('analytics.export.violation_rate'),
            'collection_rate' => __('analytics.export.collection_rate'),
            'total_fines' => __('analytics.export.total_fines'),
            'collected_fines' => __('analytics.export.collected_fines'),
            'outstanding_fines' => __('analytics.export.outstanding_fines'),
        ];

        foreach ($labels as $key => $label) {
            $current = $this->kpis[$key] ?? 0;
            $metric = $comparison[$key] ?? null;
            $rows[] = [
                $label,
                $current,
                $metric['previous'] ?? '',
                $metric['percent'] ?? '',
            ];
        }

        return $rows;
    }
}
