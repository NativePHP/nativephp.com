<?php

namespace App\Filament\Resources\PluginBundleResource\Pages;

use App\Filament\Resources\PluginBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPluginBundle extends EditRecord
{
    protected static string $resource = PluginBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
