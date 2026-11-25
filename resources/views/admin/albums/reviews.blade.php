@extends('layouts.admin')

@section('title', 'Review Album')
@section('heading', 'Review Album')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="fw-bold">{{ $album->title }}</div>
            <div class="text-muted small">
                {{ $album->artist }} &middot;
                <span class="text-gold">&#9733; {{ number_format((float) $album->rating_avg, 1) }}</span>
                ({{ $album->rating_count }} review)
            </div>
        </div>
        <a href="{{ route('admin.albums.index') }}" class="btn btn-outline-light-lxz btn-sm">Kembali ke Albums</a>
    </div>

    <div class="card-lxz p-3">
        <div class="table-responsive">
            <table class="table table-lxz align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $review->user->name }}</div>
                                <div class="text-muted small">{{ $review->user->email }}</div>
                            </td>
                            <td class="text-gold text-nowrap">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? '' : 'star-empty' }}">&#9733;</span>
                                @endfor
                            </td>
                            <td style="max-width: 380px;">{{ $review->comment ?: '-' }}</td>
                            <td>
                                @if ($review->is_verified_purchase)
                                    <span class="badge-verified">Verified</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $review->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                      onsubmit="return confirm('Hapus review ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada review untuk album ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection
