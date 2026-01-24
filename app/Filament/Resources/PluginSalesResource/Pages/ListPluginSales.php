<?php

namespace App\Filament\Resources\PluginSalesResource\Pages;

use App\Filament\Resources\PluginSalesResource;
use App\Filament\Resources\PluginSalesResource\Widgets\PluginSalesStats;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListPluginSales extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PluginSalesResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PluginSalesStats::class,
        ];
    }
}
