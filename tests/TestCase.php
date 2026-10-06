<?php

namespace ScBudgetWeb\CookieConsent\Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase as Orchestra;
use ScBudgetWeb\CookieConsent\CookieConsentServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CookieConsentServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.name', 'Example Site');
        $app['config']->set('cookies.enabled', true);
    }

    /**
     * Makes the current request carry a consent cookie with the given choices.
     *
     * @param  array<string, mixed>|string|null  $choices  Choices (revision 1 is added), a raw cookie value, or null for no cookie.
     */
    protected function withConsentCookie(array|string|null $choices): void
    {
        $cookies = [];

        if ($choices !== null) {
            $cookies['cookie_preferences'] = is_array($choices) ? json_encode(['revision' => 1] + $choices) : $choices;
        }

        $this->app->instance('request', Request::create('/', 'GET', [], $cookies));
    }
}
