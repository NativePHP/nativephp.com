<?php

namespace App\Filament\Resources\ShowcaseResource\Pages;

use App\Filament\Resources\ShowcaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShowcase extends EditRecord
{
    protected static string $resource = ShowcaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
