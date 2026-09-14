<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Enums\PluginStatus;
use App\Filament\Resources\PluginResource;
use App\Jobs\SendPendingPluginEmailDigest;
use App\Models\Plugin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

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

    public function getTabs(): array
    {
        return [
            PluginStatus::Pending->value => $this->makeStatusTab(PluginStatus::Pending, 'Pending'),
            PluginStatus::Approved->value => $this->makeStatusTab(PluginStatus::Approved, 'Approved'),
            PluginStatus::Rejected->value => $this->makeStatusTab(PluginStatus::Rejected, 'Rejected'),
            'all' => Tab::make('All'),
        ];
    }

    /**
     * The status the active tab is scoped to, or null when the tab lists every status.
     */
    public function getActiveTabStatus(): ?PluginStatus
    {
        return PluginStatus::tryFrom($this->activeTab ?? '');
    }

    private function makeStatusTab(PluginStatus $status, string $label): Tab
    {
        return Tab::make($label)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));
    }
}
