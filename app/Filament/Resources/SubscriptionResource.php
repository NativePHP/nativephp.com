<?php

namespace App\Filament\Resources;

use App\Enums\Subscription as SubscriptionEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Cashier\Subscription;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Subscriptions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_id')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_status')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_price')
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('ends_at')
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
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'canceled' => 'danger',
                        'incomplete' => 'warning',
                        'incomplete_expired' => 'danger',
                        'past_due' => 'warning',
                        'trialing' => 'info',
                        'unpaid' => 'danger',
                        default => 'gray',
                    })
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
                // Tables\Columns\TextColumn::make('trial_ends_at')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
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
                Tables\Filters\SelectFilter::make('stripe_status')
                    ->options([
                        'active' => 'Active',
                        'canceled' => 'Canceled',
                        'incomplete' => 'Incomplete',
                        'incomplete_expired' => 'Incomplete Expired',
                        'past_due' => 'Past Due',
                        'trialing' => 'Trialing',
                        'unpaid' => 'Unpaid',
                    ]),
                Tables\Filters\Filter::make('trial_ends_at')
                    ->label('On Trial')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())),
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('stripe_status', 'active')),
                Tables\Filters\Filter::make('canceled')
                    ->query(fn (Builder $query): Builder => $query->where('stripe_status', 'canceled')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_on_stripe')
                    ->label('View on Stripe')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Subscription $record) => 'https://dashboard.stripe.com/subscriptions/'.$record->stripe_id)
                    ->openUrlInNewTab(),
            ])
            ->recordUrl(
                fn ($record) => static::getUrl('view', ['record' => $record])
            );

    }

    public static function getRelations(): array
    {
        return [
            SubscriptionResource\RelationManagers\SubscriptionItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => SubscriptionResource\Pages\ListSubscriptions::route('/'),
            'view' => SubscriptionResource\Pages\ViewSubscription::route('/{record}'),
        ];
    }
}
