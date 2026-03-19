<?php

namespace App\Filament\Resources\PluginBundleResource\RelationManagers;

use App\Enums\PriceTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Pricing';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tier')
                    ->options(PriceTier::class)
                    ->required()
                    ->default(PriceTier::Regular)
                    ->helperText('Regular = standard price, Subscriber = Pro/Max holders, EAP = Early Access'),

                Forms\Components\TextInput::make('amount')
                    ->label('Price (cents)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('¢')
                    ->helperText('Enter price in cents (e.g., 4999 = $49.99)'),

                Forms\Components\Select::make('currency')
                    ->options([
                        'USD' => 'USD',
                    ])
                    ->default('USD')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active prices are shown to customers'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tier')
            ->columns([
                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->color(fn (PriceTier $state): string => match ($state) {
                        PriceTier::Regular => 'gray',
                        PriceTier::Subscriber => 'info',
                        PriceTier::Eap => 'success',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Price')
                    ->money('usd', divideBy: 100)
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tier')
                    ->options(PriceTier::class),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            ->defaultSort('tier');
    }
}
