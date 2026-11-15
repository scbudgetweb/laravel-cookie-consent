<?php

namespace ScBudgetWeb\CookieConsent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ScBudgetWeb\CookieConsent\CookieConsent;

/**
 * Google Consent Mode v2 defaults: tells Google tags what the visitor allows before any tag loads.
 * Place it in <head>, before any Google tag. Renders nothing unless cookies.google_consent_mode is on.
 */
class GoogleConsent extends Component
{
    public function shouldRender(): bool
    {
        return app(CookieConsent::class)->usesGoogleConsentMode();
    }

    public function render(): View
    {
        $state = app(CookieConsent::class)->googleConsentState();

        return view('cookies::google-consent', [
            'state' => $state,
            'adsDenied' => ($state['ad_storage'] ?? 'denied') === 'denied',
        ]);
    }
}
