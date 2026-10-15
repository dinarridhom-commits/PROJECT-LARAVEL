<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
     * Note: TrustProxies sudah dikonfigurasi di bootstrap/app.php dengan trustProxies(at: '*')
     */
    public function boot(): void
    {
        // Force HTTPS scheme untuk Railway/cloud (SSL terminate di load balancer)
        // X-Forwarded-Proto diteruskan oleh proxy Railway ke container
        if (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            URL::forceScheme('https');
        }
    }
}
