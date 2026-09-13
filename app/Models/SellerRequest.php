<?php

namespace App\Models;

use App\Enums\SellerRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'store_name', 'reason', 'experience', 'status', 'reviewed_by', 'reviewed_at', 'admin_note'])]
class SellerRequest extends Model
{
    protected function casts(): array
    {
        return [
            'status' => SellerRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
