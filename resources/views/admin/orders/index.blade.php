@extends('layouts.admin')

@section('title', 'Pesanan')
@section('heading', 'Manajemen Order')

@section('content')
    <ul class="nav nav-tabs nav-tabs-lxz mb-3">
        @foreach (['all' => 'Semua', 'pending' => 'Pending', 'accepted' => 'Accepted', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                   href="{{ route('admin.orders.index', ['status' => $key]) }}">
                    {{ $label }}
                    <span class="text-muted small">({{ $counts[$key] }})</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card-lxz p-3">
        <div class="table-responsive">
            <table class="table table-lxz align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th class="text-end">Total</th>
                        <th>Metode</th>
                        <th>Bukti Bayar</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="fw-semibold text-nowrap">{{ $order->order_code }}</td>
                            <td>
                                <div>{{ $order->user->name }}</div>
                                <div class="text-muted small">{{ $order->user->email }}</div>
                            </td>
                            <td class="text-muted small text-nowrap">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="small" style="max-width: 240px;">
                                @foreach ($order->items as $item)
                                    <div>{{ $item->album->title }} <span class="text-muted">({{ $item->type }})</span></div>
                                @endforeach
                            </td>
                            <td class="text-end price text-nowrap">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge-lxz badge-dark-border">{{ strtoupper($order->payment_method) }}</span></td>
                            <td class="text-center">
                                @if ($order->payment_proof)
                                    <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" rel="noopener" title="Lihat bukti bayar">&#128206;</a>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td><span class="badge-lxz {{ $order->paymentClass() }}">{{ $order->paymentLabel() }}</span></td>
                            <td><span class="badge-lxz {{ $order->statusClass() }}">{{ $order->statusLabel() }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-gold">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Tidak ada order pada tab ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
