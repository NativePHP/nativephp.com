<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\PluginType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PluginLicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'pluginLicenses';

    protected static ?string $title = 'Plugins';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plugin_id')
                    ->relationship('plugin', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('is_grandfathered')
                    ->label('Comped')
                    ->default(true),
                Forms\Components\DateTimePicker::make('purchased_at')
                    ->default(now()),
                Forms\Components\DateTimePicker::make('expires_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('plugin.type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (PluginType $state): string => match ($state) {
                        PluginType::Free => 'gray',
                        PluginType::Paid => 'success',
                    }),
                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->money('usd', divideBy: 100)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Comped')
                    ->boolean(),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_grandfathered')
                    ->label('Comped'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['price_paid'] = 0;
                        $data['currency'] = 'USD';

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
