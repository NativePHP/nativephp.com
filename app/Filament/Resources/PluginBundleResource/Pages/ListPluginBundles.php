<?php

namespace App\Filament\Resources\PluginBundleResource\Pages;

use App\Filament\Resources\PluginBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPluginBundles extends ListRecords
{
    protected static string $resource = PluginBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
