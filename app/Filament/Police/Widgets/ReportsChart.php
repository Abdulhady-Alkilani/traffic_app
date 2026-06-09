<?php

namespace App\Filament\Police\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReportsChart extends ChartWidget
{
    protected static ?string $heading = 'حالة البلاغات في القسم';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        $policeData = $user->policeData;

        if (!$policeData) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $department = $policeData->department;

        $reportsByStatus = Report::where('assigned_department', $department)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];
        
        $statusTranslations = [
            'new' => ['label' => __('messages.new') ?? 'جديد', 'color' => '#f59e0b'],
            'in_progress' => ['label' => __('messages.in_progress') ?? 'قيد المعالجة', 'color' => '#3b82f6'],
            'resolved' => ['label' => __('messages.resolved') ?? 'محلول', 'color' => '#10b981'],
            'rejected' => ['label' => __('messages.rejected') ?? 'مرفوض', 'color' => '#ef4444'],
        ];

        foreach ($reportsByStatus as $status => $count) {
            $labels[] = $statusTranslations[$status]['label'] ?? $status;
            $data[] = $count;
            $colors[] = $statusTranslations[$status]['color'] ?? '#6b7280';
        }

        return [
            'datasets' => [
                [
                    'label' => 'البلاغات',
                    'data' => $data,
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
