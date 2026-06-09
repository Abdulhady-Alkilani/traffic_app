<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.activity_logs');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.activity_log.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.activity_log.plural_label');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\TextInput::make('admin.full_name')
                ->label(__('filament.columns.admin')),
            \Filament\Forms\Components\TextInput::make('action_type')
                ->label(__('filament.columns.action_type')),
            \Filament\Forms\Components\TextInput::make('target_table')
                ->label(__('filament.columns.target_table')),
            \Filament\Forms\Components\DateTimePicker::make('created_at')
                ->label(__('filament.columns.created_at')),
            \Filament\Forms\Components\Textarea::make('description')
                ->label(__('messages.description'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('admin.full_name')
                    ->label(__('filament.columns.admin'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('action_type')
                    ->label(__('filament.columns.action_type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_table')
                    ->label(__('filament.columns.target_table'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('messages.description'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action_type')
                    ->label(__('filament.columns.action_type'))
                    ->options([
                        'create' => __('filament.actions.create'),
                        'update' => __('filament.actions.edit'),
                        'delete' => __('filament.actions.delete'),
                        'view' => __('filament.actions.view'),
                    ]),
                Tables\Filters\SelectFilter::make('target_table')
                    ->label(__('filament.columns.target_table'))
                    ->options([
                        'users' => __('filament.navigation.users'),
                        'reports' => __('filament.navigation.reports'),
                        'vehicles' => __('filament.navigation.vehicles'),
                        'citizens_data' => __('filament.columns.citizen'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
