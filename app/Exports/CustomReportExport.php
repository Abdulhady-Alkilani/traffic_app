<?php

declare(strict_types=1);

namespace App\Exports;

use App\Services\Analytics\AnalyticsService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomReportExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  string  $format  'xlsx' | 'csv'
     */
    public function __construct(
        protected array $rows,
        protected string $format = 'xlsx',
    ) {}

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return [
            __('analytics.export.id'),
            __('analytics.export.type'),
            __('analytics.export.citizen'),
            __('analytics.export.subtype'),
            __('analytics.export.fine'),
            __('analytics.export.status'),
            __('analytics.export.location'),
            __('analytics.export.date'),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function map($row): array
    {
        return [
            $row['id'] ?? '',
            $row['type'] ?? '',
            $row['citizen'] ?? '',
            $row['subtype'] ?? '',
            $row['fine'] ?? '',
            $row['status'] ?? '',
            $row['location'] ?? '',
            $row['date'] ?? '',
        ];
    }
}
