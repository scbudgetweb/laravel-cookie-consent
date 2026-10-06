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

    public function test_scripts_default_to_the_config(): void
    {
        config(['cookies.scripts' => self::SCRIPTS]);
        $this->withConsentCookie(['analytics' => false, 'marketing' => true]);

        $this->blade('<x-cookie-consent-scripts />')
            ->assertDontSee('googletagmanager.com', false)
            ->assertSee('fbq(', false);
    }
}
