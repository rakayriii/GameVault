<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'order_no',
    'buyer_id',
    'seller_id',
    'game_account_id',
    'subtotal',
    'discount_amount',
    'fee_amount',
    'total_amount',
    'payment_method',
    'status',
    'payment_due_at',
    'escrow_deadline',
    'handover_deadline',
    'buyer_confirmed_at',
    'completed_at',
    'cancelled_at',
    'refunded_at',
    'notes',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_due_at' => 'datetime',
            'escrow_deadline' => 'datetime',
            'handover_deadline' => 'datetime',
            'buyer_confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function gameAccount(): BelongsTo
    {
        return $this->belongsTo(GameAccount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function scopeForCustomer($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeForSeller($query, int $userId)
    {
        return $query->where('seller_id', $userId);
    }

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::get(fn () => PaymentMethod::from($this->payment_method)->label());
    }
}
