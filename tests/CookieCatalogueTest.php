<?php

namespace ScBudgetWeb\CookieConsent\Tests;

use ScBudgetWeb\CookieConsent\CookieCatalogue;

class CookieCatalogueTest extends TestCase
{
    private const GOOGLE_ANALYTICS = <<<'HTML'
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-ABC123XYZ"></script>
        <script>gtag('config', 'G-ABC123XYZ');</script>
        HTML;

    private const META_PIXEL = <<<'HTML'
        <script>!function(f,b,e,v,n,t,s){}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '123');</script>
        HTML;

    private function catalogue(): CookieCatalogue
    {
        return $this->app->make(CookieCatalogue::class);
    }

    public function test_it_detects_known_services_in_a_snippet(): void
    {
        $this->assertSame(['google_analytics'], array_keys($this->catalogue()->detect(self::GOOGLE_ANALYTICS)));
        $this->assertSame(['meta_pixel'], array_keys($this->catalogue()->detect(self::META_PIXEL)));
        $this->assertSame(['microsoft_clarity'], array_keys($this->catalogue()->detect('<script src="https://www.clarity.ms/tag/abc"></script>')));
    }

    public function test_it_detects_nothing_in_empty_or_unknown_snippets(): void
    {
        $this->assertSame([], $this->catalogue()->detect(null));
        $this->assertSame([], $this->catalogue()->detect('   '));
        $this->assertSame([], $this->catalogue()->detect('<script>console.log("hello")</script>'));
    }

    public function test_the_required_category_lists_laravels_own_cookies(): void
    {
        config(['session.cookie' => 'example_session', 'session.lifetime' => 120, 'session.expire_on_close' => false]);

        $cookies = collect($this->catalogue()->cookiesFor('functional'))->keyBy('name');

        $this->assertSame(['example_session', 'XSRF-TOKEN', 'remember_*', 'cookie_preferences'], $cookies->keys()->all());
        $this->assertSame('2 hours', $cookies['example_session']['duration']);
        $this->assertSame('Example Site', $cookies['example_session']['provider']);
        $this->assertSame('1 year', $cookies['cookie_preferences']['duration']);
    }

    public function test_detected_cookies_are_listed_under_the_category_their_script_sits_in(): void
    {
        $scripts = ['analytics' => self::GOOGLE_ANALYTICS, 'marketing' => self::META_PIXEL];

        $this->assertSame(['_ga', '_ga_*'], array_column($this->catalogue()->cookiesFor('analytics', $scripts), 'name'));
        $this->assertSame(['_fbp', '_fbc'], array_column($this->catalogue()->cookiesFor('marketing', $scripts), 'name'));
        $this->assertSame(['_ga', '_ga_*'], array_column($this->catalogue()->cookiesFor('marketing', ['marketing' => self::GOOGLE_ANALYTICS]), 'name'));
    }

    public function test_config_cookies_are_added_and_duplicates_are_listed_once(): void
    {
        config(['cookies.cookies.marketing' => [
            ['name' => 'my_ad_cookie', 'provider' => 'Example Ads', 'purpose' => 'Measures ad clicks.', 'duration' => '30 days'],
        ]]);

        $scripts = ['marketing' => '<script src="https://www.clarity.ms/tag/x"></script><script src="https://bat.bing.com/bat.js"></script>'];
        $names = array_column($this->catalogue()->cookiesFor('marketing', $scripts), 'name');

        $this->assertSame(1, count(array_keys($names, 'MUID')));
        $this->assertContains('my_ad_cookie', $names);
        $this->assertContains('_uetsid', $names);
    }

    public function test_config_services_extend_the_catalogue(): void
    {
        config(['cookies.services.example' => [
            'name' => 'Example Tracker',
            'signatures' => ['~tracker\.example\.com~'],
            'cookies' => [['name' => '_ex', 'provider' => 'Example', 'purpose' => 'Counts visits.', 'duration' => '1 year']],
        ]]);

        $this->assertSame(['_ex'], array_column($this->catalogue()->cookiesFor('analytics', ['analytics' => '<script src="https://tracker.example.com/t.js"></script>']), 'name'));
    }
}
