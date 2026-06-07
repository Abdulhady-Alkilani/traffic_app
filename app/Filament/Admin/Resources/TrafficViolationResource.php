<?php

declare(strict_types=1);

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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.violation_details'))
                    ->schema([
                        Forms\Components\TextInput::make('citizen.full_name')
                            ->label(__('filament.columns.citizen'))
                            ->disabled(),
                        Forms\Components\TextInput::make('vehicle.plate_number')
                            ->label(__('messages.plate_number'))
                            ->disabled(),
                        Forms\Components\TextInput::make('police.full_name')
                            ->label(__('messages.officer'))
                            ->disabled(),
                        Forms\Components\TextInput::make('violation_type')
                            ->label(__('messages.violation_type'))
                            ->disabled(),
                        Forms\Components\TextInput::make('fine_amount')
                            ->label(__('messages.fine_amount') . ' (SAR)')
                            ->disabled(),
                        Forms\Components\TextInput::make('issued_at')
                            ->label(__('messages.issued_at'))
                            ->disabled(),
                        Forms\Components\TextInput::make('due_date')
                            ->label(__('messages.due_date'))
                            ->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make(__('filament.sections.update_status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('messages.status'))
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
                    ->label(__('filament.columns.citizen'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('citizen.national_id')
                    ->label(__('messages.national_id'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label(__('messages.plate_number')),
                Tables\Columns\TextColumn::make('police.full_name')
                    ->label(__('messages.officer'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('violation_type')
                    ->label(__('messages.violation_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fine_amount')
                    ->label(__('messages.fine_amount') . ' (SAR)')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ViolationStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label(__('messages.issued_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('messages.due_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options(ViolationStatus::class),
                Tables\Filters\Filter::make('issued_at')
                    ->form([
                        Forms\Components\DatePicker::make('issued_from')
                            ->label(__('filament.filters.from')),
                        Forms\Components\DatePicker::make('issued_until')
                            ->label(__('filament.filters.until')),
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
