<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


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
        // Disponible en TODAS las vistas: $cartCount
        View::composer('*', function ($view) {
            $cartCount = collect(session('cart', []))->sum('quantity');
            $view->with('cartCount', $cartCount);
        });
    }
}
