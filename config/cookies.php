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
