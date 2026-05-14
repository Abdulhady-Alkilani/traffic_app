<?php

namespace App\Filament\Police\Resources;

use App\Enums\ReportStatus;
use App\Filament\Police\Resources\AssignedReportResource\Pages;
use App\Filament\Police\Resources\AssignedReportResource\RelationManagers\VehicleRelationManager;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssignedReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Assigned Reports';

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
                Forms\Components\Section::make('Update Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(ReportStatus::class)
                            ->enum(ReportStatus::class),
                    ]),
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn ($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user && $user->policeData) {
                    $query->where('assigned_department', $user->policeData->department->value);
                }
            });
    }

    public static function getRelations(): array
    {
        return [
            VehicleRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignedReports::route('/'),
            'edit' => Pages\EditAssignedReport::route('/{record}/edit'),
            'view' => Pages\ViewAssignedReport::route('/{record}'),
        ];
    }
}
