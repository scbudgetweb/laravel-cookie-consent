# Laravel Cookie Consent

A simple cookie consent banner for Laravel that follows UK GDPR and PECR guidance:

- A banner with **Accept**, **Reject** and **Customise**, and a link to your privacy policy.
- **Customise** opens a settings window: **Functional** (always on), **Analytics** and **Marketing** switches, each with a short plain-English description, and **Save preferences**.
- Nothing optional loads until the visitor accepts it. Analytics and Marketing start switched off.
- **Automatic cookie discovery:** paste in your tracking snippets and the package recognises the services in them (Google Analytics, Google Ads, Meta Pixel, LinkedIn, Microsoft Clarity, Hotjar, TikTok and more) and the cookies they set, so it can delete them when a visitor refuses. You can also turn on an optional full cookie table.
- Refusing a category later deletes its cookies.
- A "Cookie settings" link anywhere on the page reopens the settings window.
- No front-end dependencies: no Tailwind, Alpine or build step. Plain JavaScript and a small stylesheet you can theme with CSS variables.

Requires PHP 8.2+ and Laravel 11, 12 or 13.

## Installation

```bash
composer require scbudgetweb/laravel-cookie-consent
```

The service provider is registered automatically.

Add the components to your layout, just before `</body>`:

```blade
<x-cookie-consent-scripts />
<x-cookie-consent privacy-policy-url="/privacy-policy" />
```

- `<x-cookie-consent-scripts />` outputs your tracking scripts, each one only when its category is allowed.
- `<x-cookie-consent />` renders the banner (until the visitor chooses) and the settings window.

## Adding your tracking scripts

Publish the config file:

```bash
php artisan vendor:publish --tag=cookie-consent-config
```

Then put each snippet under the category it belongs to in `config/cookies.php`:

```php
'scripts' => [
    'analytics' => <<<'HTML'
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-XXXXXXX');
        </script>
        HTML,
    'marketing' => null,
],
```

That's all. The snippet only loads once the visitor allows Analytics, and the `_ga` cookies are deleted if they later switch Analytics off.

If your scripts live in a database (for example, an admin settings screen), pass them to both components instead:

```blade
<x-cookie-consent-scripts :scripts="$trackingScripts" />
<x-cookie-consent :scripts="$trackingScripts" :privacy-policy-url="route('privacy-policy')" />
```

where `$trackingScripts` is an array like `['analytics' => '...', 'marketing' => '...']`.

## Checking consent in your own code

In Blade:

```blade
@cookieConsent('marketing')
    <script src="https://example.com/ads.js"></script>
@endcookieConsent
```

In PHP:

```php
use ScBudgetWeb\CookieConsent\Facades\CookieConsent;

if (CookieConsent::allows('analytics')) {
    // ...
}
```

In JavaScript:

```js
window.CookieConsent.allows('analytics'); // true or false
window.CookieConsent.open();              // opens the settings window

document.addEventListener('cookie-consent:updated', (event) => {
    console.log(event.detail); // { revision: 1, analytics: true, marketing: false }
});
```

When the banner is disabled (`COOKIE_CONSENT_ENABLED=false`), every category counts as allowed.

## A "Cookie settings" link

Visitors must be able to change their minds. Add a link anywhere, usually the footer:

```blade
<a href="#cookie-settings">Cookie settings</a>
```

Any element with a `data-cookie-consent-open` attribute works too.

## Automatic cookie discovery

The package recognises known services in each category's scripts and knows the cookies they set. It uses this to:

- delete the right cookies when a visitor refuses a category
- optionally show a full table of every cookie (name, provider, purpose and expiry) under each category. Set `'show_cookie_list' => true`. The table also lists Laravel's own cookies under Functional: the session cookie, `XSRF-TOKEN`, `remember_*` and the consent cookie.

Built-in services: Google Analytics, Google Tag Manager, Google Ads, Meta Pixel, LinkedIn Insight Tag, Microsoft Clarity, Microsoft Advertising (UET), Hotjar, TikTok Pixel, X (Twitter) Pixel, Pinterest Tag and Matomo.

Discovery works by recognising each service's snippet. It can't see:

- cookies set by services it doesn't recognise
- cookies that other services load later (for example, tags added inside Google Tag Manager)

Declare those yourself in `config/cookies.php`, so they're deleted on refusal and appear in the full table:

```php
'cookies' => [
    'marketing' => [
        ['name' => 'my_ad_cookie', 'provider' => 'Example Ads', 'purpose' => 'Measures ad clicks.', 'duration' => '30 days'],
    ],
],
```

Or teach it a new service:

```php
'services' => [
    'example_tracker' => [
        'name' => 'Example Tracker',
        'signatures' => ['~tracker\.example\.com~i'], // regular expressions
        'cookies' => [
            ['name' => '_ex_*', 'provider' => 'Example', 'purpose' => 'Counts visits.', 'duration' => '1 year'],
        ],
    ],
],
```

A name ending in `*` matches any suffix. Pull requests adding more services are welcome.

## Configuration

| Option | Default | What it does |
|---|---|---|
| `enabled` | `env('COOKIE_CONSENT_ENABLED', true)` | Show the banner. When off, every category is allowed. |
| `cookie_name` | `cookie_preferences` | The cookie that stores the visitor's choices. |
| `lifetime` | `365` | Days before visitors are asked again. |
| `revision` | `1` | Increase it to ask every visitor again (for example after adding a new marketing service). |
| `privacy_policy_url` | `null` | The privacy policy link. Hidden when empty. |
| `show_cookie_list` | `false` | Also show an expandable table of every cookie under each category. |
| `categories` | functional, analytics, marketing | Categories in display order. `required` ones are always on. |
| `scripts` | `[]` | Tracking snippets per category. |
| `cookies` | `[]` | Extra cookies to list per category. |
| `services` | `[]` | Extra services for automatic discovery. |

You can add your own categories (for example `'social' => ['required' => false]`). Give them a label and description in the translation file.

## Styling

The banner is a white panel with blue buttons. Change it with CSS variables:

```css
#cookie-consent {
    --cc-accent: #0f766e;        /* buttons, switches and links */
    --cc-accent-text: #ffffff;   /* text on the main button */
    --cc-bg: #ffffff;
    --cc-text: #1f2937;
    --cc-muted: #4b5563;
    --cc-border: #e5e7eb;
    --cc-secondary-bg: #f3f4f6;  /* Reject and Customise buttons */
    --cc-radius: 0.75rem;
    --cc-font: inherit;
}
```

For bigger changes, publish the view:

```bash
php artisan vendor:publish --tag=cookie-consent-views
```

## Translations

The wording is in plain UK English. To change it or add a language:

```bash
php artisan vendor:publish --tag=cookie-consent-translations
```

then edit `lang/vendor/cookies/en/messages.php` (or add a folder such as `lang/vendor/cookies/fr`).

## How it works

- The banner's JavaScript saves the visitor's choices in a first-party cookie, for example `{"revision":1,"analytics":true,"marketing":false}`. The package excludes this cookie from Laravel's cookie encryption so the server can read it.
- Scripts are filtered on the server, so nothing optional is even sent to the browser until it's allowed. After a choice changes, the page reloads once to apply it.
- When a category is refused, its listed cookies are deleted from your domain and its parent domains. Cookies set on other companies' domains (such as `doubleclick.net`) can't be deleted by your site. They stop being refreshed once the scripts stop loading.

## Testing

```bash
composer install
composer test
```

## Legal note

This package helps you collect consent properly. It isn't legal advice. You're still responsible for your privacy policy and for listing every cookie your site uses.

## Licence

MIT. See [LICENSE](LICENSE).
