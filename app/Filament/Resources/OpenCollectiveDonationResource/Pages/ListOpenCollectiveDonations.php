<?php

namespace App\Filament\Resources\OpenCollectiveDonationResource\Pages;

use App\Filament\Resources\OpenCollectiveDonationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpenCollectiveDonations extends ListRecords
{
    protected static string $resource = OpenCollectiveDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
