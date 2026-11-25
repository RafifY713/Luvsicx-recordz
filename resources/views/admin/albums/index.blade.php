@extends('layouts.admin')

@section('title', 'Albums')
@section('heading', 'Manajemen Album')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">{{ $albums->total() }} album</div>
        <a href="{{ route('admin.albums.create') }}" class="btn btn-primary btn-sm px-3">Tambah Album</a>
    </div>

    <div class="card-lxz p-3">
        <div class="table-responsive">
            <table class="table table-lxz align-middle">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Judul</th>
                        <th>Artis</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Rental</th>
                        <th class="text-center">Stok</th>
                        <th>Rating</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($albums as $album)
                        <tr>
                            <td>
                                @if ($album->cover_url)
                                    <img src="{{ $album->cover_url }}" alt="" class="cover-thumb">
                                @else
                                    <div class="cover-thumb-placeholder">{{ $album->initials }}</div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $album->title }}</td>
                            <td class="text-muted">{{ $album->artist }}</td>
                            <td class="text-end price">Rp {{ number_format((float) $album->price_buy, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if ($album->price_rent !== null)
                                    <span class="price">Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $album->stock }}</td>
                            <td class="small">
                                @if ($album->rating_count > 0)
                                    <span class="text-gold">&#9733; {{ number_format((float) $album->rating_avg, 1) }}</span>
                                    <span class="text-muted">({{ $album->rating_count }})</span>
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                                <div>
                                    <a href="{{ route('admin.albums.reviews', $album) }}" class="small">Lihat Review</a>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.albums.edit', $album) }}" class="btn btn-sm btn-outline-gold">Edit</a>
                                    <form action="{{ route('admin.albums.destroy', $album) }}" method="POST"
                                          onsubmit="return confirm('Hapus album ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada album.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $albums->links() }}
        </div>
    </div>
@endsection
