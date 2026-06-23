<?php

declare(strict_types=1);

namespace App\Exports\AnalyticsSheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class RegionSheet implements FromArray, WithTitle
{
    /**
     * @param  array<int, array{region: string, reports: int, violations: int, incidents: int, resolution_rate: float}>  $regions
     */
    public function __construct(protected array $regions) {}

    public function title(): string
    {
        return __('analytics.export.region_sheet');
    }

    public function array(): array
    {
        $rows = [[
            __('analytics.export.region'),
            __('analytics.export.reports'),
            __('analytics.export.violations'),
            __('analytics.export.incidents'),
            __('analytics.export.resolution_rate'),
        ]];

        foreach ($this->regions as $region) {
            $rows[] = [
                $region['region'],
                $region['reports'],
                $region['violations'],
                $region['incidents'],
                $region['resolution_rate'],
            ];
        }

        return $rows;
    }
}
