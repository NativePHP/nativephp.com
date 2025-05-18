<?php

namespace App\Filament\Widgets;

use App\Models\License;
use Filament\Widgets\ChartWidget;

class LicensesChart extends ChartWidget
{
    protected static ?string $heading = 'New Licenses';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected static string $color = 'success';

    // Default filter value
    public ?string $filter = 'month';

    protected function getData(): array
    {
        $data = $this->getLicensesPerPeriod();

        return [
            'datasets' => [
                [
                    'label' => 'New Licenses',
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
        return 'The number of new licenses issued over time.';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'This year',
        ];
    }

    protected function getLicensesPerPeriod(): array
    {
        $filter = $this->filter;

        $startDate = match ($filter) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7)->startOfDay(),
            'month' => now()->subDays(30)->startOfDay(),
            'year' => now()->startOfYear(),
            default => now()->subDays(30)->startOfDay(),
        };

        $endDate = match ($filter) {
            'today' => now()->endOfDay(),
            'year' => now()->endOfYear(),
            default => now(),
        };

        // Determine the appropriate grouping based on the filter
        $groupByFormat = match ($filter) {
            'today' => '%H:00', // Group by hour for today
            'week' => '%Y-%m-%d', // Group by day for week
            'month' => '%Y-%m-%d', // Group by day for month
            'year' => '%Y-%m', // Group by month for year
            default => '%Y-%m-%d',
        };

        $dateFormat = match ($filter) {
            'today' => 'H:i',
            'week', 'month' => 'M d',
            'year' => 'M Y',
            default => 'M d',
        };

        $licenses = License::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$groupByFormat}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Generate all periods between start and end date
        $periods = [];
        $labels = [];
        $counts = [];

        if ($filter === 'today') {
            // For today, generate hourly periods
            for ($hour = 0; $hour < 24; $hour++) {
                $date = sprintf('%02d:00', $hour);
                $periods[$date] = 0;
                $labels[] = $date;
            }
        } elseif ($filter === 'week' || $filter === 'month') {
            // For week and month, generate daily periods
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $date = $currentDate->format('Y-m-d');
                $periods[$date] = 0;
                $labels[] = $currentDate->format($dateFormat);
                $currentDate->addDay();
            }
        } elseif ($filter === 'year') {
            // For year, generate monthly periods
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $date = $currentDate->format('Y-m');
                $periods[$date] = 0;
                $labels[] = $currentDate->format($dateFormat);
                $currentDate->addMonth();
            }
        }

        // Fill in the actual counts
        foreach ($licenses as $date => $licenseData) {
            if (isset($periods[$date])) {
                $periods[$date] = $licenseData->count;
            }
        }

        $counts = array_values($periods);

        return [
            'labels' => $labels,
            'counts' => $counts,
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
