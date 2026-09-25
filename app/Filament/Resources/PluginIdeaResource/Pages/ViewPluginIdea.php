<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginIdeaResource\Pages;

use App\Filament\Resources\PluginIdeaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

final class ViewPluginIdea extends ViewRecord
{
    protected static string $resource = PluginIdeaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
