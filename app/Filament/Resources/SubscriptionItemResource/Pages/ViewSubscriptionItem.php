<?php

namespace App\Filament\Resources\SubscriptionItemResource\Pages;

use App\Enums\Subscription;
use App\Enums\Subscription as SubscriptionEnum;
use App\Filament\Resources\SubscriptionItemResource;
use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\UserResource;
use App\Jobs\UpsertLicenseFromAnystackLicense;
use App\Models\License;
use App\Services\Anystack\Anystack;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Laravel\Cashier\SubscriptionItem;

/**
 * @property ?SubscriptionItem $record
 */
class ViewSubscriptionItem extends ViewRecord
{
    protected static string $resource = SubscriptionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_from_anystack')
                ->label('Import Related License')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->form([
                    TextInput::make('anystack_id')
                        ->label('Anystack License UUID')
                        ->placeholder('Enter the Anystack license UUID')
                        ->required()
                        ->uuid()
                        ->helperText('Paste the license UUID from Anystack to import it into the system.'),
                ])
                ->action(function (array $data) {
                    try {
                        $response = Anystack::api()
                            ->license($data['anystack_id'], Subscription::Mini->anystackProductId()) // any plan's product id will work
                            ->retrieve();

                        $licenseData = $response->json('data');

                        dispatch_sync(new UpsertLicenseFromAnystackLicense($licenseData));

                        $license = License::where('anystack_id', $data['anystack_id'])->firstOrFail();
                        $license->update(['subscription_item_id' => $this->record->id]);

                        Notification::make()
                            ->title('License Imported')
                            ->body('The license was imported.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error importing license')
                            ->body('Failed to import license from Anystack: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
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
                            ->label('Subscription ID')
                            ->url(fn ($record) => SubscriptionResource::getUrl('view', ['record' => $record->subscription_id])),
                        Components\TextEntry::make('subscription.user.email')
                            ->label('User')
                            ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record->subscription_id])),
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
