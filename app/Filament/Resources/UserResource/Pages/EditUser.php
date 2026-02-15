<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

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

                Actions\Action::make('createAnystackLicense')
                    ->label('Create Anystack License')
                    ->color('gray')
                    ->icon('heroicon-o-key')
                    ->form([
                        \Filament\Forms\Components\Select::make('subscription')
                            ->label('Subscription Plan')
                            ->options(collect(\App\Enums\Subscription::cases())->mapWithKeys(function ($case) {
                                return [$case->value => $case->name()];
                            }))
                            ->required(),
                    ])
                    ->action(function (array $data, User $record): void {
                        $subscription = \App\Enums\Subscription::from($data['subscription']);

                        dispatch(new \App\Jobs\CreateAnystackLicenseJob($record, $subscription, null, $record->first_name, $record->last_name));
                    }),

                Actions\Action::make('sendPasswordReset')
                    ->label('Send Password Reset')
                    ->color('gray')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        \Illuminate\Support\Facades\Password::sendResetLink(
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
