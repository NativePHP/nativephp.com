<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Actions\Licenses\DeleteLicense;
use App\Actions\Licenses\SuspendLicense;
use App\Actions\Licenses\UnsuspendLicense;
use App\Filament\Resources\LicenseResource;
use App\Jobs\UpsertLicenseFromAnystackLicense;
use App\Models\License;
use App\Services\Anystack\Anystack;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * @property ?License $record
 */
class EditLicense extends EditRecord
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('upsert_from_anystack')
                    ->label('Sync from Anystack')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Sync License from Anystack')
                    ->modalDescription('This will retrieve the latest license data from Anystack and update the local record.')
                    ->modalSubmitActionLabel('Sync License')
                    ->visible(fn () => filled($this->record->anystack_id))
                    ->action(function () {
                        try {
                            $response = Anystack::api()
                                ->license($this->record->anystack_id, $this->record->anystack_product_id)
                                ->retrieve();

                            dispatch_sync(new UpsertLicenseFromAnystackLicense($response->json('data')));

                            Notification::make()
                                ->title('License synced')
                                ->body('The license data has been synced.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error syncing license')
                                ->body('Failed to sync license from Anystack: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend')
                    ->modalDescription('Are you sure you want to suspend this license?')
                    ->modalSubmitActionLabel('Suspend')
                    ->visible(fn () => ! $this->record->is_suspended)
                    ->action(function () {
                        try {
                            app(SuspendLicense::class)->handle($this->record);

                            Notification::make()
                                ->title('License suspended successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error suspending license')
                                ->body('Failed to suspend license: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Actions\Action::make('unsuspend')
                    ->label('Unsuspend')
                    ->icon('heroicon-o-power')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Unsuspend')
                    ->modalDescription('Are you sure you want to remove the suspension for this license?')
                    ->modalSubmitActionLabel('Unsuspend')
                    ->visible(fn () => $this->record->is_suspended)
                    ->action(function () {
                        try {
                            app(UnsuspendLicense::class)->handle($this->record);

                            Notification::make()
                                ->title('License unsuspended successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error unsuspending license')
                                ->body('Failed to unsuspend license: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete License')
                    ->modalDescription('Are you sure you want to delete this license?')
                    ->modalSubmitActionLabel('Delete')
                    ->form([
                        Checkbox::make('delete_from_anystack')
                            ->label('Also delete from Anystack')
                            ->default(true),
                    ])
                    ->action(function (array $data) {
                        try {
                            app(DeleteLicense::class)->handle($this->record, $data['delete_from_anystack']);

                            Notification::make()
                                ->title('License deleted successfully')
                                ->success()
                                ->send();

                            $this->redirect(LicenseResource::getUrl('index'));
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error deleting license')
                                ->body('Failed to delete license: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical'),
        ];
    }
}
