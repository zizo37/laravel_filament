<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StateWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::all()->count())
                // ->value(50)
                ->color('gray')
                ->icon('heroicon-o-shopping-cart')
                ->description('Total orders')
                ->chart([1, 2, 3, 6, 15, 8, 14, 25, 40]),

            Stat::make('pending_orders', Order::where('status', 'pending')->count())
                // ->value(10)
                ->color('primary')
                ->icon('heroicon-o-clock')
                ->description('Pending orders')
                ->chart([1, 2, 3, 6, 9, 6, 8, 11, 15]),
            Stat::make('processing_orders',Order::where('status', 'processing')->count())
                // ->value(20)
                ->color('Muted')
                ->icon('heroicon-o-cog')
                ->description('Processing orders')
                ->chart([1, 2, 3, 6, 15, 8, 14, 25, 40]),
            Stat::make('shipped_orders',Order::where('status', 'shipped')->count())
                // ->value(30)
                ->color('info')
                ->icon('heroicon-o-truck')
                ->description('Shipped orders')
                ->chart([1, 2, 3, 6, 15, 8, 14, 25, 40]),
            Stat::make('delivered_orders', Order::where('status', 'delivered')->count())
                // ->value(40)
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->description('Delivered orders')
                ->chart([1, 2, 3, 6, 15, 8, 14, 25, 40]),
            Stat::make('cancelled_orders', Order::where('status', 'cancelled')->count())
                // ->value(50)
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->description('Cancelled orders')
                ->chart([1, 2, 3, 6, 15, 8, 14, 25, 40]),

        ];
    }
}
