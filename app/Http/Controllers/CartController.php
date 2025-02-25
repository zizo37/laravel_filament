<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ensure this import is present

class CartController extends Controller
{
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'product_id' => 'required|exists:products,id',
//             'quantity' => 'required|integer|min:1',
//             'unit_price' => 'required|numeric|min:0',
//             'subtotal' => 'required|numeric|min:0',
//         ]);

//         $cartItem = CartItem::create([
//             'user_id' => Auth::id(), // Use Auth facade
//             ...$validated
//         ]);

//         return response()->json($cartItem);
//     }
// }


public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'unit_price' => 'required|numeric|min:0',
        'subtotal' => 'required|numeric|min:0',
    ]);

    $userId = Auth::id();
    $productId = $validated['product_id'];

    // Vérifier si le produit est déjà dans le panier
    $cartItem = CartItem::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();

    if ($cartItem) {
        // Si l'article existe déjà, mettre à jour la quantité et le sous-total
        $cartItem->quantity += $validated['quantity'];
        $cartItem->subtotal = $cartItem->quantity * $validated['unit_price'];
        $cartItem->save();
    } else {
        // Sinon, créer un nouvel élément
        $cartItem = CartItem::create([
            'user_id' => $userId,
            ...$validated
        ]);
    }

    return response()->json($cartItem);
}
}



// namespace App\Http\Controllers;

// use App\Models\CartItem;
// use App\Models\Product;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;

// class CartController extends Controller
// {
//     /**
//      * Store a single cart item.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function store(Request $request)
//     // public function store(Request $request)
//     {
//         try {
//             // Validate incoming data
//             $validated = $request->validate([
//                 'product_id' => 'required|exists:products,id',
//                 'quantity' => 'required|integer|min:1',
//                 'unit_price' => 'required|numeric|min:0',
//                 'subtotal' => 'required|numeric|min:0',
//             ]);

//             // Check if user is authenticated
//             if (!Auth::check()) {
//                 return response()->json(['message' => 'Unauthenticated'], 401);
//             }

//             // Check if product already exists in cart
//             $existingCartItem = CartItem::where('user_id', Auth::id())
//                 ->where('product_id', $validated['product_id'])
//                 ->first();

//             if ($existingCartItem) {
//                 // Update existing cart item
//                 $existingCartItem->update([
//                     'quantity' => $existingCartItem->quantity + $validated['quantity'],
//                     'subtotal' => $existingCartItem->subtotal + $validated['subtotal']
//                 ]);
//                 $cartItem = $existingCartItem;
//             } else {
//                 // Create new cart item
//                 $cartItem = CartItem::create([
//                     'user_id' => Auth::id(),
//                     'product_id' => $validated['product_id'],
//                     'quantity' => $validated['quantity'],
//                     'unit_price' => $validated['unit_price'],
//                     'subtotal' => $validated['subtotal'],
//                 ]);
//             }

//             return response()->json([
//                 'message' => 'Item added to cart successfully',
//                 'cartItem' => $cartItem
//             ], 201);

//         } catch (\Illuminate\Validation\ValidationException $e) {
//             return response()->json([
//                 'message' => 'Validation failed',
//                 'errors' => $e->errors()
//             ], 422);
//         } catch (\Exception $e) {
//             // wLog::error('Cart store error: ' . $e->getMessage());
//             return response()->json([
//                 'message' => 'An error occurred while adding the item to the cart',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
//     /**
//      * Get all cart items for the authenticated user.
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function getItems()
//     {
//         $cartItems = CartItem::with('product')
//             ->where('user_id', Auth::id())
//             ->get();

//         $total = CartItem::getCartTotal(Auth::id());

//         return response()->json([
//             'cart_items' => $cartItems,
//             'total' => $total
//         ]);
//     }

//     /**
//      * Remove a cart item.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function destroy($id)
//     {
//         $cartItem = CartItem::where('id', $id)
//             ->where('user_id', Auth::id())
//             ->firstOrFail();

//         $cartItem->delete();

//         return response()->json([
//             'message' => 'Item removed from cart'
//         ]);
//     }

//     /**
//      * Clear the entire cart for the authenticated user.
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function clear()
//     {
//         CartItem::where('user_id', Auth::id())->delete();

//         return response()->json([
//             'message' => 'Cart cleared successfully'
//         ]);
//     }

//     /**
//      * Convert cart to order.
//      *
//      * @return \Illuminate\Http\JsonResponse
//      */
//     public function checkout()
//     {
//         try {
//             // Get first cart item to use its convertToOrder method
//             $cartItem = CartItem::where('user_id', Auth::id())->firstOrFail();
//             $order = $cartItem->convertToOrder();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Order created successfully',
//                 'order_id' => $order->id
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => $e->getMessage()
//             ], 422);
//         }
//     }
// }
