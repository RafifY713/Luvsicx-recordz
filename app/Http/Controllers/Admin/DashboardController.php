<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlbums = Album::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingVerifications = Order::where('status', 'pending')
            ->whereNotNull('payment_proof')
            ->count();
        $unpaidFines = Rental::where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->count();
        $totalReviews = Review::count();

        return view('admin.dashboard', compact(
            'totalAlbums',
            'totalOrders',
            'pendingOrders',
            'pendingVerifications',
            'unpaidFines',
            'totalReviews'
        ));
    }
}
