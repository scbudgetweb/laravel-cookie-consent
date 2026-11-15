<?php

namespace ScBudgetWeb\CookieConsent\Tests;

use ScBudgetWeb\CookieConsent\CookieConsent;

class CookieConsentTest extends TestCase
{
    private function consent(): CookieConsent
    {
        return $this->app->make(CookieConsent::class);
    }

    public function test_everything_is_allowed_when_the_banner_is_disabled(): void
    {
        config(['cookies.enabled' => false]);
        $this->withConsentCookie(null);

        $this->assertTrue($this->consent()->allows('analytics'));
        $this->assertTrue($this->consent()->allows('marketing'));
    }

    public function test_optional_categories_are_refused_until_the_visitor_chooses(): void
    {
        $this->withConsentCookie(null);

        $this->assertFalse($this->consent()->hasDecided());
        $this->assertFalse($this->consent()->allows('analytics'));
        $this->assertFalse($this->consent()->allows('marketing'));
    }

    public function test_default_allowed_categories_are_on_until_the_visitor_chooses(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(null);

        $this->assertFalse($this->consent()->hasDecided());
        $this->assertTrue($this->consent()->allows('analytics'));
        $this->assertFalse($this->consent()->allows('marketing'));
    }

    public function test_the_visitors_choice_beats_a_default(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(['analytics' => false, 'marketing' => false]);

        $this->assertFalse($this->consent()->allows('analytics'));
    }

    public function test_default_allowed_ignores_unknown_and_required_categories(): void
    {
        config(['cookies.default_allowed' => ['analytics', 'functional', 'social']]);

        $this->assertSame(['analytics'], $this->consent()->defaultAllowed());
    }

    public function test_google_consent_state_follows_what_the_visitor_allows(): void
    {
        config(['cookies.default_allowed' => ['analytics']]);
        $this->withConsentCookie(null);

        $this->assertSame([
            'functionality_storage' => 'granted',
            'security_storage' => 'granted',
            'analytics_storage' => 'granted',
            'ad_storage' => 'denied',
            'ad_user_data' => 'denied',
            'ad_personalization' => 'denied',
        ], $this->consent()->googleConsentState());

        $this->withConsentCookie(['analytics' => false, 'marketing' => true]);

        $state = $this->consent()->googleConsentState();
        $this->assertSame('denied', $state['analytics_storage']);
        $this->assertSame('granted', $state['ad_storage']);
        $this->assertSame('granted', $state['ad_personalization']);
    }

    public function test_required_categories_are_always_allowed(): void
    {
        $this->withConsentCookie(['analytics' => false, 'marketing' => false]);

        $this->assertTrue($this->consent()->allows('functional'));
    }

    public function test_it_follows_the_visitors_choices(): void
    {
        $this->withConsentCookie(['analytics' => true, 'marketing' => false]);

        $this->assertTrue($this->consent()->hasDecided());
        $this->assertTrue($this->consent()->allows('analytics'));
        $this->assertFalse($this->consent()->allows('marketing'));
        $this->assertSame(['analytics' => true, 'marketing' => false], $this->consent()->preferences());
    }

    public function test_an_unknown_category_is_refused(): void
    {
        $this->withConsentCookie(['analytics' => true, 'marketing' => true]);

        $this->assertFalse($this->consent()->allows('social'));
    }

    public function test_a_malformed_cookie_counts_as_not_decided(): void
    {
        $this->withConsentCookie('not-json');

        $this->assertFalse($this->consent()->hasDecided());
        $this->assertFalse($this->consent()->allows('analytics'));
    }

    public function test_only_true_counts_as_consent(): void
    {
        $this->withConsentCookie(['analytics' => 'yes', 'marketing' => 1]);

        $this->assertFalse($this->consent()->allows('analytics'));
        $this->assertFalse($this->consent()->allows('marketing'));
    }

    public function test_choices_saved_under_an_older_revision_are_asked_again(): void
    {
        config(['cookies.revision' => 2]);
        $this->withConsentCookie(['analytics' => true, 'marketing' => true]);

        $this->assertFalse($this->consent()->hasDecided());
        $this->assertFalse($this->consent()->allows('analytics'));
    }

    public function test_custom_categories_fall_back_to_a_headline_label(): void
    {
        config(['cookies.categories.social_media' => ['required' => false]]);

        $this->assertSame(['analytics', 'marketing', 'social_media'], $this->consent()->optionalCategories());
        $this->assertSame('Social Media', $this->consent()->label('social_media'));
        $this->assertSame('Analytics', $this->consent()->label('analytics'));
    }

    public function test_the_blade_directive_follows_the_choices(): void
    {
        $this->withConsentCookie(['analytics' => true, 'marketing' => false]);

        $html = $this->blade("@cookieConsent('analytics') analytics-on @endcookieConsent @cookieConsent('marketing') marketing-on @endcookieConsent");

        $html->assertSee('analytics-on')->assertDontSee('marketing-on');
    }
}
