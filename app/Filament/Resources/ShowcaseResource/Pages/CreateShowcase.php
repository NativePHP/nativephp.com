<?php

namespace App\Filament\Resources\ShowcaseResource\Pages;

use App\Filament\Resources\ShowcaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShowcase extends CreateRecord
{
    protected static string $resource = ShowcaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_nativephp'] = true;
        $data['approved_at'] = now();
        $data['approved_by'] = auth()->id();

        return $data;
    }
}
