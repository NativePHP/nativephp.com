<?php

namespace App\Filament\Resources\WallOfLoveSubmissionResource\Pages;

use App\Filament\Resources\WallOfLoveSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListWallOfLoveSubmissions extends ListRecords
{
    protected static string $resource = WallOfLoveSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
