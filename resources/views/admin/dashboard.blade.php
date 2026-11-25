@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Total Album</div>
                <div class="stat-value">{{ $totalAlbums }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Total Order</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Order Menunggu ACC</div>
                <div class="stat-value">{{ $pendingOrders }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-label">Order Menunggu Verifikasi</div>
                    <div class="stat-value">{{ $pendingVerifications }}</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.rentals.index', ['fine' => 'unpaid']) }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-label">Denda Belum Dibayar</div>
                    <div class="stat-value">{{ $unpaidFines }}</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Total Review</div>
                <div class="stat-value">{{ $totalReviews }}</div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary px-4">Lihat Pesanan Masuk</a>
@endsection
