<?php

namespace App\Models;

use Database\Factories\AccountImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['game_account_id', 'path', 'caption', 'position'])]
class AccountImage extends Model
{
    /** @use HasFactory<AccountImageFactory> */
    use HasFactory;

    public function gameAccount(): BelongsTo
    {
        return $this->belongsTo(GameAccount::class);
    }

    public function url(): ?string
    {
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }
}
