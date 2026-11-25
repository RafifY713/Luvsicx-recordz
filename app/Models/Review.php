<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'album_id',
        'order_id',
        'rating',
        'comment',
        'is_verified_purchase',
    ];

    protected function casts(): array
    {
        return [
            'is_verified_purchase' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchaseType(): ?string
    {
        if (! $this->order) {
            return null;
        }

        return $this->order->items->firstWhere('album_id', $this->album_id)?->type;
    }
}
