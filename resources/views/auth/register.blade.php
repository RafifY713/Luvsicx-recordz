<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar &middot; Luvsicx Recordz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/luvsicxz.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="text-center mb-4">
                <img src="{{ asset('images/LUVSICX-removebg-preview.png') }}"
                     alt="Luvsicx Recordz"
                     class="logo-img-login"
                     style="height: 80px; width: auto; object-fit: contain;">
                <p class="text-muted mt-2 mb-0">Buat akun baru</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="100"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nama lengkap" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="nama@email.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Daftar</button>
            </form>

            <p class="text-center text-muted mt-4 mb-0">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gold">Masuk di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
