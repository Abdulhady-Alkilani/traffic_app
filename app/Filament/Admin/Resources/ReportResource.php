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
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('خط العرض') . ' (Latitude)')
                            ->disabled()
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
                            ->label(__('خط الطول') . ' (Longitude)')
                            ->disabled()
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
                        Forms\Components\View::make('filament.components.map-viewer')
                            ->viewData(['isInteractive' => false])
                            ->columnSpanFull(),
                        Forms\Components\Select::make('assigned_department')
                            ->label(__('filament.columns.assigned_department'))
                            ->options(Department::class)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('messages.status'))
                            ->options(ReportStatus::class)
                            ->required()
                            ->default('new'),
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
                                $link = $record->ai_duplicate_of ? ' — <a href="' . route('filament.admin.resources.reports.view', $record->ai_duplicate_of) . '" style="color:#6366f1;text-decoration:underline;">' . __('messages.ai_duplicate_of') . ' #' . $record->ai_duplicate_of . '</a>' : '';
                                return new \Illuminate\Support\HtmlString('<span style="color:#ef4444;font-weight:700;">⚠ ' . __('نعم') . '</span>' . $link);
                            }),
                        Forms\Components\Placeholder::make('ai_analyzed_at_display')
                            ->label(__('messages.ai_analyzed_at'))
                            ->content(fn ($record) => $record?->ai_analyzed_at?->format('Y/m/d h:i A') ?? __('messages.ai_no_analysis')),
                    ])->columns(2)
                    ->collapsible()
                    ->visible(fn ($record) => $record !== null),
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
                    ->searchable(),
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
