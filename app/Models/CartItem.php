<?php

namespace App\Models;

use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
            $cartItem->unit_price = $cartItem->product->price;
            $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        });

        static::updating(function ($cartItem) {
            $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        });
    }

    public static function getCartTotal(int $userId): float
    {
        return static::where('user_id', $userId)->sum('subtotal');
    }

    public function convertToOrder()
    {
        return  \Illuminate\Support\Facades\DB::transaction(function () {
            // Validate stock
            if ($this->product->stock < $this->quantity) {
                throw new RuntimeException(
                    "Insufficient stock for product: {$this->product->name}"
                );
            }

            // Create order
            $order = Order::create([
                'user_id' => $this->user_id,
                'status' => 'pending',
                'total_amount' => static::getCartTotal($this->user_id),
            ]);

            // Create order items from cart items
            $cartItems = static::where('user_id', $this->user_id)->with('product')->get();

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'subtotal' => $cartItem->subtotal,
                ]);

                // Update stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            static::where('user_id', $this->user_id)->delete();

            return $order;
        });
    }
}
