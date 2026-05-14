<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\TrafficViolation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Reports', Report::count())
                ->description('All submitted reports')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            Stat::make('Unresolved Reports', Report::whereNotIn('status', [ReportStatus::Resolved->value, ReportStatus::Rejected->value])->count())
                ->description('Reports pending resolution')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Total Unpaid Fines (SAR)', number_format(TrafficViolation::where('status', 'unpaid')->sum('fine_amount'), 2))
                ->description('Outstanding violation fines')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger'),
            Stat::make('Violations This Week', TrafficViolation::where('created_at', '>=', now()->startOfWeek())->count())
                ->description('Violations issued this week')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }
}
