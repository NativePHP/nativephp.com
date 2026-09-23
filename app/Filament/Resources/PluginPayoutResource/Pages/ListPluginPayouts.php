<?php

namespace App\Filament\Resources\PluginPayoutResource\Pages;

use App\Filament\Resources\PluginPayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListPluginPayouts extends ListRecords
{
    protected static string $resource = PluginPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
