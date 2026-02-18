<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Filament\Resources\PluginResource;
use App\Jobs\SyncPluginReleases;
use App\Models\PluginLicense;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPlugin extends EditRecord
{
    protected static string $resource = PluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn () => $this->record->isPending())
                    ->action(fn () => $this->record->approve(auth()->id()))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Plugin')
                    ->modalDescription(fn () => "Are you sure you want to approve '{$this->record->name}'?"),

                Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn () => $this->record->isPending() || $this->record->isApproved())
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please explain why this plugin is being rejected...'),
                    ])
                    ->action(fn (array $data) => $this->record->reject($data['rejection_reason'], auth()->id()))
                    ->modalHeading('Reject Plugin')
                    ->modalDescription(fn () => "Are you sure you want to reject '{$this->record->name}'?"),

                Actions\Action::make('convertToPaid')
                    ->label('Convert to Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn () => $this->record->isFree())
                    ->form([
                        Forms\Components\Select::make('tier')
                            ->label('Pricing Tier')
                            ->options(PluginTier::class)
                            ->required()
                            ->helperText('This sets the pricing for the plugin.'),
                    ])
                    ->action(function (array $data): void {
                        $this->record->update([
                            'type' => PluginType::Paid,
                            'tier' => $data['tier'],
                        ]);

                        SyncPluginReleases::dispatch($this->record);

                        Notification::make()
                            ->title("Converted '{$this->record->name}' to paid")
                            ->body('Plugin type updated, prices synced, and Satis ingestion queued.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Convert Plugin to Paid')
                    ->modalDescription(fn () => "This will convert '{$this->record->name}' from free to paid, set up pricing, and trigger a Satis build so it's available via Composer.")
                    ->modalSubmitActionLabel('Convert & Ingest'),

                Actions\Action::make('grantToUser')
                    ->label('Grant to User')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return User::query()
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (User $user) => [$user->id => "{$user->name} ({$user->email})"])
                                    ->toArray();
                            })
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $user = User::findOrFail($data['user_id']);

                        $existingLicense = $user->pluginLicenses()
                            ->where('plugin_id', $this->record->id)
                            ->exists();

                        if ($existingLicense) {
                            Notification::make()
                                ->title('User already has a license for this plugin')
                                ->warning()
                                ->send();

                            return;
                        }

                        PluginLicense::create([
                            'user_id' => $user->id,
                            'plugin_id' => $this->record->id,
                            'price_paid' => 0,
                            'currency' => 'USD',
                            'is_grandfathered' => true,
                            'purchased_at' => now(),
                        ]);

                        $user->getPluginLicenseKey();

                        Notification::make()
                            ->title("Granted '{$this->record->name}' license to {$user->name}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Grant Plugin to User')
                    ->modalDescription(fn () => "Grant '{$this->record->name}' to a user for free.")
                    ->modalSubmitActionLabel('Grant'),

                Actions\Action::make('viewListing')
                    ->label('View Listing Page')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn () => route('plugins.show', $this->record->routeParams()))
                    ->openUrlInNewTab()
                    ->visible(fn () => $this->record->isApproved()),

                Actions\Action::make('viewPackagist')
                    ->label('View on Packagist')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn () => $this->record->getPackagistUrl())
                    ->openUrlInNewTab()
                    ->visible(fn () => $this->record->isFree()),

                Actions\Action::make('viewGithub')
                    ->label('View on GitHub')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn () => $this->record->getGithubUrl())
                    ->openUrlInNewTab(),
            ])
                ->icon('heroicon-m-ellipsis-vertical'),
        ];
    }
}
