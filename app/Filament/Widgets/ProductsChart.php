<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Products Chart';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Product::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true, // Enable fill under the line
                    'backgroundColor' => 'rgb(251, 191, 36, 0.2)', // Shadow color (semi-transparent)
                    'borderColor' => 'rgb(251, 191, 36)', // Line color
                    'tension' => 0.4, // Smooth the line (optional)
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Optional: Customize Chart.js options
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'elements' => [
                'point' => [
                    'radius' => 5, // Size of data points
                ],
            ],
        ];
    }
}
