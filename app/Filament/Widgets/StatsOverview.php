<?php

namespace App\Filament\Widgets;

use App\Models\License;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Laravel\Cashier\Subscription;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),

            Stat::make('Total Subscriptions', Subscription::count()),
            Stat::make('Active Subscriptions', Subscription::query()->active()->count()),
            Stat::make('Canceled Subscriptions', Subscription::query()->canceled()->count()),

            Stat::make('Total Licenses', License::count()),
            Stat::make('Active Licenses', License::query()->whereActive()->count()),
        ];
    }
}
