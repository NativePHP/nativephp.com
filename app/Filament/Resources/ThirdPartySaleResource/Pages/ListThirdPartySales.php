<?php

namespace App\Filament\Resources\ThirdPartySaleResource\Pages;

use App\Filament\Resources\ThirdPartySaleResource;
use Filament\Resources\Pages\ListRecords;

class ListThirdPartySales extends ListRecords
{
    protected static string $resource = ThirdPartySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
