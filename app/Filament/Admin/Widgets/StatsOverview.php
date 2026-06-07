<?php

declare(strict_types=1);

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
            Stat::make(__('filament.widgets.total_users'), User::count())
                ->description(__('filament.widgets.all_registered_users'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make(__('filament.widgets.total_reports'), Report::count())
                ->description(__('filament.widgets.all_submitted_reports'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            Stat::make(__('filament.widgets.unresolved_reports'), Report::whereNotIn('status', [ReportStatus::Resolved->value, ReportStatus::Rejected->value])->count())
                ->description(__('filament.widgets.reports_pending'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make(__('messages.total_unpaid_fines'), number_format((float) TrafficViolation::where('status', 'unpaid')->sum('fine_amount'), 2))
                ->description(__('filament.widgets.outstanding_fines'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger'),
            Stat::make(__('messages.violations_this_week'), TrafficViolation::where('created_at', '>=', now()->startOfWeek())->count())
                ->description(__('filament.widgets.violations_issued_week'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }
}
