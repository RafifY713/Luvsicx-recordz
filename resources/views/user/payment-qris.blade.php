@extends('layouts.app')

@section('title', 'Pembayaran QRIS - Luvsicx Recordz')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            @if ($order->status === 'rejected')
                <div class="fine-alert mb-4">
                    Pesanan ditolak admin. Silakan upload ulang bukti bayar yang valid.
                </div>
            @endif

            <div class="card-lxz p-4">
                <div class="text-center mb-4">
                    <div class="text-muted small">Kode Order</div>
                    <div class="fw-bold fs-5 font-monospace">{{ $order->order_code }}</div>
                    <div class="mt-3 text-muted">Total Tagihan</div>
                    <div class="amount-display">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
                </div>

                <div class="text-center mb-4">
                    <div class="qr-box">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate($order->order_code) !!}
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        Scan QR di atas, transfer sesuai total tagihan, lalu upload bukti transfer di bawah.
                    </p>
                </div>

                @if ($order->payment_proof)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/'.$order->payment_proof) }}" alt="Bukti bayar"
                             class="proof-preview">
                        <div class="badge-proof-pending d-inline-block mb-3">Menunggu Verifikasi Admin</div>
                        <div class="text-muted small mb-3">Bukti bayar sudah di-upload. Menunggu verifikasi admin.</div>
                        <a href="#form-upload" class="btn btn-outline-gold btn-sm">Upload Ulang Bukti</a>
                    </div>
                @endif

                <form id="form-upload" action="{{ route('order.pay.submit', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="proof-upload-area mb-1" onclick="document.getElementById('payment_proof').click()">
                        <div class="fw-semibold mb-1">Upload Bukti Transfer</div>
                        <div class="text-muted small">Klik untuk pilih foto (JPG, PNG, WebP &middot; maksimal 2MB)</div>
                        <input type="file" name="payment_proof" id="payment_proof" accept="image/*"
                               class="d-none @error('payment_proof') is-invalid @enderror" required>
                        <img id="proof-preview" src="#" alt="Pratinjau bukti bayar" class="proof-preview d-none">
                    </div>
                    @error('payment_proof')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">Upload Bukti</button>
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-light-lxz">Bayar Nanti</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const proofInput = document.getElementById('payment_proof');
        const proofPreview = document.getElementById('proof-preview');

        proofInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                proofPreview.src = URL.createObjectURL(this.files[0]);
                proofPreview.classList.remove('d-none');
            } else {
                proofPreview.classList.add('d-none');
            }
        });
    </script>
@endpush
