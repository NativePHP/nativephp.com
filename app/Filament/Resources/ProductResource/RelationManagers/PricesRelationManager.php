<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Enums\PriceTier;
use App\Models\ProductPrice;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Pricing';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('tier')
                    ->options(PriceTier::class)
                    ->required()
                    ->default(PriceTier::Regular)
                    ->helperText('Regular = standard price, Subscriber = Pro/Max holders, EAP = Early Access'),

                Forms\Components\TextInput::make('stripe_price_id')
                    ->label('Stripe price ID')
                    ->placeholder('price_...')
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->helperText('Optional. When set, checkout charges this Stripe price and the amount & currency are synced from Stripe on save (and are no longer editable here).'),

                Forms\Components\TextInput::make('amount')
                    ->label('Price (cents)')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('¢')
                    ->required(fn (Get $get): bool => blank($get('stripe_price_id')))
                    ->disabled(fn (Get $get): bool => filled($get('stripe_price_id')))
                    ->helperText(fn (Get $get): string => filled($get('stripe_price_id'))
                        ? 'Synced from the Stripe price on save.'
                        : 'Enter price in cents (e.g., 4999 = $49.99)'),

                Forms\Components\Select::make('currency')
                    ->options([
                        'USD' => 'USD',
                    ])
                    ->default('USD')
                    ->required(fn (Get $get): bool => blank($get('stripe_price_id')))
                    ->disabled(fn (Get $get): bool => filled($get('stripe_price_id'))),

                Forms\Components\TextInput::make('stripe_coupon_id')
                    ->label('Stripe coupon ID')
                    ->maxLength(255)
                    ->helperText('Coupon ID from the Stripe dashboard. Keep the amount above at full price — the price shown to buyers is calculated from the coupon\'s discount, and the coupon is pre-applied at Stripe checkout. Buyers cannot enter a promotion code when a coupon is pre-applied.'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active prices are shown to customers'),
            ]);
    }

    /**
     * When a Stripe price ID is set, fetch the price from Stripe and overwrite
     * the amount & currency so the database always reflects Stripe.
     */
    protected function syncStripePrice(array $data): array
    {
        if (blank($data['stripe_price_id'] ?? null)) {
            return $data;
        }

        try {
            $details = ProductPrice::detailsFromStripePrice($data['stripe_price_id']);
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->title('Could not sync Stripe price')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            throw new Halt;
        }

        $data['amount'] = $details['amount'];
        $data['currency'] = $details['currency'];

        return $data;
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

                Tables\Columns\TextColumn::make('stripe_price_id')
                    ->label('Stripe price')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stripe_coupon_id')
                    ->label('Coupon')
                    ->placeholder('—')
                    ->toggleable(),

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
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => $this->syncStripePrice($data)),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => $this->syncStripePrice($data)),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tier');
    }
}
