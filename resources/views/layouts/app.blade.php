<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luvsicx Recordz')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/luvsicxz.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-luvsicx sticky-top">
        <div class="container">
            <!-- 
                LOGO PLACEHOLDER
                Logo saat ini kosong (src="").
                Untuk mengisi:
                1. Simpan file di public/images/logo.png
                2. Ganti src="" menjadi src="{{ asset('images/logo.png') }}"
                3. Ukuran rekomendasi: 200x50 px (navbar), 400x100 px (login)
                4. Format: PNG dengan background transparan
            -->
            <a href="{{ route('shop.index') }}" class="navbar-brand d-flex align-items-center">
                <img src="{{ asset('images/LUVSICX-removebg-preview.png') }}"
                     alt="Luvsicx Recordz"
                     class="logo-img"
                     style="height: 40px; width: auto; object-fit: contain;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.*', 'product.*') ? 'active' : '' }}"
                           href="{{ route('shop.index') }}">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}"
                           href="{{ route('wishlist.index') }}">
                            Wishlist
                            <span class="nav-count-badge">{{ auth()->user()->wishlists()->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cart.*', 'checkout.*') ? 'active' : '' }}"
                           href="{{ route('cart.index') }}">
                            Cart
                            <span class="nav-count-badge">{{ auth()->user()->cart?->items()->sum('quantity') ?? 0 }}</span>
                        </a>
                    </li>
                </ul>

                <form class="d-flex navbar-search my-2 my-lg-0 me-lg-3" action="{{ route('shop.index') }}" method="GET" role="search">
                    <input class="form-control me-2" type="search" name="q" value="{{ request('q') }}"
                           placeholder="Cari album atau artis..." aria-label="Search">
                    <button class="btn btn-outline-gold" type="submit">Cari</button>
                </form>

                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Halo, {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-lxz">
                            <li><a class="dropdown-item" href="{{ route('orders.history') }}">Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
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

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
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

    <footer class="site-footer">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <div><span class="text-gold fw-bold">Luvsicx Recordz</span> &middot; Toko musik premium.</div>
            <div>&copy; {{ date('Y') }} Luvsicx Recordz</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
