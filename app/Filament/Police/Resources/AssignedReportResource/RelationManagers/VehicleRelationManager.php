<?php

namespace App\Filament\Police\Resources\AssignedReportResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicle';

    protected static ?string $title = 'Vehicle Details';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Plate Number'),
                Tables\Columns\TextColumn::make('vehicle_type'),
                Tables\Columns\TextColumn::make('make'),
                Tables\Columns\TextColumn::make('model_year'),
                Tables\Columns\TextColumn::make('color'),
            ])
            ->actions([
                //
            ]);
    }
}
