@extends('layouts.app')

@section('title', 'Checkout - Luvsicx Recordz')

@section('content')
    <h1 class="h3 fw-bold mb-4">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card-lxz p-3 mb-4">
                    <h2 class="h6 fw-bold mb-3">Ringkasan Item</h2>
                    @foreach ($cart->items as $item)
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
                            <div class="price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                    <div class="summary-line total">
                        <span>Total</span>
                        <span class="price">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="card-lxz p-3">
                    <h2 class="h6 fw-bold mb-3">Metode Pembayaran</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="pay-method">
                                <input type="radio" name="payment_method" value="qris"
                                       {{ old('payment_method', 'qris') === 'qris' ? 'checked' : '' }}>
                                <div class="pay-box">
                                    <div class="fw-bold text-gold">QRIS</div>
                                    <div class="text-muted small mt-1">Scan QR lalu masukkan nominal pembayaran.</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="pay-method">
                                <input type="radio" name="payment_method" value="cod"
                                       {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                                <div class="pay-box">
                                    <div class="fw-bold text-gold">COD</div>
                                    <div class="text-muted small mt-1">Bayar tunai saat barang diterima.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('payment_method')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-lxz p-3">
                    <h2 class="h6 fw-bold mb-3">Total Pembayaran</h2>
                    <div class="amount-display mb-3">Rp {{ number_format($cart->total, 0, ',', '.') }}</div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Buat Pesanan</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light-lxz w-100 mt-2">Kembali ke Keranjang</a>
                </div>
            </div>
        </div>
    </form>
@endsection
