<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest Orders';

    protected static ?int $sort = 4;

    protected static bool $isFullWidth = true;

    protected string|int|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->withCount('items')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (Order $record): string => match ($record->status) {
                        OrderStatus::Pending => 'heroicon-o-clock',
                        OrderStatus::Processing => 'heroicon-o-sparkles',
                        OrderStatus::Shipped => 'heroicon-o-truck',
                        OrderStatus::Delivered => 'heroicon-o-check-circle',
                        OrderStatus::Cancelled => 'heroicon-o-x-circle',
                    })
                    ->color(fn (Order $record): string => match ($record->status) {
                        OrderStatus::Pending => 'warning',
                        OrderStatus::Processing => 'info',
                        OrderStatus::Shipped => 'primary',
                        OrderStatus::Delivered => 'success',
                        OrderStatus::Cancelled => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('MAD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}







// <?php

// namespace App\Filament\Widgets;

// use App\Models\Order;
// use App\Models\OrderStatus;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Widgets\TableWidget as BaseWidget;

// class OrdersWidget extends BaseWidget
// {
//     protected static ?string $heading = 'Latest Orders';

//     protected static ?int $sort = 4; // Place it after your charts


// protected static bool $isFullWidth = true;

//     public function table(Table $table): Table
//     {
//         return $table
//         ->query(
//             Order::query()
//                 ->withCount('items')
//                 ->orderBy('created_at', 'desc')
//                 ->limit(5)
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('id')
//                     ->label('Order ID')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('user.name')
//                     ->label('Customer')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('items_count')
//                     ->label('Items')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('status')
//                     ->label('Status')
//                     ->badge()
//                     ->icon(fn (Order $record): string => match ($record->status) {
//                         OrderStatus::Pending => 'heroicon-o-clock',
//                         OrderStatus::Processing => 'heroicon-o-sparkles',
//                         OrderStatus::Shipped => 'heroicon-o-truck',
//                         OrderStatus::Delivered => 'heroicon-o-check-circle',
//                         OrderStatus::Cancelled => 'heroicon-o-x-circle',
//                     })
//                     ->color(fn (Order $record): string => match ($record->status) {
//                         OrderStatus::Pending => 'warning',
//                         OrderStatus::Processing => 'info',
//                         OrderStatus::Shipped => 'primary',
//                         OrderStatus::Delivered => 'success',
//                         OrderStatus::Cancelled => 'danger',
//                     })
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('total_amount')
//                     ->label('Total')
//                     ->money('MAD')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->label('Order Date')
//                     ->dateTime('M d, Y')
//                     ->sortable(),
//             ])->paginated(false)

//             ->defaultSort('created_at', 'desc');
//     }


//     // public static function canView(): bool
//     // {
//     //     return true;
//     // }
// }
