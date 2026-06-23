<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ReportResource\Pages;

use App\Filament\Admin\Resources\ReportResource;
use App\Services\ReportAiAnalyzer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reanalyze')
                ->label(__('messages.ai_reanalyze'))
                ->icon('heroicon-o-cpu-chip')
                ->color('info')
                ->requiresConfirmation()
                ->action(function (ReportAiAnalyzer $analyzer) {
                    $analyzer->analyze($this->record);

                    Notification::make()
                        ->title(__('messages.ai_analysis'))
                        ->body($this->record->ai_summary ?? __('messages.ai_no_analysis'))
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'ai_severity_score',
                        'ai_detected_plate',
                        'ai_incident_type',
                        'ai_damage_assessment',
                        'ai_summary',
                        'ai_is_duplicate',
                        'ai_duplicate_of',
                        'ai_analyzed_at',
                    ]);
                }),
        ];
    }
}
