<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.vehicles');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.vehicle.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.vehicle.plural_label');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('citizen_id')
                    ->relationship('citizen', 'full_name')
                    ->label(__('filament.columns.owner'))
                    ->searchable()
                    ->preload()
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
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label(__('messages.plate_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label(__('messages.type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->label(__('messages.make'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_name')
                    ->label(__('الطراز'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_year')
                    ->label(__('messages.model_year'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_status')
                    ->label(__('حالة التأمين'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('messages.color')),
                Tables\Columns\TextColumn::make('citizen.full_name')
                    ->label(__('filament.columns.owner'))
                    ->searchable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VehicleResource\RelationManagers\CitizenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'view' => Pages\ViewVehicle::route('/{record}'),
        ];
    }
}
