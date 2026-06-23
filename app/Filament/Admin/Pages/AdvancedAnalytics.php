<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Exports\AnalyticsCsvExport;
use App\Exports\AnalyticsExport;
use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;

class AdvancedAnalytics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.advanced-analytics';

    public ?string $from = null;

    public ?string $to = null;

    public bool $compare = true;

    public int $renderToken = 0;

    protected AnalyticsService $service;

    public function boot(): void
    {
        $this->service = app(AnalyticsService::class);

        $tempDir = storage_path('app/private/mpdf');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
    }

    public function mount(): void
    {
        $this->from = Carbon::now()->startOfYear()->toDateString();
        $this->to = Carbon::now()->toDateString();
        $this->renderToken = now()->timestamp;
    }

    public static function getNavigationLabel(): string
    {
        return __('analytics.navigation');
    }

    public static function getNavigationGroup(): string
    {
        return __('analytics.navigation');
    }

    public function getHeading(): string
    {
        return __('analytics.title');
    }

    public function getSubheading(): string
    {
        return __('analytics.subtitle');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')
                    ->label(__('analytics.filters.from'))
                    ->native(false),
                DatePicker::make('to')
                    ->label(__('analytics.filters.to'))
                    ->native(false),
                Toggle::make('compare')
                    ->label(__('analytics.filters.compare'))
                    ->default(true),
            ])
            ->statePath(null);
    }

    public function applyFilters(): void
    {
        $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $this->renderToken++;
    }

    public function setPreset(string $preset): void
    {
        $now = Carbon::now();

        [$this->from, $this->to] = match ($preset) {
            'last_30' => [$now->copy()->subDays(29)->toDateString(), $now->toDateString()],
            'this_month' => [$now->copy()->startOfMonth()->toDateString(), $now->toDateString()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth()->toDateString(), $now->copy()->subMonth()->endOfMonth()->toDateString()],
            'last_quarter' => [$now->copy()->subMonths(2)->startOfMonth()->toDateString(), $now->toDateString()],
            'this_year', 'ytd' => [$now->copy()->startOfYear()->toDateString(), $now->toDateString()],
            default => [$now->copy()->startOfYear()->toDateString(), $now->toDateString()],
        };

        $this->renderToken++;
    }

    public function start(): Carbon
    {
        return Carbon::parse($this->from)->startOfDay();
    }

    public function end(): Carbon
    {
        return Carbon::parse($this->to)->endOfDay();
    }

    /**
     * @return array<string, mixed>
     */
    public function kpis(): array
    {
        return $this->service->kpis($this->start(), $this->end());
    }

    public function comparison(): array
    {
        return $this->service->compareWithPrevious($this->start(), $this->end());
    }

    public function monthlyTrend(): array
    {
        return $this->service->monthlyTrend($this->start(), $this->end());
    }

    public function bestRegions(): array
    {
        return $this->service->bestRegions($this->start(), $this->end(), 3);
    }

    public function worstRegions(): array
    {
        return $this->service->worstRegions($this->start(), $this->end(), 3);
    }

    public function forecast(): array
    {
        return $this->service->forecastIncidents(12, 3);
    }

    public function hotspots(): array
    {
        return $this->service->hotspots($this->start(), $this->end());
    }

    public function regionCompliance(): array
    {
        return $this->service->regionCompliance($this->start(), $this->end());
    }

    public function metricLabel(string $key): string
    {
        return [
            'total_reports' => __('analytics.kpi.total_reports'),
            'total_violations' => __('analytics.kpi.total_violations'),
            'avg_response_minutes' => __('analytics.kpi.avg_response'),
            'resolution_rate' => __('analytics.kpi.resolution_rate'),
            'violation_rate_per_driver' => __('analytics.kpi.violation_rate'),
            'collection_rate' => __('analytics.kpi.collection_rate'),
            'total_fines' => __('analytics.kpi.total_fines'),
            'collected_fines' => __('analytics.kpi.collected_fines'),
            'outstanding_fines' => __('analytics.kpi.outstanding_fines'),
        ][$key] ?? $key;
    }

    public function reportTypeDistribution(): array
    {
        return $this->service->reportDistribution($this->start(), $this->end(), 'report_type');
    }

    public function violationTypeDistribution(): array
    {
        return $this->service->violationDistribution($this->start(), $this->end(), 'violation_type');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label(__('analytics.export.excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn () => Excel::download(
                    new AnalyticsExport($this->service, $this->start(), $this->end(), $this->compare),
                    $this->exportFilename('xlsx'),
                )),
            Action::make('exportCsv')
                ->label(__('analytics.export.csv'))
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->action(fn () => Excel::download(
                    new AnalyticsCsvExport($this->service, $this->start(), $this->end()),
                    $this->exportFilename('csv'),
                )),
            Action::make('exportPdf')
                ->label(__('analytics.export.pdf'))
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function () {
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'default_font' => 'dejavusans',
                        'tempDir' => storage_path('app/private/mpdf'),
                    ]);
                    $mpdf->SetDirectionality('rtl');
                    $mpdf->SetDisplayMode('fullpage');
                    $mpdf->autoScriptToLang = true;
                    $mpdf->autoLangToFont = true;
                    $mpdf->WriteHTML(view('filament.analytics.report-pdf', $this->pdfData())->render());

                    return response()->streamDownload(
                        fn () => print($mpdf->Output('', 'S')),
                        $this->exportFilename('pdf'),
                    );
                }),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function pdfData(): array
    {
        return [
            'kpis' => $this->kpis(),
            'comparison' => $this->compare ? $this->comparison() : [],
            'trend' => $this->monthlyTrend(),
            'regions' => $this->service->regionCompliance($this->start(), $this->end()),
            'forecast' => $this->forecast(),
            'from' => $this->from,
            'to' => $this->to,
            'generatedAt' => Carbon::now(),
        ];
    }

    protected function exportFilename(string $extension): string
    {
        return 'traffic-analytics-' . $this->from . '_to_' . $this->to . '.' . $extension;
    }
}
