<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalStatusController extends Controller
{
    public function index(Request $request)
    {
        $fineFilter = $request->query('fine');

        $rentals = Rental::with(['order.user', 'album'])
            ->when($fineFilter === 'unpaid', function ($query) {
                $query->where('late_fee', '>', 0)
                    ->where('late_fee_paid', false);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        foreach ($rentals as $rental) {
            if ($rental->status === 'active') {
                $rental->calculateLateFee();
            }
        }

        return view('admin.rentals.index', compact('rentals', 'fineFilter'));
    }

    public function show(Rental $rental)
    {
        $rental->load(['order.user', 'album']);

        if ($rental->status === 'active') {
            $rental->calculateLateFee();
        }

        return view('admin.rentals.show', compact('rental'));
    }

    public function verifyFine(Rental $rental)
    {
        if ((float) $rental->late_fee <= 0) {
            return back()->with('error', 'Rental ini tidak memiliki denda.');
        }

        if (! $rental->late_fee_proof) {
            return back()->with('error', 'User belum upload bukti bayar denda.');
        }

        if ($rental->late_fee_paid) {
            return back()->with('error', 'Denda rental ini sudah diverifikasi.');
        }

        $rental->update([
            'late_fee_paid' => true,
            'late_fee_paid_at' => now(),
            'late_fee_verified_at' => now(),
            'late_fee_verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Denda rental diverifikasi lunas.');
    }

    public function update(Rental $rental)
    {
        if ($rental->status === 'unpaid') {
            return back()->with('error', 'Rental belum dibayar.');
        }

        if ($rental->status === 'completed') {
            return back()->with('error', 'Rental ini sudah selesai.');
        }

        if (now()->lt($rental->end_date)) {
            return back()->with('error', 'Belum jatuh tempo.');
        }

        $rental->calculateLateFee();
        $rental->update(['status' => 'completed']);

        if ($rental->late_days > 0) {
            $message = 'Rental selesai. Terlambat '.$rental->late_days.' hari, denda Rp '
                .number_format((float) $rental->late_fee, 0, ',', '.').'.';
        } else {
            $message = 'Rental selesai tepat waktu.';
        }

        return back()->with('success', $message);
    }
}
