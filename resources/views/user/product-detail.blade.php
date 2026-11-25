@extends('layouts.app')

@section('title', $album->title.' - Luvsicx Recordz')

@section('content')
    <div class="row g-4 align-items-start">
        <div class="col-md-5 col-lg-4">
            @if ($album->cover_url)
                <img src="{{ $album->cover_url }}" alt="Cover {{ $album->title }}" class="product-cover">
            @else
                <div class="product-cover-placeholder">{{ $album->initials }}</div>
            @endif
        </div>

        <div class="col-md-7 col-lg-8">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-1">{{ $album->title }}</h1>
                    <p class="text-muted mb-2">{{ $album->artist }}</p>
                </div>
                <form action="{{ route('wishlist.add', $album) }}" method="POST">
                    @csrf
                    <button type="submit" class="wishlist-btn {{ $inWishlist ? 'active' : '' }}"
                            title="{{ $inWishlist ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
                        {{ $inWishlist ? '♥' : '♡' }}
                    </button>
                </form>
            </div>

            <div class="mb-3">
                @if ($album->rating_count > 0)
                    <span class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= round((float) $album->rating_avg) ? '' : 'star-empty' }}">&#9733;</span>
                        @endfor
                    </span>
                    <span class="fw-semibold ms-1">{{ number_format((float) $album->rating_avg, 1) }}</span>
                    <span class="text-muted">({{ $album->rating_count }} review)</span>
                @else
                    <span class="text-muted">Belum ada review</span>
                @endif
            </div>

            @if ($album->description)
                <p class="text-secondary">{{ $album->description }}</p>
            @endif

            <div class="card-lxz p-3 mb-3">
                @if ($album->release_date)
                    <div class="spec-row">
                        <span class="spec-label">Tanggal Rilis</span>
                        <span>{{ $album->release_date->format('d M Y') }}</span>
                    </div>
                @endif
                @if ($album->genre)
                    <div class="spec-row">
                        <span class="spec-label">Genre</span>
                        <span>{{ $album->genre }}</span>
                    </div>
                @endif
                @if ($album->label)
                    <div class="spec-row">
                        <span class="spec-label">Label</span>
                        <span>{{ $album->label }}</span>
                    </div>
                @endif
                <div class="spec-row border-0">
                    <span class="spec-label">Stok</span>
                    <span class="{{ $album->stock > 0 ? 'text-light' : 'text-danger' }}">
                        {{ $album->stock }}
                        @if ($album->stock <= 0)
                            &middot; Stok Habis
                        @endif
                    </span>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <div class="card-lxz p-3 h-100">
                        <div class="text-muted small mb-1">Harga Beli</div>
                        <div class="price-lg">Rp {{ number_format((float) $album->price_buy, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card-lxz p-3 h-100">
                        <div class="text-muted small mb-1">Harga Rental / Hari</div>
                        <div class="price-lg">
                            @if ($album->price_rent !== null)
                                Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }}
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="album_id" value="{{ $album->id }}">
                    <input type="hidden" name="type" value="buy">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary px-4" {{ $album->stock <= 0 ? 'disabled' : '' }}>
                        {{ $album->stock <= 0 ? 'Stok Habis' : 'Beli Album' }}
                    </button>
                </form>

                @if ($album->stock <= 0)
                    <span class="btn btn-outline-gold px-4 disabled">Rental</span>
                @elseif ($album->price_rent === null)
                    <span class="btn btn-outline-light-lxz px-4 disabled">Rental Tidak Tersedia</span>
                @else
                    <a href="{{ route('rental.select', $album) }}" class="btn btn-outline-gold px-4">Rental</a>
                @endif
            </div>
        </div>
    </div>

    <section id="reviews" class="mt-5">
        <h2 class="h4 section-title">Review &amp; Rating</h2>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="rating-summary">
                    <div class="rating-big">{{ number_format((float) $album->rating_avg, 1) }}</div>
                    <div class="star-rating mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= round((float) $album->rating_avg) ? '' : 'star-empty' }}">&#9733;</span>
                        @endfor
                    </div>
                    <div class="text-muted mb-3">{{ $album->rating_count }} review</div>

                    @foreach ([5, 4, 3, 2, 1] as $star)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="small text-muted" style="width: 14px;">{{ $star }}</span>
                            <div class="rating-bar flex-grow-1">
                                <div class="rating-bar-fill" style="width: {{ $distribution[$star]['percent'] }}%"></div>
                            </div>
                            <span class="small text-muted" style="width: 24px; text-align:right;">{{ $distribution[$star]['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-8">
                @if ($userReview)
                    <div class="review-card border-gold" style="border-color:#D4AF37;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="review-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <strong>{{ auth()->user()->maskedName() }}</strong>
                                    <span class="badge-lxz badge-gold">Review Anda</span>
                                </div>
                                <div class="star-rating my-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $userReview->rating ? '' : 'star-empty' }}">&#9733;</span>
                                    @endfor
                                </div>
                                <div class="text-muted small mb-2">{{ $userReview->created_at->format('d M Y') }}</div>
                                @if ($userReview->comment)
                                    <p class="mb-2">{{ $userReview->comment }}</p>
                                @endif
                                <form action="{{ route('review.destroy', $userReview) }}" method="POST"
                                      onsubmit="return confirm('Hapus review Anda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus Review</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif ($canReview)
                    <div class="card-lxz p-3 mb-3">
                        <h3 class="h6 fw-bold mb-3">Tulis Review</h3>
                        <form action="{{ route('review.store', $album) }}" method="POST">
                            @csrf
                            <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating') }}">

                            <div class="mb-2">
                                <div class="star-input" id="starInput">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">&#9733;</span>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <textarea name="comment" id="reviewComment" rows="4" maxlength="500"
                                          class="form-control @error('comment') is-invalid @enderror"
                                          placeholder="Ceritakan pengalamanmu tentang album ini...">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-end">
                                    <span class="char-counter"><span id="charCount">{{ strlen(old('comment', '')) }}</span>/500</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Kirim Review</button>
                        </form>
                    </div>
                @elseif ($hasPurchased)
                    <div class="alert alert-info mb-3">Anda sudah pernah mereview album ini.</div>
                @else
                    <div class="alert alert-warning mb-3">
                        Hanya pembeli atau penyewa album ini yang bisa memberi review.
                    </div>
                @endif

                @forelse ($reviews as $review)
                    <div class="review-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="review-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <strong>{{ $review->user->maskedName() }}</strong>
                                    @php $purchaseType = $review->purchaseType(); @endphp
                                    @if ($purchaseType === 'buy')
                                        <span class="badge-lxz badge-gold">Pembeli</span>
                                    @elseif ($purchaseType === 'rent')
                                        <span class="badge-lxz badge-gold">Penyewa</span>
                                    @endif
                                </div>
                                <div class="star-rating my-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $review->rating ? '' : 'star-empty' }}">&#9733;</span>
                                    @endfor
                                </div>
                                <div class="text-muted small mb-2">{{ $review->created_at->format('d M Y') }}</div>
                                @if ($review->comment)
                                    <p class="mb-0">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    @if ($album->rating_count == 0)
                        <div class="empty-state">Belum ada review untuk album ini. Jadilah yang pertama!</div>
                    @endif
                @endforelse

                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const starInput = document.getElementById('starInput');
        const ratingValue = document.getElementById('ratingValue');
        if (starInput && ratingValue) {
            const stars = Array.from(starInput.querySelectorAll('.star'));

            function paint(value) {
                stars.forEach(function (star) {
                    star.classList.toggle('active', Number(star.dataset.value) <= value);
                });
            }

            stars.forEach(function (star) {
                star.addEventListener('click', function () {
                    const value = Number(star.dataset.value);
                    ratingValue.value = value;
                    paint(value);
                });

                star.addEventListener('mouseenter', function () {
                    paint(Number(star.dataset.value));
                });
            });

            starInput.addEventListener('mouseleave', function () {
                paint(Number(ratingValue.value) || 0);
            });

            paint(Number(ratingValue.value) || 0);
        }

        const comment = document.getElementById('reviewComment');
        const counter = document.getElementById('charCount');
        if (comment && counter) {
            counter.textContent = comment.value.length;
            comment.addEventListener('input', function () {
                counter.textContent = comment.value.length;
            });
        }
    })();
</script>
@endpush
