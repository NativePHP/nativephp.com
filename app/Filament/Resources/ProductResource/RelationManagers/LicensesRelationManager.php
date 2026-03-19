<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static ?string $title = 'Licenses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('price_paid')
                    ->label('Price Paid (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('¢'),

                Forms\Components\Select::make('currency')
                    ->options([
                        'USD' => 'USD',
                    ])
                    ->default('USD'),

                Forms\Components\Toggle::make('is_comped')
                    ->label('Comped')
                    ->default(false),

                Forms\Components\DateTimePicker::make('purchased_at')
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Price Paid')
                    ->money('usd', divideBy: 100)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_comped')
                    ->label('Comped')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Purchased')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_comped')
                    ->label('Comped'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('purchased_at', 'desc');
    }
}
