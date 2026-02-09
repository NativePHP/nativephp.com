<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PluginSalesResource\Pages;
use App\Filament\Resources\PluginSalesResource\Widgets\PluginSalesStats;
use App\Models\PluginLicense;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PluginSalesResource extends Resource
{
    protected static ?string $model = PluginLicense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Sales';

    protected static ?string $navigationGroup = 'Products';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Sale';

    protected static ?string $pluralModelLabel = 'Sales';

    protected static ?string $slug = 'plugin-sales';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plugin.name')
                    ->label('Plugin')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('pluginBundle.name')
                    ->label('Bundle')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Amount')
                    ->formatStateUsing(fn (int $state, PluginLicense $record): string => '$'.number_format($state / 100, 2).' '.$record->currency)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_grandfathered')
                    ->label('Grandfathered')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('plugin_id')
                    ->label('Plugin')
                    ->relationship('plugin', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('plugin_bundle_id')
                    ->label('Bundle')
                    ->relationship('pluginBundle', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_grandfathered')
                    ->label('Grandfathered'),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('purchased_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getWidgets(): array
    {
        return [
            PluginSalesStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginSales::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
