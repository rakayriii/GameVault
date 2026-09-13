<?php

namespace App\Models;

use App\Enums\DisputePriority;
use App\Enums\DisputeStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'case_no',
    'order_id',
    'opened_by',
    'opponent_id',
    'resolved_by',
    'category',
    'reason',
    'description',
    'status',
    'priority',
    'evidence',
    'escalated_to_arbitration',
    'arbitration_result',
    'resolution',
    'resolution_note',
    'resolved_at',
])]
class Dispute extends Model
{
    protected function casts(): array
    {
        return [
            'status' => DisputeStatus::class,
            'priority' => DisputePriority::class,
            'evidence' => 'array',
            'escalated_to_arbitration' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function opponent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class)->orderByDesc('created_at');
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->category) {
            'account_unreceived' => 'Akun tidak diterima',
            'account_not_match' => 'Akun tidak sesuai',
            'credentials_invalid' => 'Kredensial gagal',
            default => 'Lainnya',
        });
    }
}
