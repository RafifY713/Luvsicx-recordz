@extends('layouts.app')

@section('title', 'Rental '.$album->title.' - Luvsicx Recordz')

@section('content')
    <h1 class="h3 fw-bold mb-4">Rental Album</h1>

    <div class="row g-4">
        <div class="col-md-5 col-lg-4">
            <div class="card-lxz overflow-hidden">
                @if ($album->cover_url)
                    <img src="{{ $album->cover_url }}" alt="Cover {{ $album->title }}" class="album-cover">
                @else
                    <div class="album-cover-placeholder">{{ $album->initials }}</div>
                @endif
                <div class="p-3">
                    <div class="album-title">{{ $album->title }}</div>
                    <div class="album-artist">{{ $album->artist }}</div>
                    <div class="price mt-2">
                        Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }} <span class="text-muted small">/ hari</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 col-lg-8">
            <div class="card-lxz p-4">
                <form action="{{ route('rental.confirm') }}" method="POST" id="rentalForm">
                    @csrf
                    <input type="hidden" name="album_id" value="{{ $album->id }}">

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date"
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="form-control @error('start_date') is-invalid @enderror" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date"
                                   value="{{ old('end_date') }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="form-control @error('end_date') is-invalid @enderror" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-lxz p-3 mt-4" id="calcBox">
                        <div class="summary-line">
                            <span>Total Hari</span>
                            <span id="dayCount">0 hari</span>
                        </div>
                        <div class="summary-line">
                            <span>Harga per Hari</span>
                            <span class="price">Rp {{ number_format((float) $album->price_rent, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line total">
                            <span>Total Biaya</span>
                            <span class="price" id="totalCost">Rp 0</span>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 d-none" id="dateError">
                        Tanggal selesai tidak boleh sebelum tanggal mulai.
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Lanjut ke Pembayaran</button>
                        <a href="{{ route('product.show', $album) }}" class="btn btn-outline-light-lxz">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const price = {{ (float) $album->price_rent }};
        const start = document.getElementById('start_date');
        const end = document.getElementById('end_date');
        const dayCount = document.getElementById('dayCount');
        const totalCost = document.getElementById('totalCost');
        const dateError = document.getElementById('dateError');
        const form = document.getElementById('rentalForm');

        function formatRupiah(value) {
            return 'Rp ' + Math.round(value).toLocaleString('id-ID');
        }

        function calculate() {
            if (!start.value || !end.value) {
                dayCount.textContent = '0 hari';
                totalCost.textContent = 'Rp 0';
                dateError.classList.add('d-none');
                return;
            }

            const startDate = new Date(start.value + 'T00:00:00');
            const endDate = new Date(end.value + 'T00:00:00');
            const diff = Math.round((endDate - startDate) / 86400000);

            if (diff < 0) {
                dayCount.textContent = '0 hari';
                totalCost.textContent = 'Rp 0';
                dateError.classList.remove('d-none');
                return;
            }

            dateError.classList.add('d-none');
            const days = diff + 1;
            dayCount.textContent = days + ' hari';
            totalCost.textContent = formatRupiah(days * price);
        }

        start.addEventListener('change', function () {
            end.min = start.value;
            calculate();
        });
        end.addEventListener('change', calculate);
        form.addEventListener('submit', function (event) {
            if (!start.value || !end.value || end.value < start.value) {
                event.preventDefault();
                dateError.classList.remove('d-none');
            }
        });
        calculate();
    })();
</script>
@endpush
