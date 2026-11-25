<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'album_id',
        'type',
        'quantity',
        'rental_days',
        'rental_start_date',
        'rental_end_date',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'rental_start_date' => 'date',
            'rental_end_date' => 'date',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
