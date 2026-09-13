<?php

namespace App\Providers;

use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.navbar', function ($view) {
            $view->with('topGames', $this->topGames());
            $view->with('wishlistCount', auth()->check() ? auth()->user()->wishlists()->count() : 0);
            $view->with('notificationCount', auth()->check() ? auth()->user()->unreadNotifications()->count() : 0);
        });
    }

    /**
     * Resolve the top games shown in the navigation trust strip.
     *
     * @return Collection<int, Game>
     */
    private function topGames()
    {
        try {
            return Game::query()->where('is_active', true)->orderBy('sort_order')->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
