<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\Department;
use App\Models\Report;
use Filament\Widgets\ChartWidget;

class ReportsByDepartmentChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('filament.widgets.reports_by_department');
    }

    protected function getData(): array
    {
        $data = Report::query()
            ->selectRaw('assigned_department, COUNT(*) as count')
            ->groupBy('assigned_department')
            ->pluck('count', 'assigned_department')
            ->toArray();

        $labels = [];
        $values = [];
        $colors = ['#F59E0B', '#3B82F6', '#EF4444'];

        foreach (Department::cases() as $department) {
            $labels[] = $department->label();
            $values[] = $data[$department->value] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
