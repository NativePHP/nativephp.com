<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnStripe')
                ->label('View on Stripe')
                ->color('gray')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => 'https://dashboard.stripe.com/subscriptions/'.$this->record->stripe_id)
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->columns(1)
            ->schema([
                Section::make('Subscription Details')
                    ->inlineLabel()
                    ->columns(1)
                    ->schema([
                        Components\TextEntry::make('user.email')
                            ->label('User')
                            ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->user_id])),
                        Components\TextEntry::make('type'),
                        Components\TextEntry::make('stripe_id')
                            ->label('Stripe ID')
                            ->copyable(),
                        Components\TextEntry::make('stripe_status')
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
                            }),
                        Components\TextEntry::make('stripe_price')
                            ->label('Plan')
                            ->formatStateUsing(function ($state) {
                                try {
                                    $plan = SubscriptionEnum::fromStripePriceId($state);

                                    return $plan->name().' ('.$state.')';
                                } catch (\Exception $e) {
                                    return $state;
                                }
                            }),
                        Components\TextEntry::make('quantity'),
                        Components\TextEntry::make('billing_interval')
                            ->label('Billing Interval')
                            ->state(fn ($record): string => $record->stripe_price === config('subscriptions.plans.max.stripe_price_id_monthly')
                                ? 'Monthly'
                                : 'Annual'
                            )
                            ->badge(),
                        Components\TextEntry::make('price_paid')
                            ->label('Price Paid')
                            ->money('usd', divideBy: 100),
                        Components\TextEntry::make('trial_ends_at')
                            ->dateTime(),
                        Components\TextEntry::make('ends_at')
                            ->dateTime(),
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
