<?php

declare(strict_types=1);

namespace App\Filament\Resources\DocsCoverageGapResource\Pages;

use App\Filament\Resources\DocsCoverageGapResource;
use Filament\Resources\Pages\ListRecords;

final class ListDocsCoverageGaps extends ListRecords
{
    protected static string $resource = DocsCoverageGapResource::class;
}
