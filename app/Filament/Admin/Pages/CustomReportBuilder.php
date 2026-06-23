<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Enums\ReportStatus;
use App\Enums\ViolationStatus;
use App\Exports\CustomReportExport;
use App\Services\Analytics\AnalyticsService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class CustomReportBuilder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.admin.pages.custom-report-builder';

    public string $reportType = 'reports';

    public ?string $statusFilter = null;

    public ?string $from = null;

    public ?string $to = null;

    public bool $built = false;

    /** @var array<int, array<string, mixed>> */
    public array $results = [];

    protected AnalyticsService $service;

    public function boot(): void
    {
        $this->service = app(AnalyticsService::class);
    }

    public function mount(): void
    {
        $this->from = now()->startOfYear()->toDateString();
        $this->to = now()->toDateString();
    }

    public static function getNavigationLabel(): string
    {
        return __('analytics.custom_reports_nav');
    }

    public static function getNavigationGroup(): string
    {
        return __('analytics.navigation');
    }

    public function getHeading(): string
    {
        return __('analytics.custom_reports');
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        $options = ['' => __('analytics.custom.all')];

        if ($this->reportType === 'violations') {
            foreach (ViolationStatus::cases() as $status) {
                $options[$status->value] = $status->getLabel();
            }
        } else {
            foreach (ReportStatus::cases() as $status) {
                $options[$status->value] = $status->getLabel();
            }
        }

        return $options;
    }

    public function build(): void
    {
        $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'reportType' => ['required', 'in:reports,violations,incidents'],
        ]);

        $this->results = $this->service->customReport([
            'type' => $this->reportType,
            'status' => $this->statusFilter ?: null,
            'from' => $this->from,
            'to' => $this->to,
        ]);

        $this->built = true;
    }

    public function updatedReportType(): void
    {
        $this->statusFilter = null;
    }

    public function resetFilters(): void
    {
        $this->reportType = 'reports';
        $this->statusFilter = null;
        $this->from = now()->startOfYear()->toDateString();
        $this->to = now()->toDateString();
        $this->results = [];
        $this->built = false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label(__('analytics.export.excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn () => Excel::download(
                    new CustomReportExport($this->runQuery(), 'xlsx'),
                    'custom-report.xlsx',
                )),
            Action::make('exportCsv')
                ->label(__('analytics.export.csv'))
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->action(fn () => Excel::download(
                    new CustomReportExport($this->runQuery(), 'csv'),
                    'custom-report.csv',
                )),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function runQuery(): array
    {
        return $this->service->customReport([
            'type' => $this->reportType,
            'status' => $this->statusFilter ?: null,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}
