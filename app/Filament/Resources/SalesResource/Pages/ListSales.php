<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Filament\Resources\SalesResource\Widgets\SalesStats;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = SalesResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStats::class,
        ];
    }
}
