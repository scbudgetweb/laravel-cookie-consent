<?php

return [

    /*
     * Show the cookie consent banner. When disabled, every category counts as
     * allowed, so scripts behave as if there were no consent banner at all.
     */
    'enabled' => env('COOKIE_CONSENT_ENABLED', true),

    /*
     * The cookie that stores the visitor's choices. It is written by the
     * banner's JavaScript, so the package excludes it from cookie encryption.
     */
    'cookie_name' => 'cookie_preferences',

    /*
     * How long (in days) the visitor's choices are remembered before they are
     * asked again. The UK ICO suggests asking again periodically.
     */
    'lifetime' => 365,

    /*
     * Increase this number to ask every visitor again, for example after
     * adding a category or a new marketing service.
     */
    'revision' => 1,

    /*
     * Shown as a "Privacy Policy" link in the banner and the settings window.
     * A full URL or a path. Leave null to hide the link.
     */
    'privacy_policy_url' => null,

    /*
     * The settings window shows a short description of each category. Set this
     * to true to also show an expandable table of every cookie in it (name,
     * provider, purpose and expiry), built from the automatic discovery.
     */
    'show_cookie_list' => false,

    /*
     * The cookie categories, in the order they are shown. Required categories
     * are always on and can't be switched off. Labels and descriptions come
     * from the translation file (cookies::messages.categories.{key}); unknown
     * keys fall back to a headline version of the key.
     */
    'categories' => [
        'functional' => ['required' => true],
        'analytics' => ['required' => false],
        'marketing' => ['required' => false],
    ],

    /*
     * Optional categories that are on until the visitor objects, instead of off
     * until they agree. For example ['analytics'] under the UK's statistical
     * purposes exception (Data (Use and Access) Act 2025): analytics used only
     * to improve the site needs no consent, as long as visitors are told and can
     * switch it off. The banner says so, and Reject still turns it off. Leave
     * empty for opt-in only (required in the EU, and for any advertising).
     */
    'default_allowed' => [],

    /*
     * Google Consent Mode v2. When true, <x-cookie-consent-google /> (place it in
     * <head>, before any Google tag) tells Google tags what the visitor allows,
     * and the banner updates it when they choose. google_consent_types maps each
     * category to Google's consent types; types not listed are never sent.
     */
    'google_consent_mode' => false,

    'google_consent_types' => [
        'functional' => ['functionality_storage', 'security_storage'],
        'analytics' => ['analytics_storage'],
        'marketing' => ['ad_storage', 'ad_user_data', 'ad_personalization'],
    ],

    /*
     * Tracking snippets per category. <x-cookie-consent-scripts /> outputs each
     * one only when its category is allowed, and the banner scans them to list
     * the cookies they set (Google Analytics, Meta Pixel, LinkedIn and more).
     */
    'scripts' => [
        'analytics' => null,
        'marketing' => null,
    ],

    /*
     * Extra cookies to list, per category, for anything the automatic
     * discovery can't see (your own cookies, or services it doesn't know).
     * Use * at the end of a name for cookies with a variable suffix.
     *
     * 'marketing' => [
     *     ['name' => 'my_ad_cookie', 'provider' => 'Example Ads', 'purpose' => 'Measures ad clicks.', 'duration' => '30 days'],
     * ],
     */
    'cookies' => [],

    /*
     * Extra services for automatic discovery, in the same shape as the built-in
     * catalogue (see src/CookieCatalogue.php). An entry with the same key as a
     * built-in service replaces it.
     */
    'services' => [],

];
