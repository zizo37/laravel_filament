<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{

    use HasActiveIcon;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'fas-boxes-stacked';




    protected static ?string $navigationGroup = 'products';

    protected static ?int $navigationSort = 2;



    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getNavigationBadgeColor(): ?string
{
    return 'success'; // Use predefined colors like 'primary', 'danger', 'warning', 'success'
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->sortable()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->formatStateUsing(function (OrderStatus $state): string {
                    $styles = match ($state) {
                        OrderStatus::Cancelled => 'style="
                            background: linear-gradient(to bottom right, #FEE2E2, #FCA5A5);
                            color: #991B1B;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(153, 27, 27, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                        OrderStatus::Pending => 'style="
                            background: linear-gradient(to bottom right, #FEF9C3, #FDE047);
                            color: #B45309;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(180, 83, 9, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                        OrderStatus::Processing => 'style="
                            background: linear-gradient(to bottom right, #DBEAFE, #93C5FD);
                            color: #1E40AF;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(30, 64, 175, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                        OrderStatus::Shipped => 'style="
                            background: linear-gradient(to bottom right, #E0F2FE, #7DD3FC);
                            color: #0E7490;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(14, 116, 144, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                        OrderStatus::Delivered => 'style="
                            background: linear-gradient(to bottom right, #DCFCE7, #86EFAC);
                            color: #166534;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(22, 101, 52, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                        default => 'style="
                            background: linear-gradient(to bottom right, #F3F4F6, #D1D5DB);
                            color: #374151;
                            padding: 6px 12px;
                            border-radius: 10px;
                            font-size: 14px;
                            font-weight: bold;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
                            border: 1px solid rgba(55, 65, 81, 0.2);
                            text-transform: capitalize;
                            letter-spacing: 0.5px;
                            white-space: nowrap;
                            transition: all 0.3s ease-in-out;
                        "',
                    };

                    return "<span {$styles}>" . ucfirst($state->value) . "</span>";
                })

                    ->html(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
{
    return [
        RelationManagers\ItemsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
