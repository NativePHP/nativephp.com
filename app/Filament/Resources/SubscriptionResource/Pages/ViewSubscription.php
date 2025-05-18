<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Subscription Details')
                    ->schema([
                        Components\TextEntry::make('user.email')
                            ->label('User'),
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
                        Components\TextEntry::make('trial_ends_at')
                            ->dateTime(),
                        Components\TextEntry::make('ends_at')
                            ->dateTime(),
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}
