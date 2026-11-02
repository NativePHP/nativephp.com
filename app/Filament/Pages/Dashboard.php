<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LicenseDistributionChart;
use App\Filament\Widgets\PluginRevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\SubscriberIncomeChart;
use App\Filament\Widgets\UsersChart;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    /**
     * Staff whose role can't see any dashboard widget are sent straight to
     * the first section they can open instead of an empty page.
     */
    public function mount(): void
    {
        if (static::hasVisibleWidgets()) {
            return;
        }

        if ($url = static::firstAccessibleNavigationUrl()) {
            $this->redirect($url);
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return parent::shouldRegisterNavigation() && static::hasVisibleWidgets();
    }

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
            SubscriberIncomeChart::class,
            LicenseDistributionChart::class,
            PluginRevenueChart::class,
        ];
    }

    protected static function hasVisibleWidgets(): bool
    {
        $dashboard = new static;

        return collect([...$dashboard->getHeaderWidgets(), ...$dashboard->getWidgets()])
            ->contains(fn (string $widget): bool => $widget::canView());
    }

    protected static function firstAccessibleNavigationUrl(): ?string
    {
        foreach (Filament::getNavigation() as $group) {
            foreach ($group->getItems() as $item) {
                if ($item->isVisible() && filled($item->getUrl())) {
                    return $item->getUrl();
                }
            }
        }

        return null;
    }
}
