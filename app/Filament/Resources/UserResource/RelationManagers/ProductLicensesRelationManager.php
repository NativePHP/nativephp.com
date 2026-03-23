<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProductLicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'productLicenses';

    protected static ?string $title = 'Products';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('is_comped')
                    ->label('Comped')
                    ->default(true),
                Forms\Components\DateTimePicker::make('purchased_at')
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->money('usd', divideBy: 100)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_comped')
                    ->label('Comped')
                    ->boolean(),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_comped')
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
