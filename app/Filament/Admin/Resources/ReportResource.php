<?php

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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\TextInput::make('report_type')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->disabled(),
                        Forms\Components\TextInput::make('location_text')
                            ->disabled(),
                        Forms\Components\TextInput::make('latitude')
                            ->disabled(),
                        Forms\Components\TextInput::make('longitude')
                            ->disabled(),
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
                    ->label('Citizen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ReportStatus $state): string => $state->color())
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ReportStatus::class),
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->options(Department::class),
                Tables\Filters\SelectFilter::make('report_type')
                    ->options([
                        'accident' => 'Accident',
                        'hazard' => 'Hazard',
                        'traffic_jam' => 'Traffic Jam',
                        'security_threat' => 'Security Threat',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
