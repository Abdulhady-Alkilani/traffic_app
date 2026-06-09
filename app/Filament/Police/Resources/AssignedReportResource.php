<?php

declare(strict_types=1);

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

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.assigned_reports');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.report.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.navigation.assigned_reports');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_details'))
                    ->schema([
                        Forms\Components\TextInput::make('report_type')
                            ->label(__('messages.report_type'))
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('messages.description'))
                            ->disabled(),
                        Forms\Components\TextInput::make('location_text')
                            ->label(__('messages.location'))
                            ->disabled(),
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('messages.coordinates'))
                            ->disabled(),
                        Forms\Components\TextInput::make('longitude')
                            ->label(__('messages.coordinates'))
                            ->disabled(),
                        Forms\Components\FileUpload::make('image_url')
                            ->label(__('الصور المرفقة'))
                            ->image()
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && $record->image_url),
                        Forms\Components\Placeholder::make('video_url')
                            ->label(__('الفيديو المرفق'))
                            ->content(fn ($record) => $record && $record->video_url ? new \Illuminate\Support\HtmlString('<video controls class="w-full max-h-96 rounded-lg"><source src="' . \Illuminate\Support\Facades\Storage::url($record->video_url) . '" type="video/mp4"></video>') : '-')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && $record->video_url),
                    ])->columns(2),
                Forms\Components\Section::make(__('filament.sections.update_status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('messages.status'))
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
                Tables\Columns\ImageColumn::make('image_url')
                    ->label(__('messages.image'))
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-report.png'))
                    ->visible(fn() => false),
                Tables\Columns\TextColumn::make('citizen.full_name')
                    ->label(__('filament.columns.citizen'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->label(__('messages.report_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ReportStatus $state): string => $state->color())
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->label(__('filament.columns.assigned_department'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options(ReportStatus::class),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.filters.from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.filters.until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->poll('10s')
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
