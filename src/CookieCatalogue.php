<?php

namespace ScBudgetWeb\CookieConsent;

use Carbon\CarbonInterval;

/**
 * Explains which cookies each category sets.
 *
 * Laravel's own cookies are always listed under the first required category. Known
 * third-party services are discovered by scanning each category's tracking scripts for
 * their signatures, and listed under the category the script sits in (that decides
 * when it loads). Anything else is declared in config('cookies.cookies').
 *
 * @phpstan-type Cookie array{name: string, provider: string, purpose: string, duration: string}
 * @phpstan-type Service array{name: string, signatures: list<string>, cookies: list<Cookie>}
 */
class CookieCatalogue
{
    /**
     * Built-in services. Signatures are regular expressions matched against the
     * script HTML. Names ending in * match any suffix.
     *
     * @var array<string, Service>
     */
    private const SERVICES = [
        'google_analytics' => [
            'name' => 'Google Analytics',
            'signatures' => ['~googletagmanager\.com/gtag/js\?id=G-~i', '~[\'"]G-[A-Z0-9]{6,}[\'"]~', '~google-analytics\.com~i'],
            'cookies' => [
                ['name' => '_ga', 'provider' => 'Google', 'purpose' => 'Tells visitors apart so we can count how many people use the site.', 'duration' => '2 years'],
                ['name' => '_ga_*', 'provider' => 'Google', 'purpose' => 'Keeps track of the current visit.', 'duration' => '2 years'],
            ],
        ],
        'google_tag_manager' => [
            'name' => 'Google Tag Manager',
            'signatures' => ['~googletagmanager\.com/gtm\.js~i', '~[\'"]GTM-[A-Z0-9]{4,}[\'"]~'],
            'cookies' => [],
        ],
        'google_ads' => [
            'name' => 'Google Ads',
            'signatures' => ['~[\'"]AW-\d+~', '~googleadservices\.com~i', '~googlesyndication\.com~i', '~doubleclick\.net~i'],
            'cookies' => [
                ['name' => '_gcl_au', 'provider' => 'Google', 'purpose' => 'Links ad clicks to actions on this site so Google can measure our adverts.', 'duration' => '90 days'],
                ['name' => 'IDE', 'provider' => 'Google (doubleclick.net)', 'purpose' => 'Used by Google to show and measure adverts.', 'duration' => '13 months'],
            ],
        ],
        'meta_pixel' => [
            'name' => 'Meta Pixel (Facebook and Instagram)',
            'signatures' => ['~connect\.facebook\.net~i', '~\bfbq\s*\(~'],
            'cookies' => [
                ['name' => '_fbp', 'provider' => 'Meta', 'purpose' => 'Identifies your browser so Meta can measure and target adverts.', 'duration' => '90 days'],
                ['name' => '_fbc', 'provider' => 'Meta', 'purpose' => 'Stores the last Facebook or Instagram ad you clicked.', 'duration' => '90 days'],
            ],
        ],
        'linkedin_insight' => [
            'name' => 'LinkedIn Insight Tag',
            'signatures' => ['~snap\.licdn\.com~i', '~_linkedin_partner_id~'],
            'cookies' => [
                ['name' => 'li_fat_id', 'provider' => 'LinkedIn', 'purpose' => 'Measures sign-ups and other actions that come from LinkedIn adverts.', 'duration' => '30 days'],
                ['name' => 'bcookie', 'provider' => 'LinkedIn (linkedin.com)', 'purpose' => 'Identifies your browser to LinkedIn.', 'duration' => '1 year'],
                ['name' => 'lidc', 'provider' => 'LinkedIn (linkedin.com)', 'purpose' => 'Routes requests to LinkedIn\'s servers.', 'duration' => '1 day'],
                ['name' => 'UserMatchHistory', 'provider' => 'LinkedIn (linkedin.com)', 'purpose' => 'Matches your browser with LinkedIn\'s advertising partners.', 'duration' => '30 days'],
            ],
        ],
        'microsoft_clarity' => [
            'name' => 'Microsoft Clarity',
            'signatures' => ['~clarity\.ms~i'],
            'cookies' => [
                ['name' => '_clck', 'provider' => 'Microsoft', 'purpose' => 'Recognises returning visitors in Clarity\'s usage reports.', 'duration' => '1 year'],
                ['name' => '_clsk', 'provider' => 'Microsoft', 'purpose' => 'Joins the pages of one visit into a single recording.', 'duration' => '1 day'],
                ['name' => 'CLID', 'provider' => 'Microsoft (clarity.ms)', 'purpose' => 'Recognises returning visitors across sites that use Clarity.', 'duration' => '1 year'],
                ['name' => 'MUID', 'provider' => 'Microsoft (bing.com)', 'purpose' => 'Identifies your browser across Microsoft services.', 'duration' => '1 year'],
            ],
        ],
        'microsoft_advertising' => [
            'name' => 'Microsoft Advertising',
            'signatures' => ['~bat\.bing\.com~i'],
            'cookies' => [
                ['name' => '_uetsid', 'provider' => 'Microsoft', 'purpose' => 'Measures actions that come from Bing adverts during your visit.', 'duration' => '1 day'],
                ['name' => '_uetvid', 'provider' => 'Microsoft', 'purpose' => 'Recognises returning visitors who came from Bing adverts.', 'duration' => '13 months'],
                ['name' => 'MUID', 'provider' => 'Microsoft (bing.com)', 'purpose' => 'Identifies your browser across Microsoft services.', 'duration' => '1 year'],
            ],
        ],
        'hotjar' => [
            'name' => 'Hotjar',
            'signatures' => ['~hotjar\.com~i', '~\bhjid\b~'],
            'cookies' => [
                ['name' => '_hjSessionUser_*', 'provider' => 'Hotjar', 'purpose' => 'Recognises returning visitors in Hotjar\'s reports.', 'duration' => '1 year'],
                ['name' => '_hjSession_*', 'provider' => 'Hotjar', 'purpose' => 'Keeps track of the current visit.', 'duration' => '30 minutes'],
            ],
        ],
        'tiktok_pixel' => [
            'name' => 'TikTok Pixel',
            'signatures' => ['~analytics\.tiktok\.com~i', '~\bttq\.(load|page)\s*\(~'],
            'cookies' => [
                ['name' => '_ttp', 'provider' => 'TikTok', 'purpose' => 'Measures and improves TikTok adverts.', 'duration' => '13 months'],
                ['name' => '_tt_enable_cookie', 'provider' => 'TikTok', 'purpose' => 'Checks whether your browser accepts TikTok\'s cookies.', 'duration' => '13 months'],
            ],
        ],
        'x_pixel' => [
            'name' => 'X (Twitter) Pixel',
            'signatures' => ['~static\.ads-twitter\.com~i', '~\btwq\s*\(~'],
            'cookies' => [
                ['name' => 'personalization_id', 'provider' => 'X (x.com)', 'purpose' => 'Measures and personalises X adverts.', 'duration' => '13 months'],
                ['name' => 'muc_ads', 'provider' => 'X (x.com)', 'purpose' => 'Identifies your browser for X advertising.', 'duration' => '13 months'],
            ],
        ],
        'pinterest_tag' => [
            'name' => 'Pinterest Tag',
            'signatures' => ['~s\.pinimg\.com/ct/~i', '~\bpintrk\s*\(~'],
            'cookies' => [
                ['name' => '_pin_unauth', 'provider' => 'Pinterest', 'purpose' => 'Groups actions by visitors who aren\'t signed in to Pinterest.', 'duration' => '1 year'],
                ['name' => '_epik', 'provider' => 'Pinterest', 'purpose' => 'Stores the Pinterest ad you clicked.', 'duration' => '1 year'],
            ],
        ],
        'matomo' => [
            'name' => 'Matomo',
            'signatures' => ['~(matomo|piwik)\.(js|php)~i', '~\b_paq\b~'],
            'cookies' => [
                ['name' => '_pk_id.*', 'provider' => 'Matomo', 'purpose' => 'Tells visitors apart so we can count how many people use the site.', 'duration' => '13 months'],
                ['name' => '_pk_ses.*', 'provider' => 'Matomo', 'purpose' => 'Keeps track of the current visit.', 'duration' => '30 minutes'],
            ],
        ],
    ];

    /**
     * @return array<string, Service>
     */
    public function services(): array
    {
        return array_merge(self::SERVICES, (array) config('cookies.services', []));
    }

    /**
     * The known services found in a snippet of HTML or JavaScript.
     *
     * @return array<string, Service>
     */
    public function detect(?string $html): array
    {
        if ($html === null || trim($html) === '') {
            return [];
        }

        return array_filter($this->services(), function (array $service) use ($html): bool {
            foreach ($service['signatures'] ?? [] as $signature) {
                if (@preg_match($signature, $html) === 1) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Every cookie to list under a category: Laravel's own (first required category
     * only), cookies of services found in that category's scripts, then config extras.
     *
     * @param  array<string, string|null>  $scripts
     * @return list<Cookie>
     */
    public function cookiesFor(string $category, array $scripts = []): array
    {
        $cookies = $category === $this->firstRequiredCategory() ? $this->laravelCookies() : [];

        foreach ($this->detect($scripts[$category] ?? null) as $service) {
            array_push($cookies, ...($service['cookies'] ?? []));
        }

        array_push($cookies, ...array_values((array) config("cookies.cookies.{$category}", [])));

        $unique = [];

        foreach ($cookies as $cookie) {
            $unique[$cookie['name']] ??= [
                'name' => (string) $cookie['name'],
                'provider' => (string) ($cookie['provider'] ?? ''),
                'purpose' => (string) ($cookie['purpose'] ?? ''),
                'duration' => (string) ($cookie['duration'] ?? ''),
            ];
        }

        return array_values($unique);
    }

    /**
     * @return list<Cookie>
     */
    private function laravelCookies(): array
    {
        $site = (string) config('app.name', 'This site');
        $sessionLength = config('session.expire_on_close')
            ? 'When you close your browser'
            : $this->humanise((int) config('session.lifetime', 120));

        return [
            ['name' => (string) config('session.cookie', 'laravel_session'), 'provider' => $site, 'purpose' => 'Keeps your visit working as you move between pages, including keeping you signed in.', 'duration' => $sessionLength],
            ['name' => 'XSRF-TOKEN', 'provider' => $site, 'purpose' => 'Protects forms on this site from being submitted by other websites.', 'duration' => $sessionLength],
            ['name' => 'remember_*', 'provider' => $site, 'purpose' => 'Keeps you signed in if you choose "Remember me".', 'duration' => '400 days'],
            ['name' => (string) config('cookies.cookie_name'), 'provider' => $site, 'purpose' => 'Remembers your cookie choices.', 'duration' => $this->humanise((int) config('cookies.lifetime', 365) * 1440)],
        ];
    }

    private function firstRequiredCategory(): ?string
    {
        foreach ((array) config('cookies.categories', []) as $key => $settings) {
            if ($settings['required'] ?? false) {
                return (string) $key;
            }
        }

        return null;
    }

    private function humanise(int $minutes): string
    {
        return CarbonInterval::minutes(max(1, $minutes))->cascade()->forHumans(['parts' => 1]);
    }
}
