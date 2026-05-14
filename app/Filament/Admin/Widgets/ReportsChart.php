<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;

class ReportsChart extends ChartWidget
{
    protected static ?string $heading = 'Reports Over Time';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Report::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Reports',
                    'data' => array_values($data),
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
