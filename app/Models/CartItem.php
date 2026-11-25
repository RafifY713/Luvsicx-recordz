<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'album_id',
        'quantity',
        'type',
        'rental_days',
        'rental_start_date',
        'rental_end_date',
    ];

    protected function casts(): array
    {
        return [
            'rental_start_date' => 'date',
            'rental_end_date' => 'date',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function getSubtotalAttribute(): float
    {
        if ($this->type === 'rent') {
            return (float) ($this->album->price_rent ?? 0) * (int) $this->rental_days * (int) $this->quantity;
        }

        return (float) $this->album->price_buy * (int) $this->quantity;
    }
}
