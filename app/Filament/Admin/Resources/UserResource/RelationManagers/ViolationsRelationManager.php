<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Enums\ViolationStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ViolationsRelationManager extends RelationManager
{
    protected static string $relationship = 'violations';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->isCitizen();
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.violations');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.violation_details'))
                    ->schema([
                        Forms\Components\Select::make('citizen_id')
                            ->relationship('citizen', 'full_name')
                            ->label(__('filament.columns.citizen'))
                            ->default(fn (\Filament\Resources\RelationManagers\RelationManager $livewire) => $livewire->getOwnerRecord()->citizenData?->id)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label(__('messages.plate_number'))
                            ->options(function (callable $get, \Filament\Resources\RelationManagers\RelationManager $livewire) {
                                $citizenId = $get('citizen_id') ?? $livewire->getOwnerRecord()->citizenData?->id;
                                if (!$citizenId) {
                                    return [];
                                }
                                return \App\Models\Vehicle::where('citizen_id', $citizenId)->pluck('plate_number', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('police_id')
                            ->relationship('police', 'full_name')
                            ->label(__('messages.officer'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('violation_type')
                            ->label(__('messages.violation_type'))
                            ->required(),
                        Forms\Components\TextInput::make('fine_amount')
                            ->label(__('messages.fine_amount') . ' (SYP)')
                            ->required()
                            ->numeric(),
                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label(__('messages.issued_at'))
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('messages.due_date'))
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make(__('filament.sections.update_status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('messages.status'))
                            ->options(ViolationStatus::getSelectOptions())
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
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
                    ->badge(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('messages.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('police.full_name')
                    ->label(__('messages.officer'))
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
