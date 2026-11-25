@extends('layouts.admin')

@section('title', 'Detail Rental')
@section('heading', 'Detail Rental')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <div class="fw-bold fs-5">{{ $rental->album->title }}</div>
            <div class="text-muted small">
                <a href="{{ route('admin.orders.show', $rental->order) }}">{{ $rental->order->order_code }}</a>
                &middot; {{ $rental->order->user->name }}
            </div>
        </div>
        <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-light-lxz btn-sm">Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Detail Rental</h2>
                <div class="summary-line">
                    <span>Album</span>
                    <span>{{ $rental->album->title }}</span>
                </div>
                <div class="summary-line">
                    <span>Pemesan</span>
                    <span>{{ $rental->order->user->name }} &middot; {{ $rental->order->user->email }}</span>
                </div>
                <div class="summary-line">
                    <span>Mulai</span>
                    <span>{{ $rental->start_date->format('d M Y') }}</span>
                </div>
                <div class="summary-line">
                    <span>Selesai</span>
                    <span>{{ $rental->end_date->format('d M Y') }}</span>
                </div>
                <div class="summary-line">
                    <span>Status</span>
                    @if ($rental->status === 'unpaid')
                        <span class="badge-lxz badge-maroon">Belum bayar</span>
                    @elseif ($rental->status === 'active')
                        <span class="badge-lxz badge-gold">Aktif</span>
                    @else
                        <span class="badge-lxz badge-muted">Selesai</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-lxz p-3 mb-4">
                <h2 class="h6 fw-bold mb-3">Info Denda</h2>
                @if ((float) $rental->late_fee > 0)
                    <div class="summary-line">
                        <span>Terlambat</span>
                        <span>{{ $rental->late_days }} hari</span>
                    </div>
                    <div class="summary-line total">
                        <span>Denda</span>
                        <span class="price">Rp {{ number_format((float) $rental->late_fee, 0, ',', '.') }}</span>
                    </div>

                    @if ($rental->late_fee_paid)
                        <div class="mt-3">
                            <span class="badge-late-paid">Denda Lunas</span>
                            <div class="text-muted small mt-2">
                                Dibayar pada {{ $rental->late_fee_paid_at?->format('d M Y H:i') }}
                            </div>
                            @if ($rental->late_fee_verified_at)
                                <div class="text-success small mt-1">
                                    Diverifikasi pada {{ $rental->late_fee_verified_at->format('d M Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3">
                            <span class="badge-late">Belum Lunas</span>
                        </div>

                        @if ($rental->late_fee_proof)
                            <div class="mt-3 text-center">
                                <a href="{{ asset('storage/'.$rental->late_fee_proof) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/'.$rental->late_fee_proof) }}" alt="Bukti bayar denda" class="proof-preview">
                                </a>
                                <div class="text-muted small mt-1">
                                    Di-upload pada {{ $rental->late_fee_proof_uploaded_at?->format('d M Y H:i') }}
                                </div>
                            </div>
                            <form action="{{ route('admin.rentals.verify-fine', $rental) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary w-100">Verifikasi Denda Lunas</button>
                            </form>
                        @else
                            <div class="text-muted small mt-3">User belum upload bukti bayar denda.</div>
                        @endif
                    @endif
                @else
                    <div class="text-muted small">Tidak ada denda pada rental ini.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
