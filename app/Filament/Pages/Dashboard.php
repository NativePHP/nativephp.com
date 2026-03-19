<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LicenseDistributionChart;
use App\Filament\Widgets\LicensesChart;
use App\Filament\Widgets\PluginRevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UsersChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            UsersChart::class,
            LicensesChart::class,
            LicenseDistributionChart::class,
            PluginRevenueChart::class,
        ];
    }
}
