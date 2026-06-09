<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers\AdminDataRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\CitizenDataRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\PoliceDataRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\ReportsRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\VehiclesRelationManager;
use App\Filament\Admin\Resources\UserResource\RelationManagers\ViolationsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.users');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.user.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.user.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.user_information'))
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label(__('messages.username'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('messages.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label(__('messages.password'))
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\Select::make('role_id')
                            ->label(__('filament.columns.role'))
                            ->options(\App\Models\Role::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $role = \App\Models\Role::find($state);
                                if ($role && $role->slug === 'police') {
                                    $set('is_active', false);
                                } else {
                                    $set('is_active', true);
                                }
                            }),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament.columns.is_active'))
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('بيانات المواطن')
                    ->relationship('citizenData')
                    ->schema([
                        Forms\Components\TextInput::make('national_id')
                            ->label('الرقم الوطني')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('full_name')
                            ->label('الاسم الكامل')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->required()
                            ->tel(),
                        Forms\Components\Select::make('blood_type')
                            ->label('زمرة الدم')
                            ->options([
                                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-',
                            ])
                            ->required(),
                    ])
                    ->visible(fn (Forms\Get $get) => \App\Models\Role::find($get('role_id'))?->slug === 'citizen')
                    ->columns(2),

                Forms\Components\Section::make('بيانات الشرطي')
                    ->relationship('policeData')
                    ->schema([
                        Forms\Components\TextInput::make('badge_number')
                            ->label('الرقم العسكري')
                            ->required(),
                        Forms\Components\TextInput::make('full_name')
                            ->label('الاسم الكامل')
                            ->required(),
                        Forms\Components\Select::make('rank')
                            ->label('الرتبة')
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
                            ->label('القسم')
                            ->options(\App\Enums\Department::class)
                            ->required(),
                    ])
                    ->visible(fn (Forms\Get $get) => \App\Models\Role::find($get('role_id'))?->slug === 'police')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label(__('messages.username'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('messages.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label(__('filament.columns.role'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament.columns.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label(__('filament.columns.role'))
                    ->options(\App\Models\Role::all()->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament.columns.is_active')),
            ])
            ->actions([
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
            AdminDataRelationManager::class,
            CitizenDataRelationManager::class,
            PoliceDataRelationManager::class,
            ReportsRelationManager::class,
            VehiclesRelationManager::class,
            ViolationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
