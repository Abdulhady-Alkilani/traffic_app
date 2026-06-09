<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficViolationResource\RelationManagers;

use App\Enums\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PoliceRelationManager extends RelationManager
{
    protected static string $relationship = 'police';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.officer') ?? 'الضابط محرر المخالفة';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('badge_number')
                    ->label(__('filament.columns.badge_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('full_name')
                    ->label(__('filament.columns.full_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('rank')
                    ->label(__('filament.columns.rank'))
                    ->options([
                        'شرطي' => 'شرطي',
                        'عريف' => 'عريف',
                        'رقيب' => 'رقيب',
                        'رقيب أول' => 'رقيب أول',
                        'مساعد' => 'مساعد',
                        'مساعد أول' => 'مساعد أول',
                        'ملازم' => 'ملازم',
                        'ملازم أول' => 'ملازم أول',
                        'نقيب' => 'نقيب',
                        'رائد' => 'رائد',
                        'مقدم' => 'مقدم',
                        'عقيد' => 'عقيد',
                        'عميد' => 'عميد',
                        'لواء' => 'لواء',
                    ])
                    ->required(),
                Forms\Components\Select::make('department')
                    ->label(__('filament.columns.department'))
                    ->required()
                    ->options(Department::class)
                    ->enum(Department::class),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('badge_number')
                    ->label(__('filament.columns.badge_number')),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('filament.columns.full_name')),
                Tables\Columns\TextColumn::make('rank')
                    ->label(__('filament.columns.rank')),
                Tables\Columns\TextColumn::make('department')
                    ->label(__('filament.columns.department')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false);
    }
}
