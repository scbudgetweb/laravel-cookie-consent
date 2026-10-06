<?php

namespace ScBudgetWeb\CookieConsent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ScBudgetWeb\CookieConsent\CookieCatalogue;
use ScBudgetWeb\CookieConsent\CookieConsent;

/**
 * The consent banner (shown until the visitor chooses) and the "Cookie settings" window
 * (always rendered, so a footer link can reopen it).
 */
class Banner extends Component
{
    /**
     * @param  array<string, string|null>|null  $scripts  Scripts per category, scanned to list their cookies. Defaults to config('cookies.scripts').
     * @param  string|null  $privacyPolicyUrl  Defaults to config('cookies.privacy_policy_url').
     */
    public function __construct(
        public ?array $scripts = null,
        public ?string $privacyPolicyUrl = null,
    ) {}

    public function shouldRender(): bool
    {
        return app(CookieConsent::class)->enabled();
    }

    public function render(): View
    {
        $consent = app(CookieConsent::class);
        $catalogue = app(CookieCatalogue::class);
        $scripts = $this->scripts ?? (array) config('cookies.scripts', []);
        $preferences = $consent->preferences();

        $categories = [];
        $erase = [];

        foreach ($consent->categories() as $key => $category) {
            $cookies = $catalogue->cookiesFor($key, $scripts);

            $categories[] = [
                'key' => $key,
                'label' => $consent->label($key),
                'description' => $consent->description($key),
                'required' => $category['required'],
                'cookies' => $cookies,
            ];

            if (! $category['required']) {
                $erase[$key] = array_column($cookies, 'name');
            }
        }

        return view('cookies::banner', [
            'categories' => $categories,
            'showBanner' => $preferences === null,
            'showCookieList' => (bool) config('cookies.show_cookie_list', false),
            'privacyPolicyUrl' => $this->privacyPolicyUrl ?? config('cookies.privacy_policy_url'),
            'config' => [
                'cookieName' => (string) config('cookies.cookie_name'),
                'lifetime' => (int) config('cookies.lifetime', 365),
                'revision' => (int) config('cookies.revision', 1),
                'optional' => $consent->optionalCategories(),
                'required' => array_values(array_diff(array_keys($consent->categories()), $consent->optionalCategories())),
                'preferences' => $preferences,
                'erase' => $erase,
            ],
        ]);
    }
}
