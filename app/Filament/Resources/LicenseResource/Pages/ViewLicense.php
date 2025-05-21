<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Actions\Licenses\SuspendLicense;
use App\Filament\Resources\LicenseResource;
use App\Models\License;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property ?License $record
 */
class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('suspend')
                    ->label('Suspend License')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend License')
                    ->modalDescription('Are you sure you want to suspend this license? This will prevent the user from using the software.')
                    ->modalSubmitActionLabel('Yes, suspend license')
                    ->visible(fn () => ! $this->record->is_suspended)
                    ->action(function () {
                        app(SuspendLicense::class)->handle($this->record);

                        Notification::make()
                            ->title('License suspended successfully')
                            ->success()
                            ->send();
                    }),
                Actions\DeleteAction::make(),
            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical'),
        ];
    }
}
