@extends('layouts.app')

@section('title', 'Detail Pesanan '.$order->order_code.' - Luvsicx Recordz')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Detail Pesanan</h1>
            <div class="text-muted">{{ $order->order_code }} &middot; {{ $order->created_at->format('d M Y H:i') }}</div>
        </div>
        <a href="{{ route('orders.history') }}" class="btn btn-outline-light-lxz btn-sm">Kembali ke Riwayat</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Item Pesanan</h2>
                @foreach ($order->items as $item)
                    <div class="d-flex align-items-start gap-3 py-2 border-bottom" style="border-color:#333 !important;">
                        @if ($item->album->cover_url)
                            <img src="{{ $item->album->cover_url }}" alt="" class="cover-thumb">
                        @else
                            <div class="cover-thumb-placeholder">{{ $item->album->initials }}</div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item->album->title }}</div>
                            <div class="text-muted small">
                                {{ $item->type === 'buy' ? 'Beli' : 'Rental' }} &middot; {{ $item->quantity }} item
                                @if ($item->type === 'rent')
                                    &middot; {{ $item->rental_days }} hari
                                    ({{ $item->rental_start_date?->format('d M Y') }} - {{ $item->rental_end_date?->format('d M Y') }})
                                @endif
                            </div>
                        </div>
                        <div class="price">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</div>
                    </div>
                @endforeach
                <div class="summary-line total">
                    <span>Total</span>
                    <span class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($order->rentals->isNotEmpty())
                <div class="card-lxz p-3">
                    <h2 class="h6 fw-bold mb-3">Status Rental</h2>
                    @foreach ($order->rentals as $rental)
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 border-bottom" style="border-color:#333 !important;">
                            <div>
                                <div class="fw-semibold">{{ $rental->album->title }}</div>
                                <div class="text-muted small">
                                    {{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                @if ($rental->status === 'unpaid')
                                    <span class="badge-lxz badge-maroon">Belum Aktif</span>
                                @elseif ($rental->status === 'active')
                                    <span class="badge-lxz badge-gold">Aktif</span>
                                @else
                                    <span class="badge-lxz badge-muted">Selesai</span>
                                @endif

                                @if ((float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
                                    <span class="badge-late">Terlambat {{ $rental->late_days }} Hari</span>
                                    <span class="text-muted small">Denda: Rp {{ number_format((float) $rental->late_fee, 0, ',', '.') }}</span>
                                @elseif ((float) $rental->late_fee > 0 && $rental->late_fee_paid)
                                    <span class="badge-late-paid">Denda Lunas</span>
                                @elseif ((float) $rental->late_fee == 0 && $rental->status === 'completed')
                                    <span class="badge-on-time">Selesai Tepat Waktu</span>
                                @endif
                            </div>

                            @if ((float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
                                <div class="text-end mt-2">
                                    @if ($rental->late_fee_proof)
                                        <span class="badge-proof-pending">Menunggu Verifikasi Denda</span>
                                    @else
                                        <a href="{{ route('order.pay-fine.show', $order) }}" class="btn btn-outline-gold btn-sm">Upload Bukti Bayar Denda</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-lxz p-3">
                <h2 class="h6 fw-bold mb-3">Status</h2>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Status Pesanan</div>
                    <span class="badge-lxz {{ $order->statusClass() }}">{{ $order->statusLabel() }}</span>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Metode Pembayaran</div>
                    <div>{{ strtoupper($order->payment_method) }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Status Pembayaran</div>
                    @if ($order->payment_method === 'qris')
                        @if ($order->status === 'rejected')
                            <span class="badge-proof-none">Pesanan Ditolak</span>
                        @elseif ($order->status === 'accepted' && $order->payment_status === 'paid')
                            <span class="badge-proof-verified">Pembayaran Diverifikasi</span>
                        @elseif (! $order->payment_proof)
                            <span class="badge-proof-none">Belum Upload Bukti</span>
                        @else
                            <span class="badge-proof-pending">Menunggu Verifikasi Admin</span>
                        @endif
                    @else
                        <span class="badge-lxz {{ $order->paymentClass() }}">{{ $order->paymentLabel() }}</span>
                    @endif
                </div>

                @if ($order->payment_method === 'qris' && $order->payment_proof)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Bukti Bayar</div>
                        <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti bayar" class="proof-preview" style="max-width:180px; max-height:180px;">
                        </a>
                    </div>
                @endif

                @if ($order->payment_status === 'paid')
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Total Dibayar</div>
                        <div class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Waktu Pembayaran</div>
                        <div>{{ $order->paid_at?->format('d M Y H:i') }}</div>
                    </div>
                @endif

                @if ($order->payment_method === 'qris' && $order->payment_status === 'unpaid' && ! in_array($order->status, ['accepted', 'completed']))
                    <a href="{{ route('order.pay.show', $order) }}" class="btn btn-primary w-100">Bayar / Upload Bukti</a>
                @endif
            </div>
        </div>
    </div>
@endsection
