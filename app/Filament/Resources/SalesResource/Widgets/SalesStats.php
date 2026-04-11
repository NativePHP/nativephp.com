<?php

namespace App\Filament\Resources\SalesResource\Widgets;

use App\Filament\Resources\SalesResource\Pages\ListSales;
use App\Models\Sale;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListSales::class;
    }

    protected function getStats(): array
    {
        $filteredQuery = $this->getPageTableQuery();

        $grandTotalRevenue = Sale::sum('price_paid');
        $grandTotalSales = Sale::count();

        $filteredRevenue = (clone $filteredQuery)->sum('price_paid');
        $filteredSales = (clone $filteredQuery)->count();

        $isFiltered = $filteredSales !== $grandTotalSales;

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

    protected function formatCurrency(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
