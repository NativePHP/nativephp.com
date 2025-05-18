<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Enums\Subscription as SubscriptionEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('stripe_id')
                    ->disabled(),
                Forms\Components\TextInput::make('stripe_product')
                    ->disabled(),
                Forms\Components\TextInput::make('stripe_price')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stripe_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('stripe_product')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('stripe_price')
                    ->label('Plan')
                    ->formatStateUsing(function ($state) {
                        try {
                            return SubscriptionEnum::fromStripePriceId($state)->name();
                        } catch (\Exception $e) {
                            return $state;
                        }
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
