<?php

declare(strict_types=1);

namespace App\Filament\Resources\PluginReportResource\Pages;

use App\Filament\Resources\PluginReportResource;
use Filament\Resources\Pages\ListRecords;

final class ListPluginReports extends ListRecords
{
    protected static string $resource = PluginReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
