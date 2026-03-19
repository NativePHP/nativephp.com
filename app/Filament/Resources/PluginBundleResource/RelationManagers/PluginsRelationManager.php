<?php

namespace App\Filament\Resources\PluginBundleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PluginsRelationManager extends RelationManager
{
    protected static string $relationship = 'plugins';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Included Plugins';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Package Name')
                    ->searchable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Developer'),

                Tables\Columns\TextColumn::make('activePrice.amount')
                    ->label('Retail Price')
                    ->formatStateUsing(fn (?int $state): string => $state ? '$'.number_format($state / 100, 2) : 'N/A'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->pivot->sort_order ?? 0),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name']),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
