<?php

namespace App\Filament\Resources\PluginSalesResource\Widgets;

use App\Filament\Resources\PluginSalesResource\Pages\ListPluginSales;
use App\Models\PluginLicense;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PluginSalesStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListPluginSales::class;
    }

    protected function getStats(): array
    {
        $filteredQuery = $this->getPageTableQuery();
        $totalQuery = PluginLicense::query();

        $isFiltered = $this->hasActiveFilters($filteredQuery);

        $grandTotalRevenue = $totalQuery->sum('price_paid');
        $grandTotalSales = $totalQuery->count();

        $stats = [
            Stat::make('Total Revenue', $this->formatCurrency($grandTotalRevenue))
                ->description('All time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Sales', number_format($grandTotalSales))
                ->description('All time')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
        ];

        if ($isFiltered) {
            $filteredRevenue = (clone $filteredQuery)->sum('price_paid');
            $filteredSales = (clone $filteredQuery)->count();

            $stats[] = Stat::make('Filtered Revenue', $this->formatCurrency($filteredRevenue))
                ->description('Current filter')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('warning');

            $stats[] = Stat::make('Filtered Sales', number_format($filteredSales))
                ->description('Current filter')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('warning');
        }

        return $stats;
    }

    protected function hasActiveFilters(Builder $query): bool
    {
        $tableFilters = $this->getTableFiltersForm()->getState();

        foreach ($tableFilters as $filter) {
            if (is_array($filter)) {
                foreach ($filter as $value) {
                    if ($value !== null && $value !== '' && $value !== false) {
                        return true;
                    }
                }
            } elseif ($filter !== null && $filter !== '' && $filter !== false) {
                return true;
            }
        }

        return false;
    }

    protected function formatCurrency(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
