<?php

namespace App\Filament\Resources;

use App\Enums\Subscription as SubscriptionEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Cashier\SubscriptionItem;

class SubscriptionItemResource extends Resource
{
    protected static ?string $model = SubscriptionItem::class;

    protected static ?string $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Subscription Items';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Item Details')
                    ->schema([
                        Forms\Components\Select::make('subscription_id')
                            ->relationship('subscription', 'id')
                            ->searchable()
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_id')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_product')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_price')
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription.id')
                    ->numeric()
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('view_on_stripe')
                    ->label('View on Stripe')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (SubscriptionItem $record) => 'https://dashboard.stripe.com/subscriptions/'.$record->subscription->stripe_id)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => SubscriptionItemResource\Pages\ListSubscriptionItems::route('/'),
            'view' => SubscriptionItemResource\Pages\ViewSubscriptionItem::route('/{record}'),
        ];
    }
}
