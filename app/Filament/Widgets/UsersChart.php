<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class UsersChart extends ChartWidget
{
    protected ?string $heading = 'User Growth';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    protected string $color = 'primary';

    public ?string $filter = 'this_year';

    protected function getData(): array
    {
        $data = $this->getUsersPerPeriod();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data['counts'],
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
        return 'The number of new user registrations over time.';
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

    protected function getUsersPerPeriod(): array
    {
        $filter = $this->filter;

        $startDate = match ($filter) {
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'this_year' => now()->startOfYear(),
            'last_year' => now()->subYear()->startOfYear(),
            'all_time' => User::query()->min('created_at')
                ? Carbon::parse(User::query()->min('created_at'))->startOfMonth()
                : now()->startOfYear(),
            default => now()->startOfYear(),
        };

        $endDate = match ($filter) {
            'last_month' => now()->subMonth()->endOfMonth(),
            'last_year' => now()->subYear()->endOfYear(),
            default => now(),
        };

        $groupByDaily = in_array($filter, ['this_month', 'last_month']);

        $users = User::whereBetween('created_at', [$startDate, $endDate])
            ->pluck('created_at')
            ->groupBy(fn (Carbon $date) => $groupByDaily ? $date->format('Y-m-d') : $date->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $periods = [];
        $labels = [];

        $currentDate = $startDate->copy();
        if ($groupByDaily) {
            while ($currentDate <= $endDate) {
                $key = $currentDate->format('Y-m-d');
                $periods[$key] = $users->get($key, 0);
                $labels[] = $currentDate->format('M d');
                $currentDate->addDay();
            }
        } else {
            while ($currentDate <= $endDate) {
                $key = $currentDate->format('Y-m');
                $periods[$key] = $users->get($key, 0);
                $labels[] = $currentDate->format('M Y');
                $currentDate->addMonth();
            }
        }

        return [
            'labels' => $labels,
            'counts' => array_values($periods),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
