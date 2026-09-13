<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Database\Factories\GameAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'seller_id',
    'game_id',
    'title',
    'slug',
    'description',
    'price',
    'strike_price',
    'discount_percent',
    'server',
    'region',
    'rank',
    'rank_tier',
    'level',
    'heros_count',
    'skins_count',
    'winrate',
    'in_game_balance',
    'rarity_metrics',
    'features',
    'instant_delivery',
    'is_featured',
    'handover_data',
    'handover_note',
    'status',
    'rejection_reason',
    'views_count',
    'published_at',
    'sold_at',
])]
class GameAccount extends Model
{
    /** @use HasFactory<GameAccountFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'instant_delivery' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sold_at' => 'datetime',
            'status' => ListingStatus::class,
            'rarity_metrics' => 'array',
            'features' => 'array',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AccountImage::class)->orderBy('position');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function finalPrice(): int
    {
        return $this->price;
    }

    public function isApproved(): bool
    {
        return $this->status === ListingStatus::Approved;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ListingStatus::Approved);
    }
}
