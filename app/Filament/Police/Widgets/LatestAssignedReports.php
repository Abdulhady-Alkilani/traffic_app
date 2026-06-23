<?php

namespace App\Filament\Police\Widgets;

use App\Models\Report;
use App\Enums\ReportStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAssignedReports extends BaseWidget
{
    protected static ?string $heading = 'أحدث البلاغات المحولة للقسم';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $department = $user->policeData?->department;

        return $table
            ->query(
                Report::query()
                    ->where('assigned_department', $department)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم البلاغ')
                    ->formatStateUsing(fn ($record) => 'RPT-' . str_pad((string) $record->id, 6, '0', STR_PAD_LEFT)),
                
                Tables\Columns\TextColumn::make('report_type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => __('messages.' . $state)),
                
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y/m/d h:i A')
                    ->sortable(),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض التفاصيل')
                    ->url(fn (Report $record): string => \App\Filament\Police\Resources\AssignedReportResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
