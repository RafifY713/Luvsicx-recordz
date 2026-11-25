@extends('layouts.app')

@section('title', 'Pembayaran Denda - Luvsicx Recordz')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card-lxz p-4">
                <div class="text-center mb-4">
                    <div class="text-muted small">Kode Order</div>
                    <div class="fw-bold fs-5 font-monospace">{{ $order->order_code }}</div>
                    <div class="mt-3 text-muted">Total Denda</div>
                    <div style="color:#800020; font-size:2.1rem; font-weight:800;">
                        Rp {{ number_format((float) $fineRentals->sum('late_fee'), 0, ',', '.') }}
                    </div>
                </div>

                <div class="fine-alert mb-4">
                    <div class="fw-bold mb-2">Rental Terlambat</div>
                    @foreach ($fineRentals as $rental)
                        <div class="d-flex flex-wrap justify-content-between gap-2 small py-1">
                            <span>{{ $rental->album->title }}</span>
                            <span>
                                Selesai {{ $rental->end_date->format('d M Y') }}
                                &middot; Terlambat {{ $rental->late_days }} hari
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mb-4">
                    <div class="qr-box">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate($order->order_code.'-FINE') !!}
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        Scan QR di atas, transfer sesuai total denda, lalu upload bukti transfer di bawah.
                    </p>
                </div>

                @if ($fineRentals->firstWhere('late_fee_proof', '!=', null))
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/'.$fineRentals->firstWhere('late_fee_proof', '!=', null)->late_fee_proof) }}"
                             alt="Bukti bayar denda" class="proof-preview">
                        <div class="badge-proof-pending d-inline-block mb-3">Menunggu Verifikasi Denda</div>
                        <div class="text-muted small">Bukti denda sudah di-upload. Menunggu verifikasi admin.</div>
                    </div>
                @endif

                <form id="form-denda" action="{{ route('order.pay-fine.submit', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="proof-upload-area mb-1" onclick="document.getElementById('late_fee_proof').click()">
                        <div class="fw-semibold mb-1">Upload Bukti Bayar Denda</div>
                        <div class="text-muted small">Klik untuk pilih foto (JPG, PNG, WebP &middot; maksimal 2MB)</div>
                        <input type="file" name="late_fee_proof" id="late_fee_proof" accept="image/*"
                               class="d-none @error('late_fee_proof') is-invalid @enderror" required>
                        <img id="fine-proof-preview" src="#" alt="Pratinjau bukti denda" class="proof-preview d-none">
                    </div>
                    @error('late_fee_proof')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Upload Bukti Denda</button>
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-light-lxz">Bayar Nanti</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const fineProofInput = document.getElementById('late_fee_proof');
        const fineProofPreview = document.getElementById('fine-proof-preview');

        fineProofInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                fineProofPreview.src = URL.createObjectURL(this.files[0]);
                fineProofPreview.classList.remove('d-none');
            } else {
                fineProofPreview.classList.add('d-none');
            }
        });
    </script>
@endpush
