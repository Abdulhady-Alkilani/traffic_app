<?php

declare(strict_types=1);

namespace App\Exports\AnalyticsSheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class TrendSheet implements FromArray, WithTitle
{
    /**
     * @param  array<string, array{reports: int, violations: int, total: int}>  $trend
     */
    public function __construct(protected array $trend) {}

    public function title(): string
    {
        return __('analytics.export.trend_sheet');
    }

    public function array(): array
    {
        $rows = [[__('analytics.export.month'), __('analytics.export.reports'), __('analytics.export.violations'), __('analytics.export.total')]];

        foreach ($this->trend as $month => $data) {
            $rows[] = [$month, $data['reports'], $data['violations'], $data['total']];
        }

        return $rows;
    }
}
