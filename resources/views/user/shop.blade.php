@extends('layouts.app')

@section('title', 'Shop - Luvsicx Recordz')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Katalog Album</h1>
            <p class="text-muted mb-0">
                @if ($q !== '')
                    Hasil pencarian untuk &ldquo;{{ $q }}&rdquo;
                @else
                    Temukan album favoritmu, beli atau rental.
                @endif
            </p>
        </div>
        <div class="text-muted small">{{ $albums->total() }} album</div>
    </div>

    <div class="row g-4">
        @forelse ($albums as $album)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="album-card h-100 position-relative d-flex flex-column">
                    @if (in_array($album->id, $wishlistIds))
                        <form action="{{ route('wishlist.add', $album) }}" method="POST" class="position-absolute wishlist-float">
                            @csrf
                            <button type="submit" class="wishlist-btn active" title="Hapus dari wishlist">&#9829;</button>
                        </form>
                    @else
                        <form action="{{ route('wishlist.add', $album) }}" method="POST" class="position-absolute wishlist-float">
                            @csrf
                            <button type="submit" class="wishlist-btn" title="Tambah ke wishlist">&#9825;</button>
                        </form>
                    @endif

                    <a href="{{ route('product.show', $album) }}">
                        @if ($album->cover_url)
                            <img src="{{ $album->cover_url }}" alt="Cover {{ $album->title }}" class="album-cover">
                        @else
                            <div class="album-cover-placeholder">{{ $album->initials }}</div>
                        @endif
                    </a>

                    <div class="album-card-body d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="album-title">{{ $album->title }}</div>
                                <div class="album-artist">{{ $album->artist }}</div>
                            </div>
                            @if ($album->stock <= 0)
                                <span class="badge-lxz badge-maroon">Habis</span>
                            @endif
                        </div>

                        <div class="small mb-2">
                            @if ($album->rating_count > 0)
                                <span class="text-gold">&#9733;</span>
                                <span class="text-gold fw-semibold">{{ number_format((float) $album->rating_avg, 1) }}</span>
                                <span class="text-muted">({{ $album->rating_count }})</span>
                            @else
                                <span class="text-muted">Belum ada review</span>
                            @endif
                        </div>

                        <div class="small mb-1 d-flex justify-content-between">
                            <span class="text-muted">Beli</span>
                            <span class="price">Rp {{ number_format((float) $album->price_buy, 0, ',', '.') }}</span>
                        </div>
                        <div class="small mb-2 d-flex justify-content-between">
                            <span class="text-muted">Rental / hari</span>
                            @if ($album->price_rent !== null)
                                <span class="price">Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>

                        <div class="small mb-3 d-flex justify-content-between">
                            <span class="text-muted">Stok</span>
                            <span class="{{ $album->stock > 0 ? 'text-light' : 'text-danger' }}">{{ $album->stock }}</span>
                        </div>

                        <a href="{{ route('product.show', $album) }}" class="btn btn-outline-gold btn-sm w-100 mt-auto">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    @if ($q !== '')
                        Tidak ada album yang cocok dengan '{{ $q }}'
                    @else
                        Belum ada album di toko.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $albums->links() }}
    </div>
@endsection
