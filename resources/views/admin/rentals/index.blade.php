@extends('layouts.admin')

@section('title', 'Rentals')
@section('heading', 'Manajemen Rental')

@section('content')
    <div class="card-lxz p-3">
        <div class="table-responsive">
            <table class="table table-lxz align-middle">
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>User</th>
                        <th>Album</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rentals as $rental)
                        <tr>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.orders.show', $rental->order) }}">{{ $rental->order->order_code }}</a>
                            </td>
                            <td>
                                <div>{{ $rental->order->user->name }}</div>
                                <div class="text-muted small">{{ $rental->order->user->email }}</div>
                            </td>
                            <td>{{ $rental->album->title }}</td>
                            <td class="text-muted small text-nowrap">{{ $rental->start_date->format('d M Y') }}</td>
                            <td class="text-muted small text-nowrap">{{ $rental->end_date->format('d M Y') }}</td>
                            <td>
                                @if ($rental->status === 'unpaid')
                                    <span class="badge-lxz badge-maroon">Unpaid</span>
                                @elseif ($rental->status === 'active')
                                    <span class="badge-lxz badge-gold">Active</span>
                                @else
                                    <span class="badge-lxz badge-muted">Completed</span>
                                @endif
                            </td>
                            <td>
                                @if ((float) $rental->late_fee > 0 && ! $rental->late_fee_paid)
                                    <div><span class="badge-late">Terlambat {{ $rental->late_days }} hari</span></div>
                                    <div class="text-warning small mt-1">Denda: Rp {{ number_format((float) $rental->late_fee, 0, ',', '.') }}</div>
                                    @if ($rental->late_fee_proof)
                                        <div class="mt-1">
                                            <span class="badge-proof-pending">Menunggu Verifikasi</span>
                                            <a href="{{ asset('storage/'.$rental->late_fee_proof) }}" target="_blank" rel="noopener" class="small">Lihat bukti</a>
                                        </div>
                                    @else
                                        <div class="mt-1"><span class="badge-proof-none">Belum Upload Bukti</span></div>
                                    @endif
                                @elseif ((float) $rental->late_fee > 0 && $rental->late_fee_paid)
                                    <div><span class="badge-late-paid">Denda Lunas</span></div>
                                    <div class="text-muted small mt-1">{{ $rental->late_fee_paid_at?->format('d M Y H:i') }}</div>
                                @elseif ((float) $rental->late_fee == 0 && $rental->status === 'completed')
                                    <span class="badge-on-time">Tepat waktu</span>
                                @elseif ($rental->status === 'active' && now()->gt($rental->end_date))
                                    <span class="badge-late">Terlambat {{ max(1, $rental->end_date->diffInDays(now())) }} hari</span>
                                @elseif ($rental->status === 'active')
                                    <span class="badge-active-rental">Aktif</span>
                                @elseif ($rental->status === 'unpaid')
                                    <span class="badge-lxz badge-muted">Belum bayar</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.rentals.show', $rental) }}" class="btn btn-sm btn-outline-gold mb-1">Detail</a>
                                @if ($rental->status === 'completed')
                                    <span class="text-muted small">&mdash;</span>
                                @elseif ($rental->status === 'unpaid')
                                    <button type="button" class="btn btn-sm btn-outline-gold" disabled
                                            title="Belum dibayar, tidak bisa complete">Complete</button>
                                @elseif ($rental->canComplete())
                                    <form action="{{ route('admin.rentals.update', $rental) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-primary">Complete</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-gold" disabled
                                            title="Belum jatuh tempo">Complete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada rental.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $rentals->links() }}
        </div>
    </div>
@endsection
