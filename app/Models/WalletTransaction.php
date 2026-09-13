<?php

namespace App\Models;

use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['wallet_id', 'type', 'direction', 'amount', 'balance_after', 'description', 'reference_type', 'reference_id', 'status', 'meta'])]
class WalletTransaction extends Model
{
    protected function casts(): array
    {
        return [
            'type' => WalletTransactionType::class,
            'direction' => WalletDirection::class,
            'status' => WalletTransactionStatus::class,
            'meta' => 'array',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
