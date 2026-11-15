<?php

namespace ScBudgetWeb\CookieConsent;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Reads the visitor's cookie choices from the consent cookie written by the banner.
 *
 * Stateless on purpose: the cookie is read from the current request on every call, so
 * it is safe as a singleton under Octane and across requests in tests.
 */
class CookieConsent
{
    public function enabled(): bool
    {
        return (bool) config('cookies.enabled');
    }

    /**
     * Whether the visitor allows a category. Always true when the banner is disabled
     * and for required categories. Otherwise the visitor's own choice, or, before they
     * have chosen, whether the category is on by default (cookies.default_allowed).
     */
    public function allows(string $category): bool
    {
        if (! $this->enabled() || $this->isRequired($category)) {
            return true;
        }

        $preferences = $this->preferences();

        if ($preferences === null) {
            return in_array($category, $this->defaultAllowed(), true);
        }

        return ($preferences[$category] ?? false) === true;
    }

    /**
     * Optional categories that count as allowed until the visitor objects, for example
     * Analytics under the UK's statistical purposes exception. Unknown and required
     * categories are ignored.
     *
     * @return list<string>
     */
    public function defaultAllowed(): array
    {
        return array_values(array_intersect($this->optionalCategories(), (array) config('cookies.default_allowed', [])));
    }

    /**
     * Whether to tell Google tags what the visitor allows (Google Consent Mode v2).
     */
    public function usesGoogleConsentMode(): bool
    {
        return (bool) config('cookies.google_consent_mode', false);
    }

    /**
     * Google consent types per category (cookies.google_consent_types), e.g. analytics => [analytics_storage].
     *
     * @return array<string, list<string>>
     */
    public function googleConsentTypes(): array
    {
        $types = [];

        foreach ((array) config('cookies.google_consent_types', []) as $category => $categoryTypes) {
            $types[(string) $category] = array_values(array_map('strval', (array) $categoryTypes));
        }

        return $types;
    }

    /**
     * Each Google consent type as "granted" or "denied", from what the visitor allows right now.
     *
     * @return array<string, 'granted'|'denied'>
     */
    public function googleConsentState(): array
    {
        $state = [];

        foreach ($this->googleConsentTypes() as $category => $types) {
            foreach ($types as $type) {
                $state[$type] = $this->allows($category) ? 'granted' : 'denied';
            }
        }

        return $state;
    }

    public function hasDecided(): bool
    {
        return $this->preferences() !== null;
    }

    /**
     * The visitor's choice for each optional category, or null if they haven't chosen yet
     * (no cookie, an unreadable one, or one saved under an older revision).
     *
     * @return array<string, bool>|null
     */
    public function preferences(): ?array
    {
        $value = request()->cookie((string) config('cookies.cookie_name'));

        if (! is_string($value) || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded) || ($decoded['revision'] ?? null) !== (int) config('cookies.revision', 1)) {
            return null;
        }

        $preferences = [];

        foreach ($this->optionalCategories() as $category) {
            $preferences[$category] = ($decoded[$category] ?? false) === true;
        }

        return $preferences;
    }

    /**
     * @return array<string, array{required: bool}>
     */
    public function categories(): array
    {
        $categories = [];

        foreach ((array) config('cookies.categories', []) as $key => $settings) {
            $categories[(string) $key] = ['required' => (bool) ($settings['required'] ?? false)];
        }

        return $categories;
    }

    /**
     * @return list<string>
     */
    public function optionalCategories(): array
    {
        return array_keys(array_filter($this->categories(), fn (array $category): bool => ! $category['required']));
    }

    public function isRequired(string $category): bool
    {
        return $this->categories()[$category]['required'] ?? false;
    }

    public function label(string $category): string
    {
        $key = "cookies::messages.categories.{$category}.label";

        return Lang::has($key) ? __($key) : Str::headline($category);
    }

    public function description(string $category): string
    {
        $key = "cookies::messages.categories.{$category}.description";

        return Lang::has($key) ? __($key) : '';
    }
}
