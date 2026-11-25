<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $albums = Album::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('artist', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $wishlistIds = Wishlist::where('user_id', auth()->id())->pluck('album_id')->all();

        return view('user.shop', compact('albums', 'q', 'wishlistIds'));
    }
}
