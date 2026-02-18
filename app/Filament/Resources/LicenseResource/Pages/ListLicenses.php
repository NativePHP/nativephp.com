<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Enums\Subscription;
use App\Filament\Resources\LicenseResource;
use App\Jobs\UpsertLicenseFromAnystackLicense;
use App\Models\License;
use App\Services\Anystack\Anystack;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_from_anystack')
                ->label('Import from Anystack')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->form([
                    TextInput::make('anystack_id')
                        ->label('Anystack License UUID')
                        ->placeholder('Enter the Anystack license UUID')
                        ->required()
                        ->uuid()
                        ->helperText('Paste the license UUID from Anystack to import it into the system.'),
                    Select::make('subscription_item_id')
                        ->label('Subscription Item ID')
                        ->placeholder('Enter the subscription item ID')
                        ->nullable()
                        ->helperText('Provide the subscription item ID if this license relates to a subscription.')
                        ->relationship('subscriptionItem', 'id')
                        ->searchable(),
                ])
                ->action(function (array $data): void {
                    try {
                        $response = Anystack::api()
                            ->license($data['anystack_id'], Subscription::Mini->anystackProductId()) // any plan's product id will work
                            ->retrieve();

                        $licenseData = $response->json('data');

                        dispatch_sync(new UpsertLicenseFromAnystackLicense($licenseData));

                        if (filled($data['subscription_item_id'] ?? null)) {
                            $license = License::where('anystack_id', $data['anystack_id'])->firstOrFail();
                            $license->update(['subscription_item_id' => $data['subscription_item_id']]);
                        }

                        Notification::make()
                            ->title('License Imported')
                            ->body('The license was imported.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error importing license')
                            ->body('Failed to import license from Anystack: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
