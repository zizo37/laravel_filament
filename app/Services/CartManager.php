<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Product;

class CartManager
{
    private const CART_KEY = 'shopping_cart';

    public function addItem($productId, $quantity = 1)
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $product = Product::findOrFail($productId);
            $cart[$productId] = [
                'quantity' => $quantity,
                'price' => $product->price,
                'name' => $product->name,
                'image' => $product->image_url
            ];
        }

        $this->updateCart($cart);
    }

    public function removeItem($productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->updateCart($cart);
    }

    public function updateQuantity($productId, $quantity)
    {
        $cart = $this->getCart();
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }
        $this->updateCart($cart);
    }

    public function getCart()
    {
        return Session::get(self::CART_KEY, []);
    }

    public function getTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    public function getItemsCount()
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    public function clear()
    {
        Session::forget(self::CART_KEY);
    }

    private function updateCart($cart)
    {
        Session::put(self::CART_KEY, $cart);
    }
}
