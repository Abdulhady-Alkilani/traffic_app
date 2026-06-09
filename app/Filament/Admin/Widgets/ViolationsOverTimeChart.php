<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\TrafficViolation;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ViolationsOverTimeChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return __('messages.violations_over_time') ?? 'المخالفات بمرور الوقت';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            
            $data[] = TrafficViolation::whereDate('issued_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'المخالفات',
                    'data' => $data,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
