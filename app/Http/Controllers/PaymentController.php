<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.album')
            ->firstOrCreate(['user_id' => auth()->id()]);

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Keranjang masih kosong.');
        }

        return view('user.payment', compact('cart'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:qris,cod'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $cart = Cart::with('items.album')
                ->where('user_id', auth()->id())
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
            }

            $total = 0;

            foreach ($cart->items as $item) {
                if ($item->type === 'buy' && $item->album->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok album '.$item->album->title.' tidak mencukupi.',
                    ]);
                }

                $total += $item->subtotal;
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => $this->generateOrderCode(),
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'payment_proof' => null,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'album_id' => $item->album_id,
                    'type' => $item->type,
                    'quantity' => $item->quantity,
                    'rental_days' => $item->rental_days,
                    'rental_start_date' => $item->rental_start_date,
                    'rental_end_date' => $item->rental_end_date,
                    'subtotal' => $item->subtotal,
                ]);

                if ($item->type === 'rent') {
                    $order->rentals()->create([
                        'album_id' => $item->album_id,
                        'start_date' => $item->rental_start_date,
                        'end_date' => $item->rental_end_date,
                        'status' => 'unpaid',
                    ]);
                } else {
                    $item->album->decrement('stock', $item->quantity);
                }
            }

            $cart->items()->delete();

            return $order;
        });

        if ($order->payment_method === 'qris') {
            return redirect()->route('order.pay.show', $order);
        }

        return redirect()->route('orders.history')
            ->with('success', 'Pesanan COD berhasil dibuat. Menunggu konfirmasi admin.');
    }

    public function status(Order $order)
    {
        $this->ensureOwner($order);

        $order->load(['items.album', 'rentals.album']);
        $this->calculateActiveLateFees($order);

        return view('user.order-status', compact('order'));
    }

    public function showPayForm(Order $order)
    {
        $this->ensureOwner($order);

        if (! $this->isPayable($order)) {
            return redirect()->route('order.status', $order)
                ->with('error', 'Pesanan ini tidak dapat dibayar.');
        }

        return view('user.payment-qris', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        if (! $this->isPayable($order)) {
            return redirect()->route('order.status', $order)
                ->with('error', 'Pesanan ini tidak dapat dibayar.');
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        return redirect()->route('orders.history')
            ->with('success', 'Bukti bayar berhasil di-upload. Menunggu verifikasi admin.');
    }

    public function showPayFineForm(Order $order)
    {
        $this->ensureOwner($order);

        $order->load('rentals.album');
        $this->calculateActiveLateFees($order);

        $fineRentals = $order->rentals
            ->filter(fn (Rental $rental) => (float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
            ->values();

        if ($fineRentals->isEmpty()) {
            return redirect()->route('orders.history')
                ->with('info', 'Tidak ada denda yang perlu dibayar pada pesanan ini.');
        }

        return view('user.payment-fine', compact('order', 'fineRentals'));
    }

    public function uploadFineProof(Request $request, Order $order)
    {
        $this->ensureOwner($order);

        $order->load('rentals');
        $this->calculateActiveLateFees($order);

        $fineRentals = $order->rentals
            ->filter(fn (Rental $rental) => (float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
            ->values();

        if ($fineRentals->isEmpty()) {
            return redirect()->route('orders.history')
                ->with('info', 'Tidak ada denda yang perlu dibayar pada pesanan ini.');
        }

        $request->validate([
            'late_fee_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $oldProof = $fineRentals->pluck('late_fee_proof')->filter()->first();

        if ($oldProof) {
            Storage::disk('public')->delete($oldProof);
        }

        $path = $request->file('late_fee_proof')->store('fine-proofs', 'public');

        foreach ($fineRentals as $rental) {
            $rental->update([
                'late_fee_proof' => $path,
                'late_fee_proof_uploaded_at' => now(),
            ]);
        }

        return redirect()->route('orders.history')
            ->with('success', 'Bukti pembayaran denda di-upload. Menunggu verifikasi admin.');
    }

    private function calculateActiveLateFees(Order $order): void
    {
        foreach ($order->rentals as $rental) {
            if ($rental->status === 'active') {
                $rental->calculateLateFee();
            }
        }
    }

    private function isPayable(Order $order): bool
    {
        return $order->payment_method === 'qris'
            && $order->payment_status === 'unpaid'
            && in_array($order->status, ['pending', 'rejected'], true);
    }

    private function ensureOwner(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'LUV-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
