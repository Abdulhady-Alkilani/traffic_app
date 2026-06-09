<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Enums\ReportStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->isCitizen();
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.my_reports');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_details'))
                    ->schema([
                        Forms\Components\Select::make('citizen_id')
                            ->relationship('citizen', 'full_name')
                            ->label(__('filament.columns.citizen'))
                            ->default(fn (\Filament\Resources\RelationManagers\RelationManager $livewire) => $livewire->getOwnerRecord()->citizenData?->id)
                            ->disabled()
                            ->dehydrated()
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->label(__('messages.report_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->label(__('filament.columns.assigned_department'))
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ReportStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => route('filament.admin.resources.reports.view', $record)),
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
