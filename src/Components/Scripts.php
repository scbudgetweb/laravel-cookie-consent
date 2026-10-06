<?php

namespace ScBudgetWeb\CookieConsent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ScBudgetWeb\CookieConsent\CookieConsent;

/**
 * Outputs each category's tracking scripts, but only once the visitor allows that category.
 */
class Scripts extends Component
{
    /**
     * @param  array<string, string|null>|null  $scripts  Scripts per category. Defaults to config('cookies.scripts').
     */
    public function __construct(public ?array $scripts = null) {}

    public function render(): View
    {
        $consent = app(CookieConsent::class);
        $scripts = $this->scripts ?? (array) config('cookies.scripts', []);

        return view('cookies::scripts', [
            'allowedScripts' => array_filter(
                $scripts,
                fn (?string $html, string $category): bool => filled($html) && $consent->allows($category),
                ARRAY_FILTER_USE_BOTH,
            ),
        ]);
    }
}
