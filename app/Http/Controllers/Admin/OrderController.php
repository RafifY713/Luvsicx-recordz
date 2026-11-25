<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        if (! in_array($status, ['all', 'pending', 'accepted', 'completed', 'rejected'])) {
            $status = 'pending';
        }

        $orders = Order::with(['user', 'items.album'])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'accepted' => Order::where('status', 'accepted')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'rejected' => Order::where('status', 'rejected')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.album', 'rentals.album']);

        foreach ($order->rentals as $rental) {
            if ($rental->status === 'active') {
                $rental->calculateLateFee();
            }
        }

        return view('admin.orders.show', compact('order'));
    }

    public function accept(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya order pending yang bisa di-ACC.');
        }

        if ($order->payment_method === 'qris' && ! $order->payment_proof) {
            return back()->with('error', 'User belum upload bukti bayar. Tidak bisa di-ACC.');
        }

        $order->update([
            'status' => 'accepted',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_verified_at' => now(),
            'payment_verified_by' => auth()->id(),
        ]);

        $order->rentals()->where('status', 'unpaid')->update(['status' => 'active']);

        return back()->with('success', 'Pesanan '.$order->order_code.' di-ACC. Pembayaran diverifikasi.');
    }

    public function reject(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya order pending yang bisa ditolak.');
        }

        $order->update([
            'status' => 'rejected',
            'payment_status' => 'unpaid',
        ]);

        foreach ($order->items as $item) {
            if ($item->type === 'buy') {
                Album::whereKey($item->album_id)->increment('stock', $item->quantity);
            }
        }

        return back()->with('success', 'Pesanan '.$order->order_code.' ditolak. User diminta upload ulang.');
    }

    public function complete(Order $order)
    {
        if ($order->status !== 'accepted') {
            return back()->with('error', 'Hanya order accepted yang bisa ditandai selesai.');
        }

        $order->update(['status' => 'completed']);

        return back()->with('success', 'Order ditandai selesai.');
    }

    public function markPaid(Order $order)
    {
        if ($order->payment_method !== 'cod' || $order->payment_status !== 'unpaid') {
            return back()->with('error', 'Mark-paid hanya berlaku untuk order COD yang belum dibayar.');
        }

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran COD berhasil ditandai lunas.');
    }
}
