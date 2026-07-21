<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Render (dan hosting lain yang pakai proxy HTTPS di depan) meneruskan
        // request ke aplikasi sebagai HTTP biasa di belakang layar. Baris ini
        // memaksa Laravel tetap generate semua URL (termasuk form login) pakai
        // https:// saat aplikasi berjalan di mode production.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}