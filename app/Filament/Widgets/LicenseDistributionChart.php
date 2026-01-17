<?php

namespace App\Filament\Widgets;

use App\Models\License;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LicenseDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'License Types';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getLicenseDistribution();

        return [
            'datasets' => [
                [
                    'label' => 'Licenses',
                    'data' => $data['counts'],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.7)',  // blue
                        'rgba(16, 185, 129, 0.7)', // green
                        'rgba(249, 115, 22, 0.7)', // orange
                        'rgba(139, 92, 246, 0.7)', // purple
                        'rgba(236, 72, 153, 0.7)', // pink
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',  // blue
                        'rgb(16, 185, 129)', // green
                        'rgb(249, 115, 22)', // orange
                        'rgb(139, 92, 246)', // purple
                        'rgb(236, 72, 153)', // pink
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getDescription(): ?string
    {
        return 'Distribution of licenses by policy type.';
    }

    protected function getLicenseDistribution(): array
    {
        $licenses = License::select('policy_name', DB::raw('count(*) as count'))
            ->groupBy('policy_name')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'labels' => $licenses->pluck('policy_name')->toArray(),
            'counts' => $licenses->pluck('count')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
