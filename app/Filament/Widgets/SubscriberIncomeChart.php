<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Laravel\Cashier\Subscription;

class SubscriberIncomeChart extends ChartWidget
{
    protected ?string $heading = 'Subscriber Income';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected string $color = 'success';

    public ?string $filter = 'this_year';

    protected function getData(): array
    {
        $data = $this->getIncomePerPeriod();

        return [
            'datasets' => [
                [
                    'label' => 'Gross Income',
                    'data' => $data['amounts'],
                    'fill' => 'start',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'Gross subscriber income over time.';
    }

    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'This month',
            'last_month' => 'Last month',
            'this_year' => 'This year',
            'last_year' => 'Last year',
            'all_time' => 'All time',
        ];
    }

    protected function getIncomePerPeriod(): array
    {
        $filter = $this->filter;

        $startDate = match ($filter) {
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'this_year' => now()->startOfYear(),
            'last_year' => now()->subYear()->startOfYear(),
            'all_time' => Subscription::whereNotNull('price_paid')
                ->where('price_paid', '>', 0)
                ->min('created_at')
                ? Carbon::parse(Subscription::whereNotNull('price_paid')->where('price_paid', '>', 0)->min('created_at'))->startOfMonth()
                : now()->startOfYear(),
            default => now()->startOfYear(),
        };

        $endDate = match ($filter) {
            'last_month' => now()->subMonth()->endOfMonth(),
            'last_year' => now()->subYear()->endOfYear(),
            default => now(),
        };

        $groupByDaily = in_array($filter, ['this_month', 'last_month']);

        $subscriptions = Subscription::whereNotNull('price_paid')
            ->where('price_paid', '>', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('price_paid', 'created_at')
            ->groupBy(fn ($value, $key) => $groupByDaily
                ? Carbon::parse($key)->format('Y-m-d')
                : Carbon::parse($key)->format('Y-m'))
            ->map(fn ($group) => round($group->sum() / 100, 2));

        $periods = [];
        $labels = [];

        $currentDate = $startDate->copy();
        if ($groupByDaily) {
            while ($currentDate <= $endDate) {
                $key = $currentDate->format('Y-m-d');
                $periods[$key] = $subscriptions->get($key, 0);
                $labels[] = $currentDate->format('M d');
                $currentDate->addDay();
            }
        } else {
            while ($currentDate <= $endDate) {
                $key = $currentDate->format('Y-m');
                $periods[$key] = $subscriptions->get($key, 0);
                $labels[] = $currentDate->format('M Y');
                $currentDate->addMonth();
            }
        }

        return [
            'labels' => $labels,
            'amounts' => array_values($periods),
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (item) => '$' + item.formattedValue,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => '$' + value,
                        },
                    },
                },
            }
        JS);
    }
}
