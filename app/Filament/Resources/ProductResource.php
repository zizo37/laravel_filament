<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Categorie;
use App\Models\Depot;
use App\Models\Product;
use App\Models\User;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{

    use HasActiveIcon;
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'products';

    protected static ?int $navigationSort = 1;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return 'success'; // Change this to the desired color
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    // Forms\Components\FileUpload::make('image')
                    //     ->image()
                    //     ->directory('products')
                    //     ->maxSize(5120) // 5MB
                    //     ->imageResizeMode('cover')
                    //     ->imageCropAspectRatio('16:9')
                    //     ->imageResizeTargetWidth('1920')
                    //     ->imageResizeTargetHeight('1080'),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->maxLength(1000)
                        ->rows(3),
                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('DH')
                        ->minValue(0),
                    Forms\Components\TextInput::make('stock')
                        ->required()
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\Select::make('category_id')
                        // ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(Categorie::class::pluck('name', 'id')),
                    Forms\Components\Select::make('depot_id')
                        ->relationship('depot', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(Depot::class::pluck('name', 'id')),
                    Forms\Components\FileUpload::make('image')
                    ->maxSize(5120)
                    // ->directory('products'),
                    // Forms\Components\Toggle::make('is_active')
                    //     ->label('Available')
                    //     ->default(true),
                ])->columns(2)
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('description')->searchable(),
            Tables\Columns\ImageColumn::make('image')
            ->label('Image')
            ->circular()
            ->getStateUsing(fn (Product $record) => $record->image ? asset('storage/' . $record->image) : null),
            Tables\Columns\TextColumn::make('price')->searchable(),
            Tables\Columns\TextColumn::make('stock')->searchable(),
            Tables\Columns\TextColumn::make('category.name')->searchable()->label('Category')->url(fn (Model $record): string => CategorieResource::getUrl('edit',['record'=>$record->category_id])),
            Tables\Columns\TextColumn::make('depot.name')->searchable()->label('Depot')->url(fn (Model $record): string => DepotResource::getUrl('edit',['record'=>$record->depot_id])),
            Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('')->icon('heroicon-o-eye')
                ->iconButton()
                ->size('lg')
                ->extraAttributes(['class' => 'font-bold']),

                Tables\Actions\EditAction::make()->label('')->icon('fas-user-edit')
                ->iconButton()
                ->size('lg')
                ->extraAttributes(['class' => 'font-bold']),

                Tables\Actions\Action::make('add_to_cart')
                    // ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->label('Quantity')
                            ->helperText(fn (Product $record) => 'Available: ' . $record->stock)
                            ->maxValue(fn (Product $record) => $record->stock),
                    ])
                    ->action(function (Product $record, array $data) {
                        $cartItem = CartItem::updateOrCreate(
                            [
                                'user_id' => Auth::id(),
                                'product_id' => $record->id
                            ],
                            [
                                'quantity' => $data['quantity'],
                                'unit_price' => $record->price,
                            ]
                        );

                        Notification::make()
                            ->success()
                            ->title('Added to cart')
                            ->body('Product has been added to your cart.')
                            ->send();
                    })
                    ->visible(fn (Product $record): bool =>
                        $record->stock > 0 && $record->is_active
                    )->label('')->icon('fas-cart-plus')
                    ->iconButton()
                    ->size('lg')
                    ->extraAttributes(['class' => 'font-bold']),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
