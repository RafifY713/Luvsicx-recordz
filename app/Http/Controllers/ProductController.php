<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Wishlist;

class ProductController extends Controller
{
    public function show(Album $album)
    {
        $userId = auth()->id();

        $inWishlist = Wishlist::where('user_id', $userId)
            ->where('album_id', $album->id)
            ->exists();

        $hasPurchased = OrderItem::where('album_id', $album->id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('status', ['accepted', 'completed']);
            })
            ->exists();

        $userReview = Review::where('user_id', $userId)
            ->where('album_id', $album->id)
            ->first();

        $reviewsQuery = Review::with(['user', 'order.items'])
            ->where('album_id', $album->id)
            ->where('user_id', '!=', $userId);

        $reviews = $reviewsQuery->latest()->paginate(5)->withQueryString();

        $counts = Review::where('album_id', $album->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $distribution = [];
        $totalReviews = (int) $counts->sum();

        foreach ([5, 4, 3, 2, 1] as $star) {
            $count = (int) ($counts[$star] ?? 0);
            $distribution[$star] = [
                'count' => $count,
                'percent' => $totalReviews > 0 ? round($count / $totalReviews * 100) : 0,
            ];
        }

        $canReview = $hasPurchased && ! $userReview;

        return view('user.product-detail', compact(
            'album',
            'inWishlist',
            'hasPurchased',
            'userReview',
            'reviews',
            'distribution',
            'totalReviews',
            'canReview'
        ));
    }
}
