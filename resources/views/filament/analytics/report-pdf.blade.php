@php
    $metrics = $comparison['metrics'] ?? [];
    $trend = $trend ?? [];
    $regions = $regions ?? [];
    $forecast = $forecast ?? [];
    $kpis = $kpis ?? [];
    $labelMap = [
        'total_reports' => __('analytics.kpi.total_reports'),
        'total_violations' => __('analytics.kpi.total_violations'),
        'avg_response_minutes' => __('analytics.kpi.avg_response'),
        'resolution_rate' => __('analytics.kpi.resolution_rate'),
        'violation_rate_per_driver' => __('analytics.kpi.violation_rate'),
        'collection_rate' => __('analytics.kpi.collection_rate'),
        'total_fines' => __('analytics.kpi.total_fines'),
        'collected_fines' => __('analytics.kpi.collected_fines'),
        'outstanding_fines' => __('analytics.kpi.outstanding_fines'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'dejavusans', sans-serif; }
        body { color: #1f2937; font-size: 12px; line-height: 1.6; margin: 0; }
        .header { text-align: center; border-bottom: 3px solid #f59e0b; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { color: #b45309; margin: 0 0 4px; font-size: 20px; }
        .header p { margin: 0; color: #6b7280; font-size: 11px; }
        h2 { color: #b45309; font-size: 14px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #fef3c7; color: #92400e; text-align: start; padding: 6px 8px; font-size: 11px; border: 1px solid #fde68a; }
        td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
        .kpis { width: 100%; }
        .kpis td { border: 1px solid #e5e7eb; padding: 8px; }
        .kpi-label { color: #6b7280; font-size: 10px; }
        .kpi-value { font-size: 16px; font-weight: bold; color: #111827; }
        .footer { margin-top: 24px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .up { color: #16a34a; } .down { color: #dc2626; } .neutral { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('analytics.export.report_title') }}</h1>
        <p>{{ __('analytics.export.date_range') }}: {{ $from }} — {{ $to }}</p>
        <p>{{ __('analytics.export.generated_at') }}: {{ $generatedAt->format('Y-m-d H:i') }}</p>
    </div>

    <h2>{{ __('analytics.sections.kpis') }}</h2>
    <table class="kpis">
        @foreach (array_chunk($labelMap, 3, true) as $chunk)
            <tr>
                @foreach ($chunk as $key => $label)
                    <td style="width:33%">
                        <div class="kpi-label">{{ $label }}</div>
                        <div class="kpi-value">
                            @if (in_array($key, ['total_fines','collected_fines','outstanding_fines']))
                                {{ number_format((float)($kpis[$key] ?? 0), 0) }}
                            @elseif ($key === 'avg_response_minutes')
                                {{ number_format((float)($kpis[$key] ?? 0), 1) }}
                            @else
                                {{ $kpis[$key] ?? 0 }}
                            @endif
                        </div>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>

    @if (! empty($metrics))
    <h2>{{ __('analytics.sections.comparison') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('analytics.export.metric') }}</th>
                <th>{{ __('analytics.delta.current') }}</th>
                <th>{{ __('analytics.delta.previous') }}</th>
                <th>{{ __('analytics.export.change_percent') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($metrics as $key => $m)
                @php $pct = $m['percent']; @endphp
                <tr>
                    <td>{{ $labelMap[$key] ?? $key }}</td>
                    <td>{{ is_numeric($m['current']) ? number_format((float) $m['current'], 2) : $m['current'] }}</td>
                    <td>{{ is_numeric($m['previous']) ? number_format((float) $m['previous'], 2) : $m['previous'] }}</td>
                    <td class="{{ $pct === null ? 'neutral' : ($pct >= 0 ? 'up' : 'down') }}">
                        {{ $pct === null ? '—' : (($pct >= 0 ? '+' : '') . $pct . '%') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>{{ __('analytics.sections.trends') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('analytics.export.month') }}</th>
                <th>{{ __('analytics.export.reports') }}</th>
                <th>{{ __('analytics.export.violations') }}</th>
                <th>{{ __('analytics.export.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trend as $month => $row)
                <tr>
                    <td>{{ $month }}</td>
                    <td>{{ $row['reports'] }}</td>
                    <td>{{ $row['violations'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('analytics.sections.predictions') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('analytics.export.month') }}</th>
                <th>{{ __('analytics.predictions.forecast') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forecast['forecast'] ?? [] as $month => $count)
                <tr><td>{{ $month }}</td><td>{{ $count }}</td></tr>
            @empty
                <tr><td colspan="2">{{ __('analytics.custom.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>{{ __('analytics.sections.regions') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('analytics.export.region') }}</th>
                <th>{{ __('analytics.export.reports') }}</th>
                <th>{{ __('analytics.export.violations') }}</th>
                <th>{{ __('analytics.export.incidents') }}</th>
                <th>{{ __('analytics.export.resolution_rate') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regions as $region)
                <tr>
                    <td>{{ $region['region'] }}</td>
                    <td>{{ $region['reports'] }}</td>
                    <td>{{ $region['violations'] }}</td>
                    <td>{{ $region['incidents'] }}</td>
                    <td>{{ $region['resolution_rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ config('app.name') }} — {{ __('analytics.export.report_title') }}</div>
</body>
</html>
