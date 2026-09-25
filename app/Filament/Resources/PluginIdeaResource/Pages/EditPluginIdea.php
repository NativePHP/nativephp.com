<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginIdeaResource\Pages;

use App\Filament\Resources\PluginIdeaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditPluginIdea extends EditRecord
{
    protected static string $resource = PluginIdeaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
