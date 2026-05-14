<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CitizenDataRelationManager extends RelationManager
{
    protected static string $relationship = 'citizenData';

    protected static ?string $title = 'Citizen Data';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('national_id')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('blood_type')
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
                Tables\Columns\TextColumn::make('national_id'),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('blood_type'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
