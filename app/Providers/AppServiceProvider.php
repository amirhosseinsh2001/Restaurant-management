<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
//        $this->app->bind(DeskRepositoryInterface::class, DeskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();
//        RateLimiter::for('auth-register', fn(Request $request) =>[
//            Limit::perMinute(10)->by(sprintf('%s|%s', $request->ip(), (string) $request->input('email'))),
//        ]);
    }
}
