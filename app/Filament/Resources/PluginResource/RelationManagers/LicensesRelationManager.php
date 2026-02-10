<?php

namespace App\Filament\Resources\PluginResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Purchase History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),

                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Comped')
                    ->boolean(),

                Tables\Columns\TextColumn::make('pluginBundle.name')
                    ->label('Bundle')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->defaultSort('purchased_at', 'desc');
    }
}
