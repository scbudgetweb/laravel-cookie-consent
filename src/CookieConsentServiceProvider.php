<?php

namespace ScBudgetWeb\CookieConsent;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use ScBudgetWeb\CookieConsent\Components\Banner;
use ScBudgetWeb\CookieConsent\Components\Scripts;

class CookieConsentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cookies.php', 'cookies');

        $this->app->singleton(CookieConsent::class);
        $this->app->singleton(CookieCatalogue::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cookies');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'cookies');

        Blade::component('cookie-consent', Banner::class);
        Blade::component('cookie-consent-scripts', Scripts::class);
        Blade::if('cookieConsent', fn (string $category): bool => $this->app->make(CookieConsent::class)->allows($category));

        // The banner writes this cookie in JavaScript, so Laravel must not try to decrypt it.
        EncryptCookies::except((string) config('cookies.cookie_name'));

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/cookies.php' => config_path('cookies.php')], 'cookie-consent-config');
            $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/cookies')], 'cookie-consent-views');
            $this->publishes([__DIR__.'/../lang' => $this->app->langPath('vendor/cookies')], 'cookie-consent-translations');
        }
    }
}
