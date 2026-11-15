<?php

namespace ScBudgetWeb\CookieConsent\Tests;

class ComponentsTest extends TestCase
{
    private const SCRIPTS = [
        'analytics' => '<script src="https://www.googletagmanager.com/gtag/js?id=G-ABC123XYZ"></script>',
        'marketing' => '<script>fbq("init", "123");</script>',
    ];

    public function test_the_banner_shows_until_the_visitor_chooses(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent privacy-policy-url="/privacy-policy" />')
            ->assertSee('Cookies on Example Site')
            ->assertSee('data-cc-accept', false)
            ->assertSee('data-cc-reject', false)
            ->assertSee('data-cc-customise', false)
            ->assertSee('href="/privacy-policy"', false)
            ->assertSee('data-cc-dialog', false);
    }

    public function test_accept_and_reject_have_equal_visual_weight(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent />')
            ->assertSee('class="cc-btn cc-btn--primary" data-cc-accept', false)
            ->assertSee('class="cc-btn cc-btn--primary" data-cc-reject', false);
    }

    public function test_only_the_settings_window_renders_after_the_visitor_chooses(): void
    {
        $this->withConsentCookie(['analytics' => false, 'marketing' => false]);

        $this->blade('<x-cookie-consent />')
            ->assertDontSee('class="cc-banner"', false)
            ->assertSee('data-cc-dialog', false);
    }

    public function test_nothing_renders_when_disabled(): void
    {
        config(['cookies.enabled' => false]);
        $this->withConsentCookie(null);

        $this->assertSame('', trim((string) $this->blade('<x-cookie-consent />')));
    }

    public function test_the_settings_window_shows_descriptions_without_listing_cookies(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertSee('Help us understand how people use the site')
            ->assertSee('Always on')
            ->assertDontSee('Google Analytics')
            ->assertDontSee('<code>_ga_*</code>', false)
            ->assertDontSee('Show cookies');
    }

    public function test_required_categories_have_no_switch(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent />')
            ->assertDontSee('data-cc-category="functional"', false)
            ->assertSee('data-cc-category="analytics"', false)
            ->assertSee('data-cc-category="marketing"', false);
    }

    public function test_the_full_cookie_list_can_be_switched_on(): void
    {
        config(['cookies.show_cookie_list' => true]);
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertSee('Show cookies (2)')
            ->assertSee('<code>_ga_*</code>', false)
            ->assertSee('<code>_fbp</code>', false)
            ->assertSee('<code>cookie_preferences</code>', false);
    }

    public function test_the_privacy_link_is_hidden_without_a_url(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent />')->assertDontSee('Privacy Policy');
    }

    public function test_scripts_load_only_for_allowed_categories(): void
    {
        $this->withConsentCookie(['analytics' => true, 'marketing' => false]);

        $this->blade('<x-cookie-consent-scripts :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertSee('googletagmanager.com', false)
            ->assertDontSee('fbq(', false);
    }

    public function test_no_scripts_load_before_the_visitor_chooses(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent-scripts :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertDontSee('googletagmanager.com', false)
            ->assertDontSee('fbq(', false);
    }

    public function test_every_script_loads_when_disabled(): void
    {
        config(['cookies.enabled' => false]);
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent-scripts :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertSee('googletagmanager.com', false)
            ->assertSee('fbq(', false);
    }

    public function test_default_allowed_scripts_load_before_the_visitor_chooses(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent-scripts :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertSee('googletagmanager.com', false)
            ->assertDontSee('fbq(', false);
    }

    public function test_rejecting_stops_default_allowed_scripts(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(['analytics' => false, 'marketing' => false]);

        $this->blade('<x-cookie-consent-scripts :scripts="$scripts" />', ['scripts' => self::SCRIPTS])
            ->assertDontSee('googletagmanager.com', false);
    }

    public function test_the_banner_says_which_categories_are_on_by_default(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent />')
            ->assertSee('We use analytics cookies to understand how people use this site')
            ->assertSee('On unless you switch it off.')
            ->assertSee('&quot;defaults&quot;:[&quot;analytics&quot;]', false);
    }

    public function test_the_banner_keeps_the_opt_in_wording_without_defaults(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent />')
            ->assertSee('Accepting lets us process data')
            ->assertDontSee('On unless you switch it off.');
    }

    public function test_google_consent_defaults_render_only_when_switched_on(): void
    {
        $this->withConsentCookie(null);

        $this->blade('<x-cookie-consent-google />')->assertDontSee('gtag(', false);

        config(['cookies.google_consent_mode' => true, 'cookies.default_allowed' => ['analytics']]);

        $this->blade('<x-cookie-consent-google />')
            ->assertSee("gtag('consent', 'default'", false)
            ->assertSee('"analytics_storage":"granted"', false)
            ->assertSee('"ad_storage":"denied"', false)
            ->assertSee("gtag('set', 'ads_data_redaction', true)", false);
    }

    public function test_google_consent_defaults_grant_ads_after_marketing_consent(): void
    {
        config(['cookies.google_consent_mode' => true]);
        $this->withConsentCookie(['analytics' => true, 'marketing' => true]);

        $this->blade('<x-cookie-consent-google />')
            ->assertSee('"ad_storage":"granted"', false)
            ->assertDontSee('ads_data_redaction', false);
    }

    public function test_scripts_default_to_the_config(): void
    {
        config(['cookies.scripts' => self::SCRIPTS]);
        $this->withConsentCookie(['analytics' => false, 'marketing' => true]);

        $this->blade('<x-cookie-consent-scripts />')
            ->assertDontSee('googletagmanager.com', false)
            ->assertSee('fbq(', false);
    }
}
