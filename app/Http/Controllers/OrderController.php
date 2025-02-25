<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderController extends Controller
{
    /**
     * Convert cart to order (checkout)
     */
    public function store(Request $request)
    {
        try {
            $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Your cart is empty'
                ], 422);
            }

            // Check if all items are in stock
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    return response()->json([
                        'message' => "Insufficient stock for product: {$item->product->name}"
                    ], 422);
                }
            }

            // Find any cart item to use the existing conversion method
            $cartItem = $cartItems->first();
            $order = $cartItem->convertToOrder();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items.product')
            ], 201);

        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing your order'
            ], 500);
        }
    }

    /**
     * Get user's order history
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Get a specific order
     */
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        return response()->json([
            'order' => $order
        ]);
    }
}
