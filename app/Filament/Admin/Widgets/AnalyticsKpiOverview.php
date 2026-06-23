<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsKpiOverview extends BaseWidget
{
    protected static ?int $sort = 10;

    public function getHeading(): string
    {
        return __('analytics.sections.kpis');
    }

    protected function getStats(): array
    {
        $service = app(AnalyticsService::class);
        $end = Carbon::now()->endOfDay();
        $start = Carbon::now()->subDays(89)->startOfDay();
        $kpis = $service->kpis($start, $end);
        $regions = $service->worstRegions($start, $end, 1);

        $worstRegion = $regions[0]['region'] ?? '—';

        $responseHours = $kpis['avg_response_minutes'] > 0
            ? round($kpis['avg_response_minutes'] / 60, 1) . ' ' . __('analytics.kpi.hours')
            : __('analytics.kpi.minutes');

        return [
            Stat::make(__('analytics.kpi.resolution_rate'), $kpis['resolution_rate'] . '%')
                ->description(__('analytics.kpi.total_reports') . ': ' . $kpis['total_reports'])
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make(__('analytics.kpi.avg_response'), $responseHours)
                ->description(__('analytics.delta.vs_previous') . ': ' . $kpis['avg_response_minutes'] . ' ' . __('analytics.kpi.minutes'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make(__('analytics.kpi.violation_rate'), $kpis['violation_rate_per_driver'])
                ->description(__('analytics.kpi.total_violations') . ': ' . $kpis['total_violations'])
                ->descriptionIcon('heroicon-m-truck')
                ->color('danger'),
            Stat::make(__('analytics.regions.worst'), $worstRegion)
                ->description(__('analytics.hotspots.incidents'))
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('rose'),
        ];
    }
}
