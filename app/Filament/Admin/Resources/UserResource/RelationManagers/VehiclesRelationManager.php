<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.my_vehicles');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label(__('messages.plate_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label(__('messages.type')),
                Tables\Columns\TextColumn::make('make')
                    ->label(__('messages.make')),
                Tables\Columns\TextColumn::make('model_year')
                    ->label(__('messages.year')),
                Tables\Columns\TextColumn::make('color')
                    ->label(__('messages.color')),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
