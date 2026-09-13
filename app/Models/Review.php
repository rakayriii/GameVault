<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'reviewer_id', 'seller_id', 'game_account_id', 'rating', 'content', 'verified_escrow'])]
class Review extends Model
{
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'verified_escrow' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function gameAccount(): BelongsTo
    {
        return $this->belongsTo(GameAccount::class);
    }
}
