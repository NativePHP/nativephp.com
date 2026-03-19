<?php

namespace App\Filament\Resources\PluginResource\RelationManagers;

use App\Enums\PluginActivityType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activity History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (PluginActivityType $state): string => $state->color())
                    ->icon(fn (PluginActivityType $state): string => $state->icon())
                    ->sortable(),

                Tables\Columns\TextColumn::make('from_status')
                    ->label('From')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('to_status')
                    ->label('To')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('note')
                    ->label('Note/Reason')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->note)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('causer.email')
                    ->label('By')
                    ->placeholder('System'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
