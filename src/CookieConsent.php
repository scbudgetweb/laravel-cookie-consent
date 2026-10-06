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
     * and for required categories; otherwise only after the visitor has said yes.
     */
    public function allows(string $category): bool
    {
        if (! $this->enabled() || $this->isRequired($category)) {
            return true;
        }

        return ($this->preferences()[$category] ?? false) === true;
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
