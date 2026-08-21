<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Filament\Resources\PluginResource;
use App\Jobs\SendPendingPluginEmailDigest;
use App\Models\Plugin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPlugins extends ListRecords
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendPendingNewPluginNotifications')
                ->label(fn () => 'Email Subscribers ('.Plugin::query()->pendingNewPluginNotification()->count().')')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->visible(fn () => Plugin::query()->pendingNewPluginNotification()->count() > 0)
                ->requiresConfirmation()
                ->modalHeading('Email Pending Plugin Notifications')
                ->modalDescription(fn () => 'This will email every subscribed user a single digest covering the '
                    .Plugin::query()->pendingNewPluginNotification()->count()
                    .' plugin(s) approved since the last digest.')
                ->action(function (): void {
                    SendPendingPluginEmailDigest::dispatch();

                    Notification::make()
                        ->title('Digest queued')
                        ->body('The digest email is being sent to subscribed users.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
