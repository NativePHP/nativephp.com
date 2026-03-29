<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Subscription;
use App\Filament\Resources\UserResource;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('createStripeCustomer')
                    ->label('Create Stripe Customer')
                    ->color('gray')
                    ->icon('heroicon-o-credit-card')
                    ->action(function (User $record): void {
                        if ($record->hasStripeId()) {
                            Notification::make()
                                ->danger()
                                ->title('User is already a Stripe customer.')
                                ->send();

                            return;
                        }

                        $record->createOrGetStripeCustomer();
                    })
                    ->visible(fn (User $record) => empty($record->stripe_id)),

                Actions\Action::make('compUltraSubscription')
                    ->label('Comp Ultra Subscription')
                    ->color('warning')
                    ->icon('heroicon-o-sparkles')
                    ->modalHeading('Comp Ultra Subscription')
                    ->modalSubmitActionLabel('Comp Ultra')
                    ->form(function (User $record): array {
                        $existingSubscription = $record->subscription('default');
                        $hasActiveSubscription = $existingSubscription && $existingSubscription->active();

                        $fields = [];

                        if ($hasActiveSubscription) {
                            $currentPlan = 'their current plan';

                            try {
                                $currentPlan = Subscription::fromStripePriceId(
                                    $existingSubscription->items->first()?->stripe_price ?? $existingSubscription->stripe_price
                                )->name();
                            } catch (\Exception) {
                            }

                            $fields[] = Placeholder::make('info')
                                ->label('')
                                ->content("This user has an active {$currentPlan} subscription. Choose when to switch them to the comped Ultra plan.");

                            $fields[] = Radio::make('timing')
                                ->label('When to switch')
                                ->options([
                                    'now' => 'Immediately — swap now and credit remaining value (swapAndInvoice)',
                                    'renewal' => 'At renewal — keep current plan until period ends, then switch (swap)',
                                ])
                                ->default('now')
                                ->required();
                        } else {
                            $fields[] = Placeholder::make('info')
                                ->label('')
                                ->content("This will create a free Ultra subscription for {$record->email}. A Stripe customer will be created if one doesn't exist.");
                        }

                        return $fields;
                    })
                    ->action(function (array $data, User $record): void {
                        $compedPriceId = config('subscriptions.plans.max.stripe_price_id_comped');

                        if (! $compedPriceId) {
                            Notification::make()
                                ->danger()
                                ->title('STRIPE_ULTRA_COMP_PRICE_ID is not configured.')
                                ->send();

                            return;
                        }

                        $record->createOrGetStripeCustomer();

                        $existingSubscription = $record->subscription('default');

                        if ($existingSubscription && $existingSubscription->active()) {
                            $timing = $data['timing'] ?? 'now';

                            if ($timing === 'now') {
                                $existingSubscription->skipTrial()->swapAndInvoice($compedPriceId);
                                $message = 'Subscription swapped to comped Ultra immediately. Remaining value has been credited.';
                            } else {
                                $existingSubscription->skipTrial()->swap($compedPriceId);
                                $message = 'Subscription will switch to comped Ultra at the end of the current billing period.';
                            }

                            Notification::make()
                                ->success()
                                ->title('Comped Ultra subscription applied.')
                                ->body($message)
                                ->send();
                        } else {
                            $record->newSubscription('default', $compedPriceId)->create();

                            Notification::make()
                                ->success()
                                ->title('Comped Ultra subscription created.')
                                ->body("Ultra subscription created for {$record->email}.")
                                ->send();
                        }
                    })
                    ->visible(function (User $record): bool {
                        if (! config('subscriptions.plans.max.stripe_price_id_comped')) {
                            return false;
                        }

                        return ! $record->hasActiveUltraSubscription();
                    }),

                Actions\Action::make('createAnystackLicense')
                    ->label('Create Anystack License')
                    ->color('gray')
                    ->icon('heroicon-o-key')
                    ->form([
                        Select::make('subscription')
                            ->label('Subscription Plan')
                            ->options(collect(Subscription::cases())->mapWithKeys(function ($case) {
                                return [$case->value => $case->name()];
                            }))
                            ->required(),
                    ])
                    ->action(function (array $data, User $record): void {
                        $subscription = Subscription::from($data['subscription']);

                        dispatch(new CreateAnystackLicenseJob($record, $subscription, null, $record->first_name, $record->last_name));
                    }),

                Actions\Action::make('sendPasswordReset')
                    ->label('Send Password Reset')
                    ->color('gray')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        Password::sendResetLink(
                            ['email' => $record->email]
                        );
                    }),

                Actions\Action::make('view_on_stripe')
                    ->label('View on Stripe')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (User $record) => 'https://dashboard.stripe.com/customers/'.$record->stripe_id)
                    ->openUrlInNewTab()
                    ->visible(fn (User $record) => filled($record->stripe_id)),

                Actions\Action::make('view_on_anystack')
                    ->label('View on Anystack')
                    ->color('gray')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (User $record) => 'https://app.anystack.sh/contacts/'.$record->anystack_contact_id)
                    ->openUrlInNewTab()
                    ->visible(fn (User $record) => filled($record->anystack_contact_id)),

                Actions\DeleteAction::make(),

            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical'),
        ];
    }
}
