<?php

namespace App\Filament\Resources\CartResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CartItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $product = \App\Models\Product::find($state);
                            $set('unit_price', $product->price);
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                        $unitPrice = $get('unit_price');
                        if ($unitPrice && $state) {
                            $set('subtotal', $unitPrice * $state);
                        }
                    }),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('DH')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('DH')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('MAD'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->money('MAD'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($data, $record) {
                        // Update cart total
                        $cart = $record->cart;
                        $cart->total_amount = $cart->items->sum('subtotal');
                        $cart->save();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($data, $record) {
                        // Update cart total
                        $cart = $record->cart;
                        $cart->total_amount = $cart->items->sum('subtotal');
                        $cart->save();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($data, $record) {
                        // Update cart total
                        $cart = $record->cart;
                        $cart->total_amount = $cart->items->sum('subtotal');
                        $cart->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Update cart total
                            $this->getOwnerRecord()->total_amount = $this->getOwnerRecord()->items->sum('subtotal');
                            $this->getOwnerRecord()->save();
                        }),
                ]),
            ]);
    }
}
