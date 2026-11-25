<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.album', 'rentals.album'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        foreach ($orders as $order) {
            foreach ($order->rentals as $rental) {
                if ($rental->status === 'active') {
                    $rental->calculateLateFee();
                }
            }
        }

        return view('user.order-history', compact('orders'));
    }
}
