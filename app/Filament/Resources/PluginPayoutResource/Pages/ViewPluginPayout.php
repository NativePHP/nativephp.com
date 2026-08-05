<?php

namespace App\Filament\Resources\PluginPayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Resources\PluginPayoutResource;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\PluginPayout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPluginPayout extends ViewRecord
{
    protected static string $resource = PluginPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('retryPayout')
                ->label('Retry Payout')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Retry Payout')
                ->modalDescription('This will reset the payout to pending and dispatch the transfer job. Continue?')
                ->modalSubmitActionLabel('Retry')
                ->visible(fn (): bool => $this->record instanceof PluginPayout && $this->record->isFailed())
                ->action(function (): void {
                    /** @var PluginPayout $payout */
                    $payout = $this->record;

                    $payout->update([
                        'status' => PayoutStatus::Pending,
                    ]);

                    ProcessPayoutTransfer::dispatch($payout);

                    Notification::make()
                        ->title("Payout #{$payout->id} queued for retry")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
