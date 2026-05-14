<?php

namespace App\Filament\Police\Resources\AssignedReportResource\Pages;

use App\Enums\ViolationStatus;
use App\Filament\Police\Resources\AssignedReportResource;
use App\Models\TrafficViolation;
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
                ->label('Issue Violation')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->modalHeading('Issue Violation')
                ->modalSubmitActionLabel('Issue')
                ->form([
                    Forms\Components\Select::make('violation_type')
                        ->label('Violation Type')
                        ->options([
                            'speeding' => 'Speeding',
                            'reckless_driving' => 'Reckless Driving',
                            'red_light' => 'Red Light',
                            'illegal_parking' => 'Illegal Parking',
                            'no_seatbelt' => 'No Seatbelt',
                            'using_phone' => 'Using Phone',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('fine_amount')
                        ->label('Fine Amount (SAR)')
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Due Date')
                        ->minDate(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    TrafficViolation::create([
                        'citizen_id' => $this->record->citizen_id,
                        'vehicle_id' => $this->record->vehicle_id,
                        'police_id' => auth()->user()->policeData->id,
                        'report_id' => $this->record->id,
                        'violation_type' => $data['violation_type'],
                        'description' => $data['description'],
                        'fine_amount' => $data['fine_amount'],
                        'due_date' => $data['due_date'],
                        'status' => ViolationStatus::Unpaid,
                        'issued_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Violation issued successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}
