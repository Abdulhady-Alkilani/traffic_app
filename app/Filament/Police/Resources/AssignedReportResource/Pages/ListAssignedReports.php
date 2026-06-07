<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\AssignedReportResource\Pages;

use App\Filament\Police\Resources\AssignedReportResource;
use Filament\Resources\Pages\ListRecords;

class ListAssignedReports extends ListRecords
{
    protected static string $resource = AssignedReportResource::class;
}
