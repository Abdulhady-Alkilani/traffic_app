<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Enums\ReportStatus;
use App\Enums\ViolationStatus;
use App\Models\CitizenData;
use App\Models\Report;
use App\Models\TrafficViolation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AnalyticsService
{
    /**
     * Columns allowed for report distribution queries (guards against SQL injection).
     */
    private const REPORT_DISTRIBUTION_COLUMNS = ['report_type', 'status', 'assigned_department'];

    /**
     * Columns allowed for violation distribution queries (guards against SQL injection).
     */
    private const VIOLATION_DISTRIBUTION_COLUMNS = ['violation_type', 'status'];

    /** @var array<string, mixed> */
    private array $cache = [];

    /**
     * Key performance indicators for the given date range.
     *
     * @return array{
     *     total_reports: int,
     *     total_violations: int,
     *     avg_response_minutes: float,
     *     resolution_rate: float,
     *     violation_rate_per_driver: float,
     *     collection_rate: float,
     *     total_fines: float,
     *     collected_fines: float,
     *     outstanding_fines: float,
     * }
     */
    public function kpis(Carbon $start, Carbon $end): array
    {
        $key = $this->key('kpis', $start, $end);

        return $this->cache[$key] ??= [
            'total_reports' => $this->reportQuery($start, $end)->count(),
            'total_violations' => $this->violationQuery($start, $end)->count(),
            'avg_response_minutes' => $this->averageResponseMinutes($start, $end),
            'resolution_rate' => $this->resolutionRate($start, $end),
            'violation_rate_per_driver' => $this->violationRatePerDriver($start, $end),
            'collection_rate' => $this->collectionRate($start, $end),
            'total_fines' => (float) $this->violationQuery($start, $end)->sum('fine_amount'),
            'collected_fines' => (float) $this->violationQuery($start, $end)
                ->where('status', ViolationStatus::Paid->value)->sum('fine_amount'),
            'outstanding_fines' => (float) $this->violationQuery($start, $end)
                ->whereIn('status', [
                    ViolationStatus::Unpaid->value,
                    ViolationStatus::PendingVerification->value,
                ])->sum('fine_amount'),
        ];
    }

    /**
     * Average time (in minutes) between a report being created and resolved.
     */
    public function averageResponseMinutes(Carbon $start, Carbon $end): float
    {
        $resolved = $this->reportQuery($start, $end)
            ->where('status', ReportStatus::Resolved->value)
            ->get(['created_at', 'updated_at']);

        if ($resolved->isEmpty()) {
            return 0.0;
        }

        $totalSeconds = 0;
        foreach ($resolved as $report) {
            $totalSeconds += $report->updated_at->getTimestamp() - $report->created_at->getTimestamp();
        }

        return round(($totalSeconds / $resolved->count()) / 60, 2);
    }

    /**
     * Percentage of reports that were resolved (vs all non-rejected reports).
     */
    public function resolutionRate(Carbon $start, Carbon $end): float
    {
        $total = $this->reportQuery($start, $end)->count();

        if ($total === 0) {
            return 0.0;
        }

        $resolved = $this->reportQuery($start, $end)
            ->where('status', ReportStatus::Resolved->value)->count();

        return round(($resolved / $total) * 100, 2);
    }

    /**
     * Average number of violations per registered driver.
     */
    public function violationRatePerDriver(Carbon $start, Carbon $end): float
    {
        $drivers = CitizenData::count();

        if ($drivers === 0) {
            return 0.0;
        }

        return round($this->violationQuery($start, $end)->count() / $drivers, 2);
    }

    /**
     * Percentage of issued fines that have been collected.
     *
     * Canceled fines are excluded from the denominator so the rate reflects
     * only fines that are actually still actionable.
     */
    public function collectionRate(Carbon $start, Carbon $end): float
    {
        $total = $this->violationQuery($start, $end)
            ->where('status', '!=', ViolationStatus::Canceled->value)->count();

        if ($total === 0) {
            return 0.0;
        }

        $paid = $this->violationQuery($start, $end)
            ->where('status', ViolationStatus::Paid->value)->count();

        return round(($paid / $total) * 100, 2);
    }

    /**
     * Monthly incident trend (reports + violations) across the range.
     *
     * @return array<string, array{reports: int, violations: int, total: int}>
     */
    public function monthlyTrend(Carbon $start, Carbon $end): array
    {
        $key = $this->key('monthly', $start, $end);

        return $this->cache[$key] ??= (function () use ($start, $end) {
            $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->startOfMonth());
            $trend = [];

            foreach ($period as $month) {
                $trend[$month->format('Y-m')] = ['reports' => 0, 'violations' => 0, 'total' => 0];
            }

            $reports = $this->reportQuery($start, $end)
                ->selectRaw("substr(created_at, 1, 7) as month, COUNT(*) as aggregate")
                ->groupBy('month')->pluck('aggregate', 'month');

            $violations = $this->violationQuery($start, $end)
                ->selectRaw("substr(issued_at, 1, 7) as month, COUNT(*) as aggregate")
                ->groupBy('month')->pluck('aggregate', 'month');

            foreach ($trend as $month => &$row) {
                $row['reports'] = (int) ($reports[$month] ?? 0);
                $row['violations'] = (int) ($violations[$month] ?? 0);
                $row['total'] = $row['reports'] + $row['violations'];
            }

            return $trend;
        })();
    }

    /**
     * Distribution of reports by a given column.
     *
     * @return array<string, int>
     */
    public function reportDistribution(Carbon $start, Carbon $end, string $column): array
    {
        $this->guardColumn($column, self::REPORT_DISTRIBUTION_COLUMNS);

        return $this->reportQuery($start, $end)
            ->selectRaw("{$column}, COUNT(*) as aggregate")
            ->groupBy($column)
            ->orderByDesc('aggregate')
            ->pluck('aggregate', $column)
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * Distribution of violations by a given column.
     *
     * @return array<string, int>
     */
    public function violationDistribution(Carbon $start, Carbon $end, string $column): array
    {
        $this->guardColumn($column, self::VIOLATION_DISTRIBUTION_COLUMNS);

        return $this->violationQuery($start, $end)
            ->selectRaw("{$column}, COUNT(*) as aggregate")
            ->groupBy($column)
            ->orderByDesc('aggregate')
            ->pluck('aggregate', $column)
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * Region (city) compliance ranking. Best = most compliant, Worst = least.
     *
     * @return array<int, array{region: string, reports: int, violations: int, incidents: int, resolution_rate: float}>
     */
    public function regionCompliance(Carbon $start, Carbon $end): array
    {
        $key = $this->key('regions', $start, $end);

        return $this->cache[$key] ??= (function () use ($start, $end) {
            $reportRegions = $this->reportQuery($start, $end)->get(['location_text', 'status']);
            $violationRegions = $this->violationQuery($start, $end)->get(['description']);

            $regions = [];

            foreach ($reportRegions as $report) {
                $region = $this->extractRegion($report->location_text);
                $regions[$region] ??= ['reports' => 0, 'violations' => 0, 'resolved' => 0];
                $regions[$region]['reports']++;
                if ($report->status === ReportStatus::Resolved->value) {
                    $regions[$region]['resolved']++;
                }
            }

            foreach ($violationRegions as $violation) {
                $region = $this->extractRegion($violation->description);
                $regions[$region] ??= ['reports' => 0, 'violations' => 0, 'resolved' => 0];
                $regions[$region]['violations']++;
            }

            $ranked = [];
            foreach ($regions as $name => $data) {
                $incidents = $data['reports'] + $data['violations'];
                $ranked[] = [
                    'region' => $name,
                    'reports' => $data['reports'],
                    'violations' => $data['violations'],
                    'incidents' => $incidents,
                    'resolution_rate' => $data['reports'] > 0
                        ? round(($data['resolved'] / $data['reports']) * 100, 2)
                        : 0.0,
                ];
            }

            usort($ranked, fn ($a, $b) => $a['incidents'] <=> $b['incidents']);

            return $ranked;
        })();
    }

    public function bestRegions(Carbon $start, Carbon $end, int $limit = 3): array
    {
        return array_slice($this->regionCompliance($start, $end), 0, $limit);
    }

    public function worstRegions(Carbon $start, Carbon $end, int $limit = 3): array
    {
        return array_slice(array_reverse($this->regionCompliance($start, $end)), 0, $limit);
    }

    /**
     * Compare the given range against an equivalent previous range.
     *
     * @return array<string, mixed>
     */
    public function compareWithPrevious(Carbon $start, Carbon $end): array
    {
        $length = $start->diffInDays($end) + 1;
        $prevStart = $start->copy()->subDays($length);
        $prevEnd = $start->copy()->subSecond();

        $current = $this->kpis($start, $end);
        $previous = $this->kpis($prevStart, $prevEnd);

        $deltas = [];
        foreach ($current as $metric => $value) {
            $oldValue = $previous[$metric];
            $deltas[$metric] = [
                'current' => $value,
                'previous' => $oldValue,
                'absolute' => round((float) $value - (float) $oldValue, 2),
                'percent' => $oldValue > 0
                    ? round((((float) $value - (float) $oldValue) / (float) $oldValue) * 100, 2)
                    : null,
            ];
        }

        return [
            'current_range' => [$start, $end],
            'previous_range' => [$prevStart, $prevEnd],
            'metrics' => $deltas,
        ];
    }

    /**
     * Forecast future incidents using linear regression on historical monthly data.
     *
     * @return array{history: array<string, int>, forecast: array<string, int>, trend: string}
     */
    public function forecastIncidents(int $historyMonths = 12, int $forecastMonths = 3): array
    {
        $end = Carbon::now()->endOfMonth();
        $start = $end->copy()->subMonths($historyMonths - 1)->startOfMonth();

        $trend = $this->monthlyTrend($start, $end);

        $history = [];
        foreach ($trend as $month => $row) {
            $history[$month] = $row['total'];
        }

        $values = array_values($history);
        $forecast = [];
        $slope = 0.0;
        $intercept = 0.0;

        if (count($values) >= 2) {
            [$slope, $intercept] = $this->linearRegression($values);

            $lastIndex = count($values) - 1;
            $lastMonth = Carbon::createFromFormat('Y-m', array_key_last($history))->startOfMonth();

            for ($i = 1; $i <= $forecastMonths; $i++) {
                $predicted = (int) max(0, round($intercept + $slope * ($lastIndex + $i)));
                $forecast[$lastMonth->copy()->addMonths($i)->format('Y-m')] = $predicted;
            }
        }

        $trendDirection = count($values) < 2 ? 'stable'
            : ($slope > 0 ? 'up' : ($slope < 0 ? 'down' : 'stable'));

        return [
            'history' => $history,
            'forecast' => $forecast,
            'trend' => $trendDirection,
        ];
    }

    /**
     * Identify the most dangerous hours of day and hotspots.
     *
     * @return array{peak_hours: array<int, int>, top_hotspots: array<int, array{region: string, incidents: int}>}
     */
    public function hotspots(Carbon $start, Carbon $end): array
    {
        $key = $this->key('hotspots', $start, $end);

        return $this->cache[$key] ??= (function () use ($start, $end) {
            $hours = array_fill(0, 24, 0);

            $reportHours = $this->reportQuery($start, $end)
                ->selectRaw("substr(created_at, 12, 2) as hour, COUNT(*) as aggregate")
                ->groupBy('hour')->pluck('aggregate', 'hour');

            $violationHours = $this->violationQuery($start, $end)
                ->selectRaw("substr(issued_at, 12, 2) as hour, COUNT(*) as aggregate")
                ->groupBy('hour')->pluck('aggregate', 'hour');

            foreach ($reportHours as $hour => $count) {
                $hours[(int) $hour] += (int) $count;
            }

            foreach ($violationHours as $hour => $count) {
                $hours[(int) $hour] += (int) $count;
            }

            $regions = $this->regionCompliance($start, $end);
            $hotspots = array_map(fn ($r) => [
                'region' => $r['region'],
                'incidents' => $r['incidents'],
            ], $regions);

            usort($hotspots, fn ($a, $b) => $b['incidents'] <=> $a['incidents']);

            return [
                'peak_hours' => $hours,
                'top_hotspots' => array_slice($hotspots, 0, 5),
            ];
        })();
    }

    /**
     * Build a custom report dataset from user-defined criteria.
     *
     * @param  array{type?: string, status?: string|null, from?: string|null, to?: string|null}  $criteria
     * @return array<int, array<string, mixed>>
     */
    public function customReport(array $criteria): array
    {
        $from = isset($criteria['from']) ? Carbon::parse($criteria['from'])->startOfDay() : Carbon::minValue();
        $to = isset($criteria['to']) ? Carbon::parse($criteria['to'])->endOfDay() : Carbon::maxValue();
        $type = $criteria['type'] ?? 'reports';

        return match ($type) {
            'violations' => $this->customViolations($from, $to, $criteria['status'] ?? null),
            'incidents' => $this->customIncidents($from, $to),
            default => $this->customReports($from, $to, $criteria['status'] ?? null),
        };
    }

    protected function customReports(Carbon $from, Carbon $to, ?string $status): array
    {
        $query = Report::whereBetween('created_at', [$from, $to])->with(['citizen', 'vehicle']);
        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->get()->map(fn (Report $r) => [
            'id' => $r->id,
            'type' => 'report',
            'citizen' => $r->citizen?->full_name,
            'subtype' => $r->report_type,
            'department' => $r->assigned_department,
            'status' => $r->status instanceof \BackedEnum && method_exists($r->status, 'getLabel') ? $r->status->getLabel() : ($r->status->value ?? $r->status),
            'location' => $r->location_text,
            'date' => $r->created_at?->format('Y-m-d H:i'),
        ])->all();
    }

    protected function customViolations(Carbon $from, Carbon $to, ?string $status): array
    {
        $query = TrafficViolation::whereBetween('issued_at', [$from, $to])->with(['citizen', 'vehicle']);
        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('issued_at')->get()->map(fn (TrafficViolation $v) => [
            'id' => $v->id,
            'type' => 'violation',
            'citizen' => $v->citizen?->full_name,
            'subtype' => $v->violation_type,
            'fine' => (float) $v->fine_amount,
            'status' => $v->status instanceof \BackedEnum && method_exists($v->status, 'getLabel') ? $v->status->getLabel() : ($v->status->value ?? $v->status),
            'location' => null,
            'date' => $v->issued_at?->format('Y-m-d H:i'),
        ])->all();
    }

    protected function customIncidents(Carbon $from, Carbon $to): array
    {
        return array_merge(
            $this->customReports($from, $to, null),
            $this->customViolations($from, $to, null),
        );
    }

    /**
     * Extract a normalized region/city name from free-text location data.
     */
    public function extractRegion(?string $text): string
    {
        if (blank($text)) {
            return __('analytics.regions.unknown');
        }

        $cities = [
            'دمشق' => 'damascus',
            'ريف دمشق' => 'rural_damascus',
            'حلب' => 'aleppo',
            'حمص' => 'homs',
            'حماة' => 'hama',
            'اللاذقية' => 'latakia',
            'طرطوس' => 'tartus',
            'Damascus' => 'damascus',
            'Aleppo' => 'aleppo',
            'Homs' => 'homs',
            'Hama' => 'hama',
            'Latakia' => 'latakia',
            'Tartus' => 'tartus',
        ];

        foreach ($cities as $needle => $key) {
            if (mb_strpos($text, $needle) !== false) {
                return __('analytics.regions.' . $key);
            }
        }

        return __('analytics.regions.unknown');
    }

    /**
     * Least-squares linear regression.
     *
     * @param  array<int, int|float>  $values
     * @return array{0: float, 1: float} [slope, intercept]
     */
    protected function linearRegression(array $values): array
    {
        $n = count($values);
        $sumX = $sumY = $sumXY = $sumX2 = 0.0;

        foreach ($values as $x => $y) {
            $sumX += $x;
            $sumY += (float) $y;
            $sumXY += $x * (float) $y;
            $sumX2 += $x * $x;
        }

        $denominator = ($n * $sumX2 - $sumX * $sumX);
        $slope = $denominator != 0 ? ($n * $sumXY - $sumX * $sumY) / $denominator : 0.0;
        $intercept = ($sumY - $slope * $sumX) / $n;

        return [$slope, $intercept];
    }

    protected function reportQuery(Carbon $start, Carbon $end): \Illuminate\Database\Eloquent\Builder
    {
        return Report::query()->whereBetween('created_at', [$start, $end]);
    }

    protected function violationQuery(Carbon $start, Carbon $end): \Illuminate\Database\Eloquent\Builder
    {
        return TrafficViolation::query()->whereBetween('issued_at', [$start, $end]);
    }

    /**
     * Ensure a column is part of an allow-list before using it in a raw query.
     *
     * @param  array<int, string>  $allowed
     *
     * @throws \InvalidArgumentException
     */
    protected function guardColumn(string $column, array $allowed): void
    {
        if (! in_array($column, $allowed, true)) {
            throw new \InvalidArgumentException("Distribution column [{$column}] is not allowed.");
        }
    }

    protected function key(string $method, Carbon $start, Carbon $end): string
    {
        return $method . ':' . $start->toDateTimeString() . '|' . $end->toDateTimeString();
    }
}
