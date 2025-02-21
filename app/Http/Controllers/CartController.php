<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ensure this import is present

class CartController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $cartItem = CartItem::create([
            'user_id' => Auth::id(), // Use Auth facade
            ...$validated
        ]);

        return response()->json($cartItem);
    }
}
