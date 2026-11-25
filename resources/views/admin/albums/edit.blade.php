@extends('layouts.admin')

@section('title', 'Edit Album')
@section('heading', 'Edit Album')

@section('content')
    <div class="card-lxz p-4" style="max-width: 820px;">
        <form action="{{ route('admin.albums.update', $album) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Judul</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $album->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="artist" class="form-label">Artis</label>
                    <input type="text" name="artist" id="artist" value="{{ old('artist', $album->artist) }}"
                           class="form-control @error('artist') is-invalid @enderror" required>
                    @error('artist')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $album->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="cover_image" class="form-label">Cover Baru (opsional)</label>
                    <input type="file" name="cover_image" id="cover_image" accept=".jpg,.jpeg,.png,.webp"
                           class="form-control @error('cover_image') is-invalid @enderror">
                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="mt-2">
                        @if ($album->cover_url)
                            <img src="{{ $album->cover_url }}" alt="Cover saat ini" class="cover-thumb">
                        @else
                            <div class="cover-thumb-placeholder">{{ $album->initials }}</div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="release_date" class="form-label">Tanggal Rilis</label>
                    <input type="date" name="release_date" id="release_date"
                           value="{{ old('release_date', $album->release_date?->format('Y-m-d')) }}"
                           class="form-control @error('release_date') is-invalid @enderror">
                    @error('release_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="genre" class="form-label">Genre</label>
                    <input type="text" name="genre" id="genre" value="{{ old('genre', $album->genre) }}"
                           class="form-control @error('genre') is-invalid @enderror">
                    @error('genre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="label" class="form-label">Label</label>
                    <input type="text" name="label" id="label" value="{{ old('label', $album->label) }}"
                           class="form-control @error('label') is-invalid @enderror">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="price_buy" class="form-label">Harga Beli (Rp)</label>
                    <input type="number" name="price_buy" id="price_buy" min="0" step="0.01"
                           value="{{ old('price_buy', $album->price_buy) }}"
                           class="form-control @error('price_buy') is-invalid @enderror" required>
                    @error('price_buy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="price_rent" class="form-label">Harga Rental / Hari (Rp)</label>
                    <input type="number" name="price_rent" id="price_rent" min="0" step="0.01"
                           value="{{ old('price_rent', $album->price_rent) }}"
                           class="form-control @error('price_rent') is-invalid @enderror">
                    @error('price_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="stock" class="form-label">Stok</label>
                    <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', $album->stock) }}"
                           class="form-control @error('stock') is-invalid @enderror" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">Update Album</button>
                <a href="{{ route('admin.albums.index') }}" class="btn btn-outline-light-lxz">Batal</a>
            </div>
        </form>
    </div>
@endsection
