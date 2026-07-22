<?php

namespace App\Filament\Resources\PluginPayoutResource\RelationManagers;

use App\Models\PluginPayoutAttempt;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    protected static ?string $title = 'Attempt History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('succeeded')
                    ->label('Succeeded')
                    ->boolean(),

                Tables\Columns\TextColumn::make('charge_id')
                    ->label('Charge')
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('stripe_transfer_id')
                    ->label('Transfer')
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->wrap()
                    ->placeholder('—')
                    ->tooltip(fn (PluginPayoutAttempt $record): ?string => $record->error_message),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
