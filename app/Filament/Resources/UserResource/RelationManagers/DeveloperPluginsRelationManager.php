<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperPluginsRelationManager extends RelationManager
{
    protected static string $relationship = 'plugins';

    protected static ?string $title = 'Developer Plugins';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Package')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (PluginType $state): string => match ($state) {
                        PluginType::Free => 'gray',
                        PluginType::Paid => 'success',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (PluginStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => PluginResource::getUrl('edit', ['record' => $record]));
    }
}
