<?php

declare(strict_types=1);

namespace App\Exports;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Exports\AnalyticsSheets\KpiSheet;
use App\Exports\AnalyticsSheets\RegionSheet;
use App\Exports\AnalyticsSheets\TrendSheet;
use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnalyticsExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected AnalyticsService $service,
        protected Carbon $start,
        protected Carbon $end,
        protected bool $compare = true,
    ) {}

    public function sheets(): array
    {
        $kpis = $this->service->kpis($this->start, $this->end);
        $comparison = $this->compare ? $this->service->compareWithPrevious($this->start, $this->end) : [];

        return [
            new KpiSheet($kpis, $comparison),
            new TrendSheet($this->service->monthlyTrend($this->start, $this->end)),
            new RegionSheet($this->service->regionCompliance($this->start, $this->end)),
        ];
    }
}
