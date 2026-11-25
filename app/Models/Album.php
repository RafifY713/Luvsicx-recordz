<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'description',
        'cover_image',
        'release_date',
        'genre',
        'label',
        'price_buy',
        'price_rent',
        'stock',
        'rating_avg',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'price_buy' => 'decimal:2',
            'price_rent' => 'decimal:2',
            'rating_avg' => 'decimal:2',
        ];
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/albums/'.$this->cover_image) : null;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->title));

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return strtoupper(substr($words[0], 0, 1).substr(end($words), 0, 1));
    }
}
