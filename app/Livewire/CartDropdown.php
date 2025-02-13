<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CartItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CartDropdown extends Component
{
    public $isOpen = false;
    public $quantities = [];

    public function mount()
    {
        $this->loadCartItems();
    }

    protected function loadCartItems()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        foreach ($cartItems as $item) {
            $this->quantities[$item->id] = $item->quantity;
        }
    }

    public function render(): View
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        $total = CartItem::getCartTotal(Auth::id());

        return view('livewire.cart-dropdown', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $cartItem = CartItem::find($cartItemId);
        if (!$cartItem) return;

        if ($quantity <= 0) {
            $cartItem->delete();
            Notification::make()
                ->title('Item removed')
                ->success()
                ->send();
        } else {
            $cartItem->update(['quantity' => $quantity]);
            Notification::make()
                ->title('Quantity updated')
                ->success()
                ->send();
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem($cartItemId)
    {
        CartItem::destroy($cartItemId);
        Notification::make()
            ->title('Item removed from cart')
            ->success()
            ->send();
        $this->dispatch('cart-updated');
    }

    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            Notification::make()
                ->title('Cart is empty')
                ->warning()
                ->send();
            return;
        }

        try {
            $order = $cartItems->first()->convertToOrder();
            Notification::make()
                ->title('Order created successfully')
                ->success()
                ->send();
            return redirect()->route('filament.admin.resources.orders.view', ['record' => $order]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error creating order')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
