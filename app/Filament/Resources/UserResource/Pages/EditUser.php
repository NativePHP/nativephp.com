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
            Actions\Action::make('createStripeCustomer')
                ->label('Create Stripe Customer')
                ->color('gray')
                ->icon('heroicon-o-credit-card')
                ->action(function (User $record) {
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
                ->action(function (array $data, User $record) {
                    $subscription = \App\Enums\Subscription::from($data['subscription']);

                    \App\Jobs\CreateAnystackLicenseJob::dispatch(
                        $record,
                        $subscription,
                        null,
                        $record->first_name,
                        $record->last_name,
                    );
                }),

            Actions\Action::make('sendPasswordReset')
                ->label('Send Password Reset')
                ->color('gray')
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->action(function (User $record) {
                    \Illuminate\Support\Facades\Password::sendResetLink(
                        ['email' => $record->email]
                    );
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
