<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &middot; Luvsicx Recordz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/luvsicxz.css') }}" rel="stylesheet">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <!-- 
                LOGO PLACEHOLDER
                Logo saat ini kosong (src="").
                Untuk mengisi:
                1. Simpan file di public/images/logo.png
                2. Ganti src="" menjadi src="{{ asset('images/logo.png') }}"
                3. Ukuran rekomendasi: 200x50 px (navbar), 400x100 px (login)
                4. Format: PNG dengan background transparan
            -->
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center">
                <img src="{{ asset('images/LUVSICX-removebg-preview.png') }}"
                     alt="Luvsicx Recordz"
                     class="logo-img"
                     style="height: 40px; width: auto; object-fit: contain;">
            </a>
            <div class="brand-sub">Admin Panel</div>

            <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="side-link {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}"
               href="{{ route('admin.albums.index') }}">Albums</a>
            <a class="side-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') }}">
                Pesanan
                <span class="count">{{ \App\Models\Order::where('status', 'pending')->count() }}</span>
            </a>
            <a class="side-link {{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}"
               href="{{ route('admin.rentals.index') }}">Rentals</a>

            <hr class="my-4" style="border-color: rgba(212,175,55,.35)">
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="side-link w-100 text-start" style="background:transparent;">Logout</button>
            </form>
        </aside>

        <main class="admin-main">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 admin-page-title mb-0">@yield('heading', 'Dashboard')</h1>
                    <div class="admin-page-sub mb-0">Halo, {{ auth()->user()->name }}</div>
                </div>
                <div class="text-muted small">{{ now()->format('d M Y') }}</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
