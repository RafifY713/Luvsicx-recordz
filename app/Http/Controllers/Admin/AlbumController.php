<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::latest()->paginate(10);

        return view('admin.albums.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.albums.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAlbum($request);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = basename(
                $request->file('cover_image')->store('albums', 'public')
            );
        }

        Album::create($validated);

        return redirect()->route('admin.albums.index')
            ->with('success', 'Album berhasil ditambahkan.');
    }

    public function edit(Album $album)
    {
        return view('admin.albums.edit', compact('album'));
    }

    public function update(Request $request, Album $album)
    {
        $validated = $this->validateAlbum($request);

        if ($request->hasFile('cover_image')) {
            if ($album->cover_image) {
                Storage::disk('public')->delete('albums/'.$album->cover_image);
            }

            $validated['cover_image'] = basename(
                $request->file('cover_image')->store('albums', 'public')
            );
        }

        $album->update($validated);

        return redirect()->route('admin.albums.index')
            ->with('success', 'Album berhasil diperbarui.');
    }

    public function destroy(Album $album)
    {
        if ($album->orderItems()->exists() || $album->rentals()->exists()) {
            return back()->with('error', 'Album tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        if ($album->cover_image) {
            Storage::disk('public')->delete('albums/'.$album->cover_image);
        }

        $album->delete();

        return back()->with('success', 'Album berhasil dihapus.');
    }

    public function reviews(Album $album)
    {
        $reviews = Review::with(['user', 'order.items'])
            ->where('album_id', $album->id)
            ->latest()
            ->paginate(20);

        return view('admin.albums.reviews', compact('album', 'reviews'));
    }

    public function destroyReview(Review $review)
    {
        $album = $review->album;

        $review->delete();

        $album->rating_count = $album->reviews()->count();
        $album->rating_avg = $album->reviews()->avg('rating') ?? 0;
        $album->save();

        return back()->with('success', 'Review berhasil dihapus.');
    }

    private function validateAlbum(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'release_date' => ['nullable', 'date'],
            'genre' => ['nullable', 'string', 'max:100'],
            'label' => ['nullable', 'string', 'max:100'],
            'price_buy' => ['required', 'numeric', 'min:0'],
            'price_rent' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}
