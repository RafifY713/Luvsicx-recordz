@extends('layouts.app')

@section('title', 'Pesanan Saya - Luvsicx Recordz')

@section('content')
    <h1 class="h3 fw-bold mb-4">Pesanan Saya</h1>

    @forelse ($orders as $order)
        <div class="card-lxz p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <div class="fw-bold fs-6 font-monospace">{{ $order->order_code }}</div>
                    <div class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</div>
                </div>
                <span class="badge-lxz {{ $order->statusClass() }}">{{ $order->statusLabel() }}</span>
            </div>

            <div class="mb-3">
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between gap-2 small py-1">
                        <span>
                            {{ $item->album->title }}
                            <span class="text-muted">&middot; {{ $item->type === 'buy' ? 'Beli' : 'Rental' }}</span>
                        </span>
                        <span class="text-muted">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                @if ($order->payment_method === 'qris')
                    @if ($order->status === 'rejected')
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge-proof-none">Pesanan Ditolak</span>
                        </div>
                        <div class="text-muted small mb-2">Upload ulang bukti bayar yang valid.</div>
                        <a href="{{ route('order.pay.show', $order) }}" class="btn btn-outline-gold btn-sm">Upload Ulang Bukti</a>
                    @elseif ($order->status === 'accepted' && $order->payment_status === 'paid')
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge-proof-verified">Pembayaran Diverifikasi</span>
                            @if ($order->payment_proof)
                                <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti bayar" class="proof-thumbnail">
                                </a>
                            @endif
                        </div>
                        @if ($order->payment_verified_at)
                            <div class="text-muted small">Diverifikasi pada {{ $order->payment_verified_at->format('d M Y H:i') }}</div>
                        @endif
                    @elseif (! $order->payment_proof)
                        <div class="mb-2"><span class="badge-proof-none">Belum Upload Bukti</span></div>
                        <a href="{{ route('order.pay.show', $order) }}" class="btn btn-primary btn-sm">Upload Bukti Bayar</a>
                    @else
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge-proof-pending">Menunggu Verifikasi Admin</span>
                            <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti bayar" class="proof-thumbnail">
                            </a>
                        </div>
                        <a href="{{ route('order.pay.show', $order) }}" class="btn btn-outline-gold btn-sm">Upload Ulang</a>
                    @endif
                @else
                    <span class="badge-lxz {{ $order->paymentClass() }}">{{ $order->paymentLabel() }}</span>
                @endif

                @if ($order->payment_status === 'paid')
                    <div class="text-muted small mt-2">
                        Dibayar: <span class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                        pada {{ $order->paid_at?->format('d M Y H:i') }}
                    </div>
                @endif
            </div>

            @if ($order->rentals->isNotEmpty())
                <div class="mb-3">
                    @foreach ($order->rentals as $rental)
                        <div class="py-2 border-top" style="border-color:#333 !important;">
                            <div class="small fw-semibold mb-2">
                                {{ $rental->album->title }}
                                <span class="text-muted">({{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }})</span>
                            </div>

                            @if ((float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge-late">&#9888; Rental Terlambat {{ $rental->late_days }} Hari</span>
                                    <span class="text-muted small">Denda: Rp {{ number_format((float) $rental->late_fee, 0, ',', '.') }}</span>
                                </div>
                                @if ($rental->late_fee_proof)
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge-proof-pending">Menunggu Verifikasi Denda</span>
                                        <a href="{{ asset('storage/'.$rental->late_fee_proof) }}" target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/'.$rental->late_fee_proof) }}" alt="Bukti denda" class="proof-thumbnail">
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('order.pay-fine.show', $order) }}" class="btn btn-outline-gold btn-sm mt-2">Upload Bukti Bayar Denda</a>
                                @endif
                            @elseif ((float) $rental->late_fee > 0 && $rental->late_fee_paid)
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge-late-paid">Denda Lunas</span>
                                    <span class="text-muted small">{{ $rental->late_fee_paid_at?->format('d M Y H:i') }}</span>
                                </div>
                            @elseif ((float) $rental->late_fee == 0 && $rental->status === 'completed')
                                <span class="badge-on-time">Selesai Tepat Waktu</span>
                            @elseif ($rental->status === 'active' && now()->gt($rental->end_date))
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge-active-rental" style="background:#fd7e14; color:#fff;">
                                        Terlambat {{ max(1, $rental->end_date->diffInDays(now())) }} hari
                                    </span>
                                    <span class="text-muted small">Denda akan dihitung otomatis.</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Metode: <span class="text-light">{{ strtoupper($order->payment_method) }}</span>
                    &middot; Total: <span class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex gap-2">
                    @if ($order->payment_method === 'qris' && $order->payment_status === 'unpaid' && ! in_array($order->status, ['accepted', 'completed']))
                        <a href="{{ route('order.pay.show', $order) }}" class="btn btn-primary btn-sm">Bayar Sekarang</a>
                    @endif
                    <a href="{{ route('order.status', $order) }}" class="btn btn-outline-gold btn-sm">Lihat Detail</a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <p class="mb-3">Belum ada pesanan.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
