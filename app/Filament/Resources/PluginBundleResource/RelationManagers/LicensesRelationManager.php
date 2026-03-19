<?php

namespace App\Filament\Resources\PluginBundleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Bundle Purchases';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Allocated Amount')
                    ->formatStateUsing(fn (int $state): string => '$'.number_format($state / 100, 2)),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('purchased_at', 'desc');
    }
}
