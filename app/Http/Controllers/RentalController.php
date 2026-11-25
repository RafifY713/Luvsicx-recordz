<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Cart;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function select(Album $album)
    {
        if ($album->price_rent === null) {
            return redirect()->route('product.show', $album)
                ->with('error', 'Album ini tidak tersedia untuk rental.');
        }

        if ($album->stock <= 0) {
            return redirect()->route('product.show', $album)
                ->with('error', 'Stok album habis.');
        }

        return view('user.rental-select', compact('album'));
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'album_id' => ['required', 'exists:albums,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $album = Album::findOrFail($validated['album_id']);

        if ($album->price_rent === null) {
            return back()->with('error', 'Album ini tidak tersedia untuk rental.');
        }

        if ($album->stock <= 0) {
            return back()->with('error', 'Stok album habis.');
        }

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $cart->items()->create([
            'album_id' => $album->id,
            'quantity' => 1,
            'type' => 'rent',
            'rental_days' => $days,
            'rental_start_date' => $validated['start_date'],
            'rental_end_date' => $validated['end_date'],
        ]);

        return redirect()->route('checkout.index')->with('success', 'Rental ditambahkan ke keranjang.');
    }
}
