<?php

namespace App\Providers;

use App\Services\GoogleOauth\GoogleOauthService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            GoogleOauthService::class,
            fn () => new GoogleOauthService(
                googleClientId: config('services.google-oauth.client-id'),
                googleClientSecret: config('services.google-oauth.client-secret'),
                googleRedirectUri: config('services.google-oauth.redirect-uri')
            )
        );
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->ulid ?: $request->ip());
        });
    }
}
