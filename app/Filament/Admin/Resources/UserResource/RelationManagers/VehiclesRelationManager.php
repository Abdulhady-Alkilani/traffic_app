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

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->isCitizen();
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.my_vehicles');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('citizen_id')
                    ->relationship('citizen', 'full_name')
                    ->label(__('filament.columns.citizen'))
                    ->default(fn (\Filament\Resources\RelationManagers\RelationManager $livewire) => $livewire->getOwnerRecord()->citizenData?->id)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make('plate_number')
                    ->label(__('messages.plate_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('vehicle_type')
                    ->label(__('messages.type'))
                    ->options([
                        'private' => 'سيارة خاصة (سياحية)',
                        'public' => 'نقل عام (باص/ميكروباص)',
                        'pickup' => 'سيارة نقل (بيك أب)',
                        'truck' => 'شاحنة',
                        'motorcycle' => 'دراجة نارية',
                        'agricultural' => 'مركبة زراعية',
                        'other' => 'أخرى',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('make')
                    ->label(__('messages.make'))
                    ->required(),
                Forms\Components\TextInput::make('model_name')
                    ->label(__('الطراز'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('model_year')
                    ->label(__('messages.model_year'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('chassis_number')
                    ->label(__('رقم الشاسيه'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('engine_number')
                    ->label(__('رقم المحرك'))
                    ->maxLength(255),
                Forms\Components\DatePicker::make('registration_expiry')
                    ->label(__('تاريخ انتهاء الميكانيك')),
                Forms\Components\Select::make('insurance_status')
                    ->label(__('حالة التأمين'))
                    ->options([
                        'valid' => __('ساري المفعول'),
                        'expired' => __('منتهي الصلاحية'),
                    ])
                    ->default('valid'),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('messages.color'))
                    ->required(),
            ]);
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
