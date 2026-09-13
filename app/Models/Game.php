<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'slug', 'icon_color', 'icon_url', 'banner_url', 'description', 'is_active', 'sort_order'])]
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(GameAccount::class);
    }

    public function iconUrl(): ?string
    {
        return $this->icon_url ? Storage::disk('public')->url($this->icon_url) : null;
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_url ? Storage::disk('public')->url($this->banner_url) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
