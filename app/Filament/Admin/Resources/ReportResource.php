<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Filament\Admin\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.reports');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.report.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.report.plural_label');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_details'))
                    ->schema([
                        Forms\Components\Select::make('citizen_id')
                            ->relationship('citizen', 'full_name')
                            ->label(__('filament.columns.citizen'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('report_type')
                            ->label(__('messages.report_type'))
                            ->options([
                                'accident' => __('messages.accident'),
                                'hazard' => __('messages.hazard'),
                                'traffic_jam' => __('messages.traffic_jam'),
                                'security_threat' => __('messages.security_threat'),
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('messages.description'))
                            ->required(),
                        Forms\Components\TextInput::make('location_text')
                            ->label(__('messages.location'))
                            ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('messages.coordinates'))
                            ->required()
                            ->inputMode('decimal')
                            ->rule('regex:/^[-]?\d+[\.,]?\d*$/')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $val = (float) str_replace(',', '.', (string) $value);
                                        if ($val < -90 || $val > 90) {
                                            $fail('خط العرض يجب أن يكون بين -90 و 90');
                                        }
                                    };
                                },
                            ])
                            ->mutateDehydratedStateUsing(fn ($state) => str_replace(',', '.', (string) $state)),
                        Forms\Components\TextInput::make('longitude')
                            ->label(__('messages.coordinates'))
                            ->required()
                            ->inputMode('decimal')
                            ->rule('regex:/^[-]?\d+[\.,]?\d*$/')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $val = (float) str_replace(',', '.', (string) $value);
                                        if ($val < -180 || $val > 180) {
                                            $fail('خط الطول يجب أن يكون بين -180 و 180');
                                        }
                                    };
                                },
                            ])
                            ->mutateDehydratedStateUsing(fn ($state) => str_replace(',', '.', (string) $state)),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('citizen.full_name')
                    ->label(__('filament.columns.citizen'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->label(__('messages.report_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->label(__('filament.columns.assigned_department'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ReportStatus $state): string => $state->color())
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options(ReportStatus::class),
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->label(__('filament.columns.assigned_department'))
                    ->options(Department::class),
                Tables\Filters\SelectFilter::make('report_type')
                    ->label(__('messages.report_type'))
                    ->options([
                        'accident' => __('messages.accident'),
                        'hazard' => __('messages.hazard'),
                        'traffic_jam' => __('messages.traffic_jam'),
                        'security_threat' => __('messages.security_threat'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function getRelations(): array
    {
        return [
            ReportResource\RelationManagers\CitizenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
