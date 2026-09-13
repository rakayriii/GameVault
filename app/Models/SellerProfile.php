<?php

namespace App\Models;

use Database\Factories\SellerProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'store_name', 'slug', 'bio', 'banner_url', 'membership_tier', 'is_official_verified', 'total_sales', 'rating_cache', 'response_time_minutes', 'announcement'])]
class SellerProfile extends Model
{
    /** @use HasFactory<SellerProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_official_verified' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(GameAccount::class, 'seller_id', 'user_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(SellerFollower::class, 'seller_id', 'user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
