@extends('layouts.admin')

@section('title', 'Detail Order')
@section('heading', 'Detail Order')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <div class="fw-bold fs-5">{{ $order->order_code }}</div>
            <div class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-light-lxz btn-sm">Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Item Pesanan</h2>
                <div class="table-responsive">
                    <table class="table table-lxz align-middle">
                        <thead>
                            <tr>
                                <th>Album</th>
                                <th>Tipe</th>
                                <th class="text-center">Qty</th>
                                <th>Rental</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($item->album->cover_url)
                                                <img src="{{ $item->album->cover_url }}" alt="" class="cover-thumb">
                                            @else
                                                <div class="cover-thumb-placeholder">{{ $item->album->initials }}</div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $item->album->title }}</div>
                                                <div class="text-muted small">{{ $item->album->artist }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->type === 'buy')
                                            <span class="badge-lxz badge-gold">Beli</span>
                                        @else
                                            <span class="badge-lxz badge-dark-border">Rental</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="small text-muted">
                                        @if ($item->type === 'rent')
                                            {{ $item->rental_start_date?->format('d M Y') }} - {{ $item->rental_end_date?->format('d M Y') }}
                                            <div>{{ $item->rental_days }} hari</div>
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td class="text-end price">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="summary-line total">
                    <span>Total</span>
                    <span class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Info Pembayaran</h2>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Metode</div>
                    <div>{{ strtoupper($order->payment_method) }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Status</div>
                    <span class="badge-lxz {{ $order->paymentClass() }}">{{ $order->payment_status }}</span>
                </div>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Total</div>
                    <div class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
                </div>
                <div class="mt-3">
                    <div class="text-muted small mb-1">Bukti bayar</div>
                    @if ($order->payment_proof)
                        <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti bayar" class="proof-preview" style="max-width:220px; max-height:220px;">
                        </a>
                        <div class="text-muted small mt-2">
                            Di-upload pada: {{ $order->payment_proof_uploaded_at?->format('d M Y H:i') }}
                        </div>
                        @if ($order->payment_status === 'paid' && $order->payment_verified_at)
                            <div class="text-success small mt-1">
                                Diverifikasi oleh {{ $order->paymentVerifier?->name ?? 'Admin' }} pada
                                {{ $order->payment_verified_at->format('d M Y H:i') }}
                            </div>
                        @endif
                    @else
                        <div class="text-muted small">User belum upload bukti bayar.</div>
                    @endif
                </div>
            </div>

            @if ($order->rentals->isNotEmpty())
                <div class="card-lxz p-3">
                    <h2 class="h6 fw-bold mb-3">Rental</h2>
                    @foreach ($order->rentals as $rental)
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 border-bottom" style="border-color:#333 !important;">
                            <div>
                                <div class="fw-semibold">
                                    <a href="{{ route('admin.rentals.show', $rental) }}">{{ $rental->album->title }}</a>
                                </div>
                                <div class="text-muted small">
                                    {{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}
                                </div>
                                @if ((float) $rental->late_fee > 0)
                                    <div class="small mt-1">
                                        @if ($rental->late_fee_paid)
                                            <span class="badge-late-paid">Denda Lunas</span>
                                        @else
                                            <span class="badge-late">Terlambat {{ $rental->late_days }} hari &middot; Denda Rp {{ number_format((float) $rental->late_fee, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if ($rental->status === 'unpaid')
                                <span class="badge-lxz badge-maroon">Unpaid</span>
                            @elseif ($rental->status === 'active')
                                <span class="badge-lxz badge-gold">Active</span>
                            @else
                                <span class="badge-lxz badge-muted">Completed</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Pemesan</h2>
                <div class="fw-semibold">{{ $order->user->name }}</div>
                <div class="text-muted small">{{ $order->user->email }}</div>
            </div>

            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Status</h2>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Status Order</div>
                    <span class="badge-lxz {{ $order->statusClass() }}">{{ $order->statusLabel() }}</span>
                </div>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Metode</div>
                    <div>{{ strtoupper($order->payment_method) }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted small mb-1">Pembayaran</div>
                    <span class="badge-lxz {{ $order->paymentClass() }}">{{ $order->paymentLabel() }}</span>
                </div>
                    @if ($order->payment_status === 'paid')
                        <div class="mt-2">
                            <div class="text-muted small mb-1">Total Dibayar</div>
                            <div class="price">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
                            <div class="text-muted small">pada {{ $order->paid_at?->format('d M Y H:i') }}</div>
                        </div>
                    @endif
            </div>

            <div class="card-lxz p-3">
                <h2 class="h6 fw-bold mb-3">Aksi</h2>

                @if ($order->status === 'pending')
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('admin.orders.accept', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            @if ($order->payment_method === 'qris' && ! $order->payment_proof)
                                <button type="button" class="btn btn-primary btn-sm px-3" disabled
                                        title="User belum upload bukti bayar.">ACC Pesanan</button>
                            @else
                                <button type="submit" class="btn btn-primary btn-sm px-3">ACC Pesanan</button>
                            @endif
                        </form>
                        <form action="{{ route('admin.orders.reject', $order) }}" method="POST"
                              onsubmit="return confirm('Tolak order ini? Stok album akan dikembalikan.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm px-3">Tolak Pesanan</button>
                        </form>
                    </div>
                @elseif ($order->status === 'accepted')
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm px-3">Tandai Selesai</button>
                    </form>
                @else
                    <div class="text-muted small">Order sudah read-only.</div>
                @endif

                @if ($order->payment_method === 'cod' && $order->payment_status === 'unpaid')
                    <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-gold btn-sm">Tandai Lunas (COD)</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
