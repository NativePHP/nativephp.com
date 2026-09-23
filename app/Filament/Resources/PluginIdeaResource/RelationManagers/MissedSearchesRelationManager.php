<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginIdeaResource\RelationManagers;

use App\Enums\PluginType;
use App\Models\MissedPluginSearch;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class MissedSearchesRelationManager extends RelationManager
{
    protected static string $relationship = 'missedSearches';

    protected static ?string $title = 'Searches';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('term')
                    ->label('Search')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('use_case')
                    ->label('Use Case')
                    ->placeholder('—')
                    ->limit(120)
                    ->tooltip(fn (MissedPluginSearch $record): ?string => $record->use_case)
                    ->wrap(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Looking For')
                    ->badge()
                    ->formatStateUsing(fn (PluginType $state): string => $state->label())
                    ->placeholder('Any'),

                Tables\Columns\TextColumn::make('existing_idea_probability')
                    ->label('Already Requested')
                    ->headerTooltip('How likely Jev thought it was that this search asked for an idea we already had. Blank when Jev wasn\'t asked: the very first idea, or a repeat of an earlier search term.')
                    ->formatStateUsing(fn (float $state): string => round($state * 100).'%')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Searched')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
