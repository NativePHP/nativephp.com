<?php

namespace App\Filament\Resources\SubscriptionItemResource\Pages;

use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionItemResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionItem extends ViewRecord
{
    protected static string $resource = SubscriptionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnStripe')
                ->label('View on Stripe')
                ->color('gray')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => 'https://dashboard.stripe.com/subscriptions/'.$this->record->subscription->stripe_id)
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Subscription Item Details')
                    ->schema([
                        Components\TextEntry::make('subscription.id')
                            ->label('Subscription ID'),
                        Components\TextEntry::make('stripe_id')
                            ->label('Stripe ID')
                            ->copyable(),
                        Components\TextEntry::make('stripe_product')
                            ->copyable(),
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
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}
