<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources\AssignedReportResource\Pages;

use App\Enums\ViolationStatus;
use App\Filament\Police\Resources\AssignedReportResource;
use App\Services\ViolationService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignedReport extends ViewRecord
{
    protected static string $resource = AssignedReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('issueViolation')
                ->label(__('messages.issue_violation'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->modalHeading(__('messages.issue_violation'))
                ->modalSubmitActionLabel(__('filament.actions.create'))
                ->form([
                    Forms\Components\Hidden::make('citizen_id')
                        ->default($this->record->citizen_id),
                    Forms\Components\Hidden::make('vehicle_id')
                        ->default($this->record->vehicle_id),
                    Forms\Components\Hidden::make('report_id')
                        ->default($this->record->id),
                    Forms\Components\Select::make('violation_type')
                        ->label(__('messages.violation_type'))
                        ->options([
                            'speeding' => __('messages.speeding'),
                            'reckless_driving' => __('messages.reckless_driving'),
                            'red_light' => __('messages.red_light'),
                            'illegal_parking' => __('messages.illegal_parking'),
                            'no_seatbelt' => __('messages.no_seatbelt'),
                            'using_phone' => __('messages.using_phone'),
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label(__('messages.description'))
                        ->maxLength(500),
                    Forms\Components\TextInput::make('fine_amount')
                        ->label(__('messages.fine_amount') . ' (SAR)')
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Forms\Components\DatePicker::make('due_date')
                        ->label(__('messages.due_date'))
                        ->minDate(now())
                        ->required(),
                ])
                ->action(function (array $data, ViolationService $violationService) {
                    $violationService->issueFromReport(
                        $this->record,
                        auth()->user()->policeData,
                        $data
                    );

                    Notification::make()
                        ->title(__('messages.violation_issued_success'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
