<?php

namespace App\Filament\Police\Widgets;

use App\Models\Report;
use App\Models\TrafficViolation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PoliceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $policeData = $user->policeData;

        if (!$policeData) {
            return [];
        }

        $department = $policeData->department;

        $totalReports = Report::where('assigned_department', $department)->count();
        $pendingReports = Report::where('assigned_department', $department)
            ->where('status', 'new')
            ->count();
            
        $myViolations = TrafficViolation::where('police_id', $policeData->id)->count();

        return [
            Stat::make(__('إجمالي بلاغات القسم'), $totalReports)
                ->description(__('جميع البلاغات المحولة للقسم'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make(__('بلاغات قيد الانتظار'), $pendingReports)
                ->description(__('تحتاج إلى مراجعة'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingReports > 0 ? 'warning' : 'success'),

            Stat::make(__('مخالفاتي المسجلة'), $myViolations)
                ->description(__('عدد المخالفات التي قمت بإصدارها'))
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
        ];
    }
}
