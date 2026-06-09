<?php

declare(strict_types=1);

namespace App\Filament\Police\Resources;

use App\Enums\ViolationStatus;
use App\Filament\Police\Resources\TrafficViolationResource\Pages;
use App\Models\CitizenData;
use App\Models\TrafficViolation;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrafficViolationResource extends Resource
{
    protected static ?string $model = TrafficViolation::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getNavigationLabel(): string
    {
        return __('messages.violations');
    }

    public static function getModelLabel(): string
    {
        return __('messages.violation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.violations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.violation_details'))
                    ->schema([
                        Forms\Components\Select::make('citizen_id')
                            ->label(__('filament.columns.citizen'))
                            ->options(
                                CitizenData::query()
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(fn(CitizenData $citizen) => [
                                        $citizen->id => "{$citizen->full_name} ({$citizen->national_id})",
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->reactive(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label(__('messages.vehicle'))
                            ->options(function (callable $get) {
                                $citizenId = $get('citizen_id');
                                if (!$citizenId) {
                                    return [];
                                }
                                return Vehicle::where('citizen_id', $citizenId)->pluck('plate_number', 'id');
                            })
                            ->searchable(),
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
                            ->label(__('filament.columns.fine_amount'))
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('messages.due_date'))
                            ->minDate(now())
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('citizen.full_name')
                    ->label(__('filament.columns.citizen'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label(__('messages.plate_number')),
                Tables\Columns\TextColumn::make('violation_type')
                    ->label(__('messages.violation_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fine_amount')
                    ->label(__('messages.fine_amount') . ' (SYP)')
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ViolationStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label(__('messages.issued_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options(ViolationStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user && $user->policeData) {
                    $query->where('police_id', $user->policeData->id);
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrafficViolations::route('/'),
            'create' => Pages\CreateTrafficViolation::route('/create'),
            'edit' => Pages\EditTrafficViolation::route('/{record}/edit'),
        ];
    }
}
