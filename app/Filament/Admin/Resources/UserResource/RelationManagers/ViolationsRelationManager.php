<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Enums\ViolationStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ViolationsRelationManager extends RelationManager
{
    protected static string $relationship = 'violations';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('messages.violations');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('violation_type')
                    ->label(__('messages.violation_type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fine_amount')
                    ->label(__('messages.fine_amount') . ' (SAR)')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn(ViolationStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('messages.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('police.full_name')
                    ->label(__('messages.officer'))
                    ->searchable(),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
