<?php

namespace App\Filament\Actions;

use App\Actions\RefundPluginPurchase;
use App\Models\PluginLicense;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class RefundPluginLicenseAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'refund';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->authorize('refund')
            ->label('Refund')
            ->icon('heroicon-o-receipt-refund')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Refund purchase')
            ->modalDescription(function (PluginLicense $record): string {
                $description = "This will refund {$record->user->email} what they paid for {$record->plugin->name}, after any coupon or tax, and revoke their license.";

                if ($record->wasPurchasedAsBundle()) {
                    $description .= ' This license was bought as part of a bundle, so every license in the bundle will be refunded.';
                }

                return $description;
            })
            ->modalSubmitActionLabel('Yes, refund')
            ->visible(fn (PluginLicense $record): bool => $record->isRefundable())
            ->action(function (PluginLicense $record): void {
                try {
                    $amount = app(RefundPluginPurchase::class)->handle($record, auth()->user());

                    Notification::make()
                        ->title('Purchase refunded successfully')
                        ->body('Refunded $'.number_format($amount / 100, 2).'.')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Refund failed')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
