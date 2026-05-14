<?php

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

    protected static ?string $navigationLabel = 'Traffic Violations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Violation Details')
                    ->schema([
                        Forms\Components\Select::make('citizen_id')
                            ->label('Citizen')
                            ->options(CitizenData::all()->pluck('full_name', 'id')->mapWithKeys(fn($name, $id) => [
                                $id => CitizenData::find($id)?->full_name . ' (' . CitizenData::find($id)?->national_id . ')',
                            ]))
                            ->searchable()
                            ->required()
                            ->live()
                            ->reactive(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(function (callable $get) {
                                $citizenId = $get('citizen_id');
                                if (!$citizenId) {
                                    return [];
                                }
                                return Vehicle::where('citizen_id', $citizenId)->pluck('plate_number', 'id');
                            })
                            ->searchable(),
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
                    ->label('Citizen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle Plate'),
                Tables\Columns\TextColumn::make('violation_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fine_amount')
                    ->label('Amount (SAR)')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(ViolationStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
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
