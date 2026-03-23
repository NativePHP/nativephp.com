<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Filament\Resources\SalesResource\Widgets\SalesStats;
use App\Models\Sale;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Sales';

    protected static \UnitEnum|string|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Sale';

    protected static ?string $pluralModelLabel = 'Sales';

    protected static ?string $slug = 'sales';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
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

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->description(fn (Sale $record): ?string => $record->bundle_name ? "Bundle: {$record->bundle_name}" : null)
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Amount')
                    ->formatStateUsing(fn (int $state, Sale $record): string => '$'.number_format($state / 100, 2).' '.$record->currency)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_comped')
                    ->label('Comped')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email', fn (Builder $query) => $query->whereNotNull('email'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('product_name')
                    ->label('Product')
                    ->options(fn (): array => Sale::query()
                        ->whereNotNull('product_name')
                        ->distinct()
                        ->pluck('product_name', 'product_name')
                        ->toArray())
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_comped')
                    ->label('Comped'),
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
            SalesStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
