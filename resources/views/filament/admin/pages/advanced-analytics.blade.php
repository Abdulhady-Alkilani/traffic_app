@php
    $kpis = $this->kpis();
    $comparison = $compare ? $this->comparison() : ['metrics' => []];
    $metrics = $comparison['metrics'] ?? [];
    $trendData = $this->monthlyTrend();
    $bestRegions = $this->bestRegions();
    $worstRegions = $this->worstRegions();
    $forecastData = $this->forecast();
    $hotspotsData = $this->hotspots();
    $reportTypes = $this->reportTypeDistribution();
    $violationTypes = $this->violationTypeDistribution();

    $lowerIsBetter = [
        'avg_response_minutes' => true,
        'violation_rate_per_driver' => true,
        'outstanding_fines' => true,
        'total_violations' => true,
    ];

    $kpiCards = [
        'total_reports' => ['label' => __('analytics.kpi.total_reports'), 'icon' => 'heroicon-o-document-text', 'color' => 'blue', 'suffix' => ''],
        'total_violations' => ['label' => __('analytics.kpi.total_violations'), 'icon' => 'heroicon-o-exclamation-triangle', 'color' => 'red', 'suffix' => ''],
        'avg_response_minutes' => ['label' => __('analytics.kpi.avg_response'), 'icon' => 'heroicon-o-clock', 'color' => 'amber', 'suffix' => ' ' . __('analytics.kpi.minutes')],
        'resolution_rate' => ['label' => __('analytics.kpi.resolution_rate'), 'icon' => 'heroicon-o-check-badge', 'color' => 'green', 'suffix' => '%'],
        'violation_rate_per_driver' => ['label' => __('analytics.kpi.violation_rate'), 'icon' => 'heroicon-o-truck', 'color' => 'orange', 'suffix' => ''],
        'collection_rate' => ['label' => __('analytics.kpi.collection_rate'), 'icon' => 'heroicon-o-banknotes', 'color' => 'emerald', 'suffix' => '%'],
        'total_fines' => ['label' => __('analytics.kpi.total_fines'), 'icon' => 'heroicon-o-currency-dollar', 'color' => 'indigo', 'suffix' => ''],
        'outstanding_fines' => ['label' => __('analytics.kpi.outstanding_fines'), 'icon' => 'heroicon-o-receipt-percent', 'color' => 'rose', 'suffix' => ''],
    ];

    $trendCats = array_keys($trendData);
    $trendReports = array_map(fn ($r) => $r['reports'], array_values($trendData));
    $trendViolations = array_map(fn ($r) => $r['violations'], array_values($trendData));
    $trendOptions = [
        'chart' => ['type' => 'area', 'height' => 340, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit', 'zoom' => ['enabled' => false]],
        'series' => [
            ['name' => __('analytics.trends.reports'), 'data' => $trendReports],
            ['name' => __('analytics.trends.violations'), 'data' => $trendViolations],
        ],
        'xaxis' => ['categories' => $trendCats],
        'dataLabels' => ['enabled' => false],
        'stroke' => ['curve' => 'smooth', 'width' => 2],
        'colors' => ['#3b82f6', '#ef4444'],
        'fill' => ['type' => 'gradient', 'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.4, 'opacityTo' => 0.05]],
        'legend' => ['position' => 'top'],
    ];

    $histKeys = array_keys($forecastData['history']);
    $fcKeys = array_keys($forecastData['forecast']);
    $allKeys = array_merge($histKeys, $fcKeys);
    $histSeries = array_values($forecastData['history']);
    $fcVals = array_values($forecastData['forecast']);
    $historicalSeries = array_merge($histSeries, array_fill(0, count($fcKeys), null));
    $forecastSeries = array_merge(array_fill(0, max(0, count($histSeries) - 1), null), count($histSeries) > 0 ? [end($histSeries)] : [], $fcVals);
    $forecastOptions = [
        'chart' => ['type' => 'line', 'height' => 340, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'series' => [
            ['name' => __('analytics.predictions.historical'), 'data' => $historicalSeries],
            ['name' => __('analytics.predictions.forecast'), 'data' => $forecastSeries],
        ],
        'xaxis' => ['categories' => $allKeys],
        'stroke' => ['width' => [3, 3], 'dash' => [0, 6]],
        'colors' => ['#6366f1', '#f59e0b'],
        'dataLabels' => ['enabled' => false],
        'markers' => ['size' => 4],
        'legend' => ['position' => 'top'],
    ];

    $peakHours = $hotspotsData['peak_hours'];
    $hourCats = [];
    for ($h = 0; $h < 24; $h++) {
        $hourCats[] = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00';
    }
    $hoursOptions = [
        'chart' => ['type' => 'bar', 'height' => 320, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
        'series' => [['name' => __('analytics.hotspots.incidents'), 'data' => array_values($peakHours)]],
        'xaxis' => ['categories' => $hourCats],
        'colors' => ['#ef4444'],
        'dataLabels' => ['enabled' => false],
        'plotOptions' => ['bar' => ['borderRadius' => 4, 'columnWidth' => '60%']],
    ];

    $mapType = fn ($t) => __('messages.' . $t, ['' => $t]);
    $reportTypeLabels = array_map($mapType, array_keys($reportTypes));
    $violationTypeLabels = array_map($mapType, array_keys($violationTypes));

    $reportDonut = [
        'chart' => ['type' => 'donut', 'height' => 320, 'fontFamily' => 'inherit'],
        'series' => array_values($reportTypes),
        'labels' => count($reportTypeLabels) ? $reportTypeLabels : ['—'],
        'legend' => ['position' => 'bottom'],
        'colors' => ['#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6'],
    ];
    $violationDonut = [
        'chart' => ['type' => 'donut', 'height' => 320, 'fontFamily' => 'inherit'],
        'series' => array_values($violationTypes),
        'labels' => count($violationTypeLabels) ? $violationTypeLabels : ['—'],
        'legend' => ['position' => 'bottom'],
        'colors' => ['#ef4444', '#f97316', '#eab308', '#84cc16', '#06b6d4', '#8b5cf6'],
    ];
@endphp

<div>
<x-filament::section>
    <x-slot name="heading">{{ __('analytics.filters.title') }}</x-slot>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-3 w-full">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.filters.from') }}</label>
                <input type="date" wire:model.live="from" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('analytics.filters.to') }}</label>
                <input type="date" wire:model.live="to" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button wire:click="applyFilters" type="button" class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700 transition">
                    {{ __('analytics.filters.apply') }}
                </button>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @foreach (['last_30','this_month','last_month','last_quarter','ytd'] as $preset)
                <button wire:click="setPreset('{{ $preset }}')" type="button"
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 transition">
                    {{ __('analytics.filters.' . $preset) }}
                </button>
            @endforeach
        </div>
    </div>

    <label class="mt-4 inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
        <input type="checkbox" wire:model.live="compare" class="rounded border-gray-300 text-amber-600 shadow-sm">
        {{ __('analytics.filters.compare') }}
    </label>
</x-filament::section>

<x-filament::section>
    <x-slot name="heading">{{ __('analytics.sections.kpis') }}</x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($kpiCards as $key => $card)
            @php
                $value = $kpis[$key] ?? 0;
                $displayValue = in_array($key, ['total_fines','collected_fines','outstanding_fines'])
                    ? number_format((float) $value, 0)
                    : (is_float($value) || is_int($value) ? number_format((float) $value, ($key === 'avg_response_minutes' ? 1 : 0)) : $value);
                $metric = $metrics[$key] ?? null;
                $delta = $metric['percent'] ?? null;
                $lib = $lowerIsBetter[$key] ?? false;
                $isUp = ($delta ?? 0) > 0;
                $good = $delta === null ? null : ($lib ? !$isUp : $isUp);
                $deltaColor = $good === null ? 'gray' : ($good ? 'green' : 'red');
            @endphp
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
                    <span class="text-{{ $card['color'] }}-500"><x-filament::icon :icon="$card['icon']" class="h-5 w-5" /></span>
                </div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $displayValue }}{{ $card['suffix'] }}</div>
                @if ($delta !== null)
                    <div class="mt-1 text-xs font-medium text-{{ $deltaColor }}-600 dark:text-{{ $deltaColor }}-400">
                        {{ ($isUp ? '▲' : '▼') }} {{ abs((float) $delta) }}% {{ __('analytics.delta.vs_previous') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-filament::section>

<x-filament::section>
    <x-slot name="heading">{{ __('analytics.sections.trends') }}</x-slot>
    <div wire:key="trend-{{ $renderToken }}" x-data="apexChart(@js($trendOptions))"></div>
</x-filament::section>

@if ($compare && ! empty($metrics))
<x-filament::section>
    <x-slot name="heading">{{ __('analytics.sections.comparison') }}</x-slot>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.export.metric') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.delta.current') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.delta.previous') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.delta.change') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($metrics as $key => $m)
                    @php
                        $lib = $lowerIsBetter[$key] ?? false;
                        $pct = $m['percent'];
                        $isUp = ($pct ?? 0) > 0;
                        $good = $pct === null ? null : ($lib ? !$isUp : $isUp);
                        $color = $good === null ? 'gray' : ($good ? 'green' : 'red');
                        $label = $this->metricLabel($key);
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $label }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100 font-medium">{{ is_numeric($m['current']) ? number_format((float) $m['current'], 2) : $m['current'] }}</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ is_numeric($m['previous']) ? number_format((float) $m['previous'], 2) : $m['previous'] }}</td>
                        <td class="px-4 py-2">
                            @if ($pct === null)
                                <span class="text-gray-400">—</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/40 dark:text-{{ $color }}-300">
                                    {{ ($isUp ? '+' : '') . $pct }}%
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>
@endif

<x-filament::section>
    <x-slot name="heading">{{ __('analytics.sections.predictions') }}</x-slot>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
        @if ($forecastData['trend'] === 'up') {{ __('analytics.predictions.trend_up') }}
        @elseif ($forecastData['trend'] === 'down') {{ __('analytics.predictions.trend_down') }}
        @else {{ __('analytics.predictions.trend_stable') }} @endif
        — {{ __('analytics.predictions.forecast_note') }}
    </p>
    <div wire:key="forecast-{{ $renderToken }}" x-data="apexChart(@js($forecastOptions))"></div>

    @if (! empty($forecastData['forecast']))
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach ($forecastData['forecast'] as $month => $predicted)
            <div class="rounded-lg border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-900/20 p-3 text-center">
                <div class="text-xs text-amber-700 dark:text-amber-300">{{ $month }}</div>
                <div class="text-xl font-bold text-amber-900 dark:text-amber-100">{{ $predicted }}</div>
                <div class="text-xs text-amber-600 dark:text-amber-400">{{ __('analytics.hotspots.incidents') }}</div>
            </div>
        @endforeach
    </div>
    @endif
</x-filament::section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <x-filament::section>
        <x-slot name="heading">{{ __('analytics.regions.best') }}</x-slot>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($bestRegions as $region)
                <li class="py-2 flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 text-xs font-bold">{{ $loop->iteration }}</span>
                        {{ $region['region'] }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $region['incidents'] }} {{ __('analytics.hotspots.incidents') }}</span>
                </li>
            @empty
                <li class="py-2 text-gray-400">—</li>
            @endforelse
        </ul>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">{{ __('analytics.regions.worst') }}</x-slot>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($worstRegions as $region)
                <li class="py-2 flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 text-xs font-bold">{{ $loop->iteration }}</span>
                        {{ $region['region'] }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $region['incidents'] }} {{ __('analytics.hotspots.incidents') }}</span>
                </li>
            @empty
                <li class="py-2 text-gray-400">—</li>
            @endforelse
        </ul>
    </x-filament::section>
</div>

<x-filament::section>
    <x-slot name="heading">{{ __('analytics.regions.compliance_ranking') }}</x-slot>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.regions.region') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.regions.reports') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.regions.violations') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.regions.incidents') }}</th>
                    <th class="px-4 py-3 text-start font-semibold text-gray-600 dark:text-gray-300">{{ __('analytics.regions.resolution_rate') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($this->regionCompliance() as $region)
                    <tr>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $region['region'] }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $region['reports'] }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $region['violations'] }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $region['incidents'] }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">{{ $region['resolution_rate'] }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>

<x-filament::section>
    <x-slot name="heading">{{ __('analytics.hotspots.peak_hours') }}</x-slot>
    <div wire:key="hours-{{ $renderToken }}" x-data="apexChart(@js($hoursOptions))"></div>
</x-filament::section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <x-filament::section>
        <x-slot name="heading">{{ __('analytics.custom.type_reports') }}</x-slot>
        <div wire:key="donut-report-{{ $renderToken }}" x-data="apexChart(@js($reportDonut))"></div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">{{ __('analytics.custom.type_violations') }}</x-slot>
        <div wire:key="donut-violation-{{ $renderToken }}" x-data="apexChart(@js($violationDonut))"></div>
    </x-filament::section>
</div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js"></script>
    <script>
        (function () {
            function registerApex() {
                if (!window.Alpine) return;
                Alpine.data('apexChart', (options) => ({
                    chart: null,
                    init() {
                        const el = this.$el;
                        const render = () => {
                            if (!window.ApexCharts) { setTimeout(render, 100); return; }
                            el.innerHTML = '';
                            this.chart = new ApexCharts(el, options);
                            this.chart.render();
                        };
                        render();
                    },
                    destroy() {
                        if (this.chart) { try { this.chart.destroy(); } catch (e) {} this.chart = null; }
                    },
                }));
            }
            if (window.Alpine) { registerApex(); }
            document.addEventListener('alpine:init', registerApex);
        })();
    </script>
@endpush
