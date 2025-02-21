<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Products Chart';

//     public ?string $filter = 'today';

//     protected function getFilters(): ?array
// {
//     return [
//         'today' => 'Today',
//         'week' => 'Last week',
//         'month' => 'Last month',
//         'year' => 'This year',
//     ];
// }

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
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
