@extends('layouts.app')

@section('title', 'Keranjang - Luvsicx Recordz')

@section('content')
    <h1 class="h3 fw-bold mb-4">Keranjang</h1>

    @if ($cart->items->isEmpty())
        <div class="empty-state">
            <p class="mb-3">Keranjang kamu masih kosong.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @else
        <div class="card-lxz p-3 mb-3">
            <div class="table-responsive">
                <table class="table table-lxz align-middle">
                    <thead>
                        <tr>
                            <th>Album</th>
                            <th>Tipe</th>
                            <th>Qty</th>
                            <th>Tanggal Rental</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart->items as $item)
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
                                <td>
                                    <form action="{{ route('cart.update', $item) }}" method="POST"
                                          class="d-flex align-items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                               class="form-control form-control-sm qty-input @error('quantity') is-invalid @enderror">
                                        <button type="submit" class="btn btn-sm btn-outline-gold">Update</button>
                                        @error('quantity')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </form>
                                </td>
                                <td class="small">
                                    @if ($item->type === 'rent')
                                        <div>{{ $item->rental_start_date?->format('d M Y') }} &mdash; {{ $item->rental_end_date?->format('d M Y') }}</div>
                                        <div class="text-muted">{{ $item->rental_days }} hari</div>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('cart.remove', $item) }}" method="POST"
                                          onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-5 col-lg-4">
                <div class="card-lxz p-3">
                    <div class="summary-line total mb-3">
                        <span>Total</span>
                        <span class="price">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Checkout</a>
                </div>
            </div>
        </div>
    @endif
@endsection
