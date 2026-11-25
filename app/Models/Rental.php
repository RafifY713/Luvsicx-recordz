<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    public const LATE_FEE_PER_DAY = 10000;

    protected $fillable = [
        'order_id',
        'album_id',
        'start_date',
        'end_date',
        'status',
        'late_days',
        'late_fee',
        'late_fee_paid',
        'late_fee_paid_at',
        'late_fee_proof',
        'late_fee_proof_uploaded_at',
        'late_fee_verified_at',
        'late_fee_verified_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'late_fee_paid' => 'boolean',
            'late_fee_paid_at' => 'datetime',
            'late_fee_proof_uploaded_at' => 'datetime',
            'late_fee_verified_at' => 'datetime',
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

    public function canComplete(): bool
    {
        return $this->status === 'active' && now()->startOfDay()->gte($this->end_date);
    }

    public function calculateLateFee(): void
    {
        if ($this->status !== 'active') {
            return;
        }

        if (now()->lte($this->end_date)) {
            return;
        }

        if ($this->late_fee_paid) {
            return;
        }

        $lateDays = $this->end_date->diffInDays(now());

        if ($lateDays < 1) {
            $lateDays = 1;
        }

        $this->late_days = $lateDays;
        $this->late_fee = $lateDays * self::LATE_FEE_PER_DAY;
        $this->save();
    }
}
