<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $title = 'Ratings';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rating')
                    ->label('Stars')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Rated')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Changed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([
                Actions\DeleteAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove Rating')
                    ->modalDescription('This will delete the rating and recalculate the plugin\'s average. Use this to moderate abusive or fake ratings.'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
