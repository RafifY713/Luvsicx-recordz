<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('album')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.wishlist', compact('wishlists'));
    }

    public function add(Album $album)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('album_id', $album->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return back()->with('success', 'Dihapus dari wishlist');
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'album_id' => $album->id,
        ]);

        return back()->with('success', 'Ditambahkan ke wishlist');
    }

    public function remove(Album $album)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('album_id', $album->id)
            ->first();

        if (! $wishlist) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Dihapus dari wishlist');
    }
}
