<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Enums\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PoliceDataRelationManager extends RelationManager
{
    protected static string $relationship = 'policeData';

    protected static ?string $title = 'Police Data';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('badge_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rank')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('department')
                    ->required()
                    ->options(Department::class)
                    ->enum(Department::class),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('badge_number'),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('rank'),
                Tables\Columns\TextColumn::make('department'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
