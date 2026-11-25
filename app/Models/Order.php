<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_proof',
        'payment_proof_uploaded_at',
        'payment_verified_at',
        'payment_verified_by',
        'paid_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'payment_proof_uploaded_at' => 'datetime',
            'payment_verified_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function paymentVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function getIsProofUploadedAttribute(): bool
    {
        return $this->payment_proof !== null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Request is Pending',
            'accepted' => 'Your order has been accepted',
            'completed' => 'Order Completed',
            'rejected' => 'Order Rejected',
            default => ucfirst($this->status),
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'pending' => 'badge-gold',
            'accepted' => 'badge-success',
            'completed' => 'badge-muted',
            'rejected' => 'badge-maroon',
            default => 'badge-muted',
        };
    }

    public function paymentLabel(): string
    {
        if ($this->payment_method === 'qris') {
            return $this->payment_status === 'paid' ? 'Barang sudah dibayar' : 'Belum Dibayar';
        }

        return $this->payment_status === 'paid' ? 'Lunas (COD)' : 'COD - Bayar di Tempat';
    }

    public function paymentClass(): string
    {
        if ($this->payment_status === 'paid') {
            return 'badge-success';
        }

        return $this->payment_method === 'qris' ? 'badge-maroon' : 'badge-muted';
    }
}
