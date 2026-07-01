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
                        Forms\Components\View::make('filament.components.map-viewer')
                            ->viewData(['isInteractive' => false])
                            ->columnSpanFull(),
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
                Forms\Components\Section::make(__('messages.ai_analysis'))
                    ->icon('heroicon-o-cpu-chip')
                    ->schema([
                        Forms\Components\Placeholder::make('ai_severity_display')
                            ->label(__('messages.ai_severity_score'))
                            ->content(function ($record) {
                                if (!$record || !$record->ai_severity_score) return __('messages.ai_no_analysis');
                                $score = $record->ai_severity_score;
                                $colors = [1 => '#10b981', 2 => '#22d3ee', 3 => '#f59e0b', 4 => '#f97316', 5 => '#ef4444'];
                                $labels = [1 => __('messages.severity_1'), 2 => __('messages.severity_2'), 3 => __('messages.severity_3'), 4 => __('messages.severity_4'), 5 => __('messages.severity_5')];
                                $color = $colors[$score] ?? '#6b7280';
                                $label = $labels[$score] ?? $score;
                                $width = ($score / 5) * 100;
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="flex items-center gap-3"><span style="color:'.$color.';font-size:1.5rem;font-weight:800;">'.$score.'/5</span>'
                                    .'<div class="flex-1"><div style="background:#e5e7eb;border-radius:9999px;height:8px;"><div style="background:'.$color.';width:'.$width.'%;border-radius:9999px;height:8px;transition:width 0.5s;"></div></div>'
                                    .'<span style="font-size:0.75rem;color:'.$color.';font-weight:600;">'.$label.'</span></div></div>'
                                );
                            }),
                        Forms\Components\Placeholder::make('ai_detected_plate_display')
                            ->label(__('messages.ai_detected_plate'))
                            ->content(fn ($record) => $record?->ai_detected_plate
                                ? new \Illuminate\Support\HtmlString('<span style="background:#eef2ff;color:#4f46e5;padding:4px 12px;border-radius:8px;font-weight:700;font-family:monospace;font-size:1.1rem;">' . $record->ai_detected_plate . '</span>')
                                : '-'),
                        Forms\Components\Placeholder::make('ai_incident_type_display')
                            ->label(__('messages.ai_incident_type'))
                            ->content(function ($record) {
                                if (!$record || !$record->ai_incident_type) return '-';
                                $map = ['accident' => __('messages.accident'), 'hazard' => __('messages.hazard'), 'traffic_jam' => __('messages.traffic_jam'), 'security_threat' => __('messages.security_threat')];
                                return $map[$record->ai_incident_type] ?? $record->ai_incident_type;
                            }),
                        Forms\Components\Placeholder::make('ai_damage_assessment_display')
                            ->label(__('messages.ai_damage_assessment'))
                            ->content(fn ($record) => $record?->ai_damage_assessment ?? '-')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('ai_summary_display')
                            ->label(__('messages.ai_summary'))
                            ->content(fn ($record) => $record?->ai_summary ?? '-')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('ai_duplicate_display')
                            ->label(__('messages.ai_is_duplicate'))
                            ->content(function ($record) {
                                if (!$record || !$record->ai_is_duplicate) return new \Illuminate\Support\HtmlString('<span style="color:#10b981;font-weight:600;">✓ ' . __('لا') . '</span>');
                                $link = $record->ai_duplicate_of ? ' — <a href="' . route('filament.police.resources.assigned-reports.view', $record->ai_duplicate_of) . '" style="color:#6366f1;text-decoration:underline;">' . __('messages.ai_duplicate_of') . ' #' . $record->ai_duplicate_of . '</a>' : '';
                                return new \Illuminate\Support\HtmlString('<span style="color:#ef4444;font-weight:700;">⚠ ' . __('نعم') . '</span>' . $link);
                            }),
                        Forms\Components\Placeholder::make('ai_analyzed_at_display')
                            ->label(__('messages.ai_analyzed_at'))
                            ->content(fn ($record) => $record?->ai_analyzed_at?->format('Y/m/d h:i A') ?? __('messages.ai_no_analysis')),
                    ])->columns(2)
                    ->collapsible()
                    ->visible(fn ($record) => $record !== null),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->label(__('filament.columns.assigned_department'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ai_severity_score')
                    ->label(__('messages.ai_severity_score'))
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1 => 'success',
                        2 => 'info',
                        3 => 'warning',
                        4 => 'warning',
                        5 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?int $state): string => $state ? $state . '/5' : '-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('ai_is_duplicate')
                    ->label(__('messages.ai_is_duplicate'))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
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
