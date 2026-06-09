<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficViolationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CitizenRelationManager extends RelationManager
{
    protected static string $relationship = 'citizen';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('filament.columns.owner') ?? 'صاحب المخالفة';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('national_id')
                    ->label(__('filament.columns.national_id'))
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('full_name')
                    ->label(__('filament.columns.full_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(__('filament.columns.phone'))
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('blood_type')
                    ->label(__('filament.columns.blood_type'))
                    ->required()
                    ->options([
                        'A+' => 'A+', 'A-' => 'A-',
                        'B+' => 'B+', 'B-' => 'B-',
                        'AB+' => 'AB+', 'AB-' => 'AB-',
                        'O+' => 'O+', 'O-' => 'O-',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('national_id')
                    ->label(__('filament.columns.national_id')),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('filament.columns.full_name')),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.columns.phone')),
                Tables\Columns\TextColumn::make('blood_type')
                    ->label(__('filament.columns.blood_type')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false);
    }
}
