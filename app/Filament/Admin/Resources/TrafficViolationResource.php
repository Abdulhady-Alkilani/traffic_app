<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ViolationStatus;
use App\Filament\Admin\Resources\TrafficViolationResource\Pages;
use App\Models\TrafficViolation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrafficViolationResource extends Resource
{
    protected static ?string $model = TrafficViolation::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Traffic Violations';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Violation Details')
                    ->schema([
                        Forms\Components\TextInput::make('citizen.full_name')
                            ->label('Citizen')
                            ->disabled(),
                        Forms\Components\TextInput::make('vehicle.plate_number')
                            ->label('Vehicle Plate')
                            ->disabled(),
                        Forms\Components\TextInput::make('police.full_name')
                            ->label('Officer')
                            ->disabled(),
                        Forms\Components\TextInput::make('violation_type')
                            ->label('Violation Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('fine_amount')
                            ->label('Fine Amount (SAR)')
                            ->disabled(),
                        Forms\Components\TextInput::make('issued_at')
                            ->label('Issued At')
                            ->disabled(),
                        Forms\Components\TextInput::make('due_date')
                            ->label('Due Date')
                            ->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make('Update Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(ViolationStatus::class)
                            ->enum(ViolationStatus::class)
                            ->required(),
                    ]),
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
                Tables\Columns\TextColumn::make('police.full_name')
                    ->label('Officer')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ViolationStatus::class),
                Tables\Filters\Filter::make('issued_at')
                    ->form([
                        Forms\Components\DatePicker::make('issued_from'),
                        Forms\Components\DatePicker::make('issued_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['issued_from'], fn($q) => $q->whereDate('issued_at', '>=', $data['issued_from']))
                            ->when($data['issued_until'], fn($q) => $q->whereDate('issued_at', '<=', $data['issued_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrafficViolations::route('/'),
            'edit' => Pages\EditTrafficViolation::route('/{record}/edit'),
            'view' => Pages\ViewTrafficViolation::route('/{record}'),
        ];
    }
}
