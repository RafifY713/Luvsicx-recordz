@extends('layouts.app')

@section('title', 'Wishlist - Luvsicx Recordz')

@section('content')
    <h1 class="h3 fw-bold mb-4">Wishlist</h1>

    <div class="row g-4">
        @forelse ($wishlists as $wishlist)
            @php $album = $wishlist->album; @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="album-card h-100 d-flex flex-column">
                    <a href="{{ route('product.show', $album) }}">
                        @if ($album->cover_url)
                            <img src="{{ $album->cover_url }}" alt="Cover {{ $album->title }}" class="album-cover">
                        @else
                            <div class="album-cover-placeholder">{{ $album->initials }}</div>
                        @endif
                    </a>
                    <div class="album-card-body d-flex flex-column flex-grow-1">
                        <div class="album-title">{{ $album->title }}</div>
                        <div class="album-artist">{{ $album->artist }}</div>

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
                        <div class="small mb-3 d-flex justify-content-between">
                            <span class="text-muted">Rental / hari</span>
                            @if ($album->price_rent !== null)
                                <span class="price">Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>

                        <div class="mt-auto d-flex flex-column gap-2">
                            <a href="{{ route('product.show', $album) }}" class="btn btn-outline-gold btn-sm">Lihat Detail</a>
                            <form action="{{ route('wishlist.remove', $album) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">Hapus dari Wishlist</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">Wishlist kamu masih kosong.</div>
            </div>
        @endforelse
    </div>
@endsection
