<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Album $album)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $hasPurchased = OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id())
                ->whereIn('status', ['accepted', 'completed']);
        })->where('album_id', $album->id)->exists();

        if (! $hasPurchased) {
            return back()->with('error', 'Anda hanya bisa review album yang sudah Anda beli atau rental.');
        }

        $alreadyReviewed = Review::where('user_id', auth()->id())
            ->where('album_id', $album->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah pernah mereview album ini.');
        }

        $order = Order::where('user_id', auth()->id())
            ->whereIn('status', ['accepted', 'completed'])
            ->whereHas('items', function ($query) use ($album) {
                $query->where('album_id', $album->id);
            })
            ->latest()
            ->first();

        Review::create([
            'user_id' => auth()->id(),
            'album_id' => $album->id,
            'order_id' => $order?->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_verified_purchase' => true,
        ]);

        $this->refreshAlbumRating($album);

        return redirect()->to(route('product.show', $album).'#reviews')
            ->with('success', 'Review berhasil dikirim. Terima kasih!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $album = $review->album;

        $review->delete();

        $this->refreshAlbumRating($album);

        return back()->with('success', 'Review dihapus.');
    }

    private function refreshAlbumRating(Album $album): void
    {
        $album->rating_count = $album->reviews()->count();
        $album->rating_avg = $album->reviews()->avg('rating') ?? 0;
        $album->save();
    }
}
