<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.album')
            ->firstOrCreate(['user_id' => auth()->id()]);

        return view('user.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'album_id' => ['required', 'exists:albums,id'],
            'type' => ['required', 'in:buy,rent'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $album = Album::findOrFail($validated['album_id']);
        $type = $validated['type'];
        $quantity = (int) ($validated['quantity'] ?? 1);

        if ($album->stock <= 0) {
            return back()->with('error', 'Stok album habis.');
        }

        if ($type === 'rent' && $album->price_rent === null) {
            return back()->with('error', 'Album ini tidak tersedia untuk rental.');
        }

        if ($type === 'buy' && $quantity > $album->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $cart->items()->create([
            'album_id' => $album->id,
            'quantity' => $quantity,
            'type' => $type,
        ]);

        return redirect()->route('checkout.index')->with('success', 'Album ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($item->type === 'buy' && $validated['quantity'] > $item->album->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $item->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(CartItem $item)
    {
        $this->authorizeItem($item);

        $item->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    private function authorizeItem(CartItem $item): void
    {
        if ($item->cart->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
