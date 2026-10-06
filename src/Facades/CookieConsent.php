<?php

namespace ScBudgetWeb\CookieConsent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled()
 * @method static bool allows(string $category)
 * @method static bool hasDecided()
 * @method static array<string, bool>|null preferences()
 * @method static array<string, array{required: bool}> categories()
 * @method static list<string> optionalCategories()
 * @method static bool isRequired(string $category)
 *
 * @see \ScBudgetWeb\CookieConsent\CookieConsent
 */
class CookieConsent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ScBudgetWeb\CookieConsent\CookieConsent::class;
    }
}
