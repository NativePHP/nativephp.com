<?php

namespace App\Filament\Widgets;

use App\Models\PluginLicense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PluginRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue by Plugin';

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getRevenueByPlugin();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data['amounts'],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.7)',   // blue
                        'rgba(16, 185, 129, 0.7)',  // green
                        'rgba(249, 115, 22, 0.7)', // orange
                        'rgba(139, 92, 246, 0.7)', // purple
                        'rgba(236, 72, 153, 0.7)', // pink
                        'rgba(245, 158, 11, 0.7)', // amber
                        'rgba(20, 184, 166, 0.7)', // teal
                        'rgba(239, 68, 68, 0.7)',  // red
                        'rgba(99, 102, 241, 0.7)', // indigo
                        'rgba(168, 162, 158, 0.7)', // stone
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(249, 115, 22)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(245, 158, 11)',
                        'rgb(20, 184, 166)',
                        'rgb(239, 68, 68)',
                        'rgb(99, 102, 241)',
                        'rgb(168, 162, 158)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getDescription(): ?string
    {
        $total = PluginLicense::sum('price_paid');

        return 'Top 10 plugins by revenue. Total: $'.number_format($total / 100, 2);
    }

    protected function getRevenueByPlugin(): array
    {
        $revenues = PluginLicense::select('plugin_id', DB::raw('SUM(price_paid) as total_revenue'))
            ->whereNotNull('plugin_id')
            ->groupBy('plugin_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->with('plugin:id,name')
            ->get();

        $labels = [];
        $amounts = [];

        foreach ($revenues as $revenue) {
            $pluginName = $revenue->plugin?->name ?? 'Unknown';
            $labels[] = $pluginName;
            $amounts[] = $revenue->total_revenue / 100; // Convert cents to dollars for display
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
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
