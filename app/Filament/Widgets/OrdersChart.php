<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders Chart';

    protected static ?int $sort = 2;

    // Try a smaller height
    protected function getHeight(): ?string
    {
        return '230px';
    }

    protected function getData(): array
    {
        $orders = Order::selectRaw('status, COUNT(*) as count')
            ->whereMonth('created_at', now()->month)
            ->groupBy('status')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $orders->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB',  // pending
                        '#FF9F40',  // processing
                        '#4BC0C0',  // shipped
                        '#FFCE56',  // delivered
                        '#FF1818',  // cancelled
                    ],
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $orders->map(function ($order) {
                return ucfirst($order->status->value) . ' (' . $order->count . ')';
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,  // Add this line
            'responsive' => true,            // Add this line
            'scales' => [
                'y' => [
                    'display' => false,
                ],
                'x' => [
                    'display' => false,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'font' => [
                            'size' => 12
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label;
                        }",
                    ],
                ],
            ],
        ];
    }
}
