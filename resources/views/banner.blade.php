<div id="cookie-consent" class="cc" data-config="{{ json_encode($config) }}">
    <style>
        .cc { --cc-accent-fallback: #2563eb; font-family: var(--cc-font, inherit); }
        .cc *, .cc *::before, .cc *::after { box-sizing: border-box; }
        .cc-banner { position: fixed; left: 1rem; right: 1rem; bottom: 1rem; z-index: 2147483000; max-width: 72rem; margin: 0 auto; padding: 1.25rem 1.5rem; background: var(--cc-bg, #fff); color: var(--cc-text, #1f2937); border: 1px solid var(--cc-border, #e5e7eb); border-radius: var(--cc-radius, .75rem); box-shadow: 0 10px 40px rgba(0, 0, 0, .18); font-size: .9375rem; line-height: 1.5; }
        .cc-banner[hidden] { display: none; }
        .cc-banner__inner { display: flex; flex-direction: column; gap: 1rem; }
        .cc-banner__title { margin: 0 0 .25rem; font-size: 1rem; font-weight: 700; }
        .cc-banner__text { margin: 0; }
        .cc-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
        .cc-actions .cc-btn { flex: 1 1 8rem; }
        @media (min-width: 900px) {
            .cc-banner__inner { flex-direction: row; align-items: center; gap: 2rem; }
            .cc-banner__content { flex: 1; }
            .cc-banner .cc-actions { flex-wrap: nowrap; }
        }
        .cc-link { color: var(--cc-link, var(--cc-accent, var(--cc-accent-fallback))); text-decoration: underline; text-underline-offset: 2px; }
        .cc-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 2.75rem; padding: .625rem 1.25rem; border: 1px solid transparent; border-radius: var(--cc-button-radius, .5rem); font: inherit; font-weight: 600; line-height: 1.2; cursor: pointer; transition: filter .15s ease, background-color .15s ease; }
        .cc-btn:focus-visible, .cc-link:focus-visible, .cc-switch:focus-visible, .cc-close:focus-visible, .cc-category summary:focus-visible { outline: 3px solid var(--cc-focus, #fbbf24); outline-offset: 2px; }
        .cc-btn--primary { background: var(--cc-accent, var(--cc-accent-fallback)); color: var(--cc-accent-text, #fff); }
        .cc-btn--primary:hover { filter: brightness(.92); }
        .cc-btn--secondary { background: var(--cc-secondary-bg, #f3f4f6); color: var(--cc-text, #1f2937); border-color: var(--cc-border, #e5e7eb); }
        .cc-btn--secondary:hover { background: var(--cc-secondary-hover-bg, #e5e7eb); }
        .cc-modal { position: fixed; inset: 0; margin: auto; height: fit-content; width: calc(100% - 2rem); max-width: 42rem; max-height: calc(100% - 2rem); padding: 0; background: var(--cc-bg, #fff); color: var(--cc-text, #1f2937); border: 0; border-radius: var(--cc-radius, .75rem); box-shadow: 0 20px 60px rgba(0, 0, 0, .3); font-size: .9375rem; line-height: 1.5; }
        .cc-modal::backdrop { background: rgba(17, 24, 39, .55); }
        .cc-modal__header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--cc-border, #e5e7eb); }
        .cc-modal__title { margin: 0; font-size: 1.125rem; font-weight: 700; }
        .cc-modal__body { padding: 1.25rem 1.5rem; }
        .cc-modal__intro { margin: 0 0 1rem; }
        .cc-modal__footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.5rem; border-top: 1px solid var(--cc-border, #e5e7eb); }
        .cc-close { display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; padding: 0; border: 0; border-radius: 9999px; background: transparent; color: inherit; font-size: 1.5rem; line-height: 1; cursor: pointer; }
        .cc-close:hover { background: var(--cc-secondary-bg, #f3f4f6); }
        .cc-category { padding: 1rem 0; border-top: 1px solid var(--cc-border, #e5e7eb); }
        .cc-category:first-of-type { border-top: 0; padding-top: 0; }
        .cc-category__header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .cc-category__label { font-weight: 700; }
        label.cc-category__label { cursor: pointer; }
        .cc-category__description { margin: .25rem 0 0; color: var(--cc-muted, #4b5563); }
        .cc-category details { margin-top: .5rem; }
        .cc-category summary { cursor: pointer; color: var(--cc-link, var(--cc-accent, var(--cc-accent-fallback))); font-size: .875rem; font-weight: 600; }
        .cc-badge { font-size: .875rem; font-weight: 600; color: var(--cc-always-on, #15803d); white-space: nowrap; }
        .cc-switch { appearance: none; -webkit-appearance: none; position: relative; flex-shrink: 0; width: 2.75rem; height: 1.5rem; margin: 0; border-radius: 9999px; background: var(--cc-switch-off, #d1d5db); cursor: pointer; transition: background-color .15s ease; }
        .cc-switch::after { content: ""; position: absolute; top: .1875rem; left: .1875rem; width: 1.125rem; height: 1.125rem; border-radius: 9999px; background: #fff; box-shadow: 0 1px 2px rgba(0, 0, 0, .25); transition: transform .15s ease; }
        .cc-switch:checked { background: var(--cc-accent, var(--cc-accent-fallback)); }
        .cc-switch:checked::after { transform: translateX(1.25rem); }
                .cc-table-wrap { overflow-x: auto; margin-top: .5rem; }
        .cc-table { width: 100%; border-collapse: collapse; font-size: .8125rem; }
        .cc-table th, .cc-table td { padding: .5rem; border-bottom: 1px solid var(--cc-border, #e5e7eb); text-align: left; vertical-align: top; }
        .cc-table th { font-weight: 600; white-space: nowrap; }
        .cc-table code { font-size: .75rem; word-break: break-all; }
        .cc-empty { margin: .5rem 0 0; font-size: .8125rem; color: var(--cc-muted, #4b5563); }
        @media (prefers-reduced-motion: reduce) { .cc-btn, .cc-switch, .cc-switch::after { transition: none; } }
    </style>

    @if ($showBanner)
        <section class="cc-banner" data-cc-banner role="region" aria-labelledby="cc-banner-title">
            <div class="cc-banner__inner">
                <div class="cc-banner__content">
                    <h2 class="cc-banner__title" id="cc-banner-title">{{ __('cookies::messages.banner_title', ['site' => config('app.name')]) }}</h2>
                    <p class="cc-banner__text">
                        {{ __('cookies::messages.banner_text') }}
                        @if ($privacyPolicyUrl)
                            <a href="{{ $privacyPolicyUrl }}" class="cc-link">{{ __('cookies::messages.privacy_policy') }}</a>
                        @endif
                    </p>
                </div>
                <div class="cc-actions">
                    <button type="button" class="cc-btn cc-btn--primary" data-cc-accept>{{ __('cookies::messages.accept') }}</button>
                    <button type="button" class="cc-btn cc-btn--primary" data-cc-reject>{{ __('cookies::messages.reject') }}</button>
                    <button type="button" class="cc-btn cc-btn--secondary" data-cc-customise>{{ __('cookies::messages.customise') }}</button>
                </div>
            </div>
        </section>
    @endif

    <dialog class="cc-modal" data-cc-dialog aria-labelledby="cc-modal-title">
        <div class="cc-modal__header">
            <h2 class="cc-modal__title" id="cc-modal-title">{{ __('cookies::messages.settings_title') }}</h2>
            <button type="button" class="cc-close" data-cc-close aria-label="{{ __('cookies::messages.close') }}">&times;</button>
        </div>

        <div class="cc-modal__body">
            <p class="cc-modal__intro">{{ __('cookies::messages.settings_text') }}</p>

            @foreach ($categories as $category)
                <div class="cc-category">
                    <div class="cc-category__header">
                        @if ($category['required'])
                            <span class="cc-category__label">{{ $category['label'] }}</span>
                            <span class="cc-badge">{{ __('cookies::messages.always_on') }}</span>
                        @else
                            <label class="cc-category__label" for="cc-category-{{ $category['key'] }}">{{ $category['label'] }}</label>
                            <input type="checkbox" role="switch" class="cc-switch" id="cc-category-{{ $category['key'] }}" data-cc-category="{{ $category['key'] }}">
                        @endif
                    </div>

                    @if ($category['description'] !== '')
                        <p class="cc-category__description">{{ $category['description'] }}</p>
                    @endif

                    @if ($showCookieList)
                        <details>
                            <summary>{{ __('cookies::messages.show_cookies', ['count' => count($category['cookies'])]) }}</summary>
                            @if ($category['cookies'] === [])
                                <p class="cc-empty">{{ __('cookies::messages.no_cookies') }}</p>
                            @else
                                <div class="cc-table-wrap">
                                    <table class="cc-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('cookies::messages.column_name') }}</th>
                                                <th scope="col">{{ __('cookies::messages.column_provider') }}</th>
                                                <th scope="col">{{ __('cookies::messages.column_purpose') }}</th>
                                                <th scope="col">{{ __('cookies::messages.column_duration') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($category['cookies'] as $cookie)
                                                <tr>
                                                    <td><code>{{ $cookie['name'] }}</code></td>
                                                    <td>{{ $cookie['provider'] }}</td>
                                                    <td>{{ __($cookie['purpose']) }}</td>
                                                    <td>{{ __($cookie['duration']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </details>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="cc-modal__footer">
            @if ($privacyPolicyUrl)
                <a href="{{ $privacyPolicyUrl }}" class="cc-link">{{ __('cookies::messages.privacy_policy') }}</a>
            @else
                <span></span>
            @endif
            <button type="button" class="cc-btn cc-btn--primary" data-cc-save>{{ __('cookies::messages.save') }}</button>
        </div>
    </dialog>

    <script>
        (function () {
            var root = document.getElementById('cookie-consent');
            if (!root || root.dataset.ccReady) { return; }
            root.dataset.ccReady = '1';

            var config = JSON.parse(root.getAttribute('data-config'));
            var banner = root.querySelector('[data-cc-banner]');
            var dialog = root.querySelector('[data-cc-dialog]');
            var expired = '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';

            function allowedList(preferences) {
                return config.optional.filter(function (category) { return !!(preferences && preferences[category]); }).join(',');
            }

            function cookieNames() {
                return document.cookie.split(';').map(function (part) { return part.split('=')[0].trim(); }).filter(Boolean);
            }

            // Deletes a cookie on this host and every parent domain, where trackers usually set them.
            function eraseCookie(name) {
                var parts = location.hostname.split('.');
                document.cookie = name + expired;
                for (var i = 0; i < parts.length - 1; i++) {
                    var domain = parts.slice(i).join('.');
                    document.cookie = name + expired + '; domain=' + domain;
                    document.cookie = name + expired + '; domain=.' + domain;
                }
            }

            function eraseCategory(category) {
                var existing = cookieNames();
                (config.erase[category] || []).forEach(function (pattern) {
                    var isPrefix = pattern.slice(-1) === '*';
                    var prefix = pattern.slice(0, -1);
                    existing.forEach(function (name) {
                        if (isPrefix ? name.indexOf(prefix) === 0 : name === pattern) { eraseCookie(name); }
                    });
                });
            }

            function save(choices) {
                var value = { revision: config.revision };
                config.optional.forEach(function (category) { value[category] = !!choices[category]; });

                var expires = new Date(Date.now() + config.lifetime * 864e5).toUTCString();
                document.cookie = config.cookieName + '=' + encodeURIComponent(JSON.stringify(value))
                    + '; expires=' + expires + '; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');

                config.optional.forEach(function (category) { if (!value[category]) { eraseCategory(category); } });

                var changed = allowedList(config.preferences) !== allowedList(value);
                config.preferences = value;

                if (banner) { banner.hidden = true; }
                if (dialog.open) { dialog.close(); }

                document.dispatchEvent(new CustomEvent('cookie-consent:updated', { detail: value }));

                // Reload so scripts for newly allowed categories load (or refused ones stop loading).
                if (changed) { location.reload(); }
            }

            function setAll(allowed) {
                var choices = {};
                config.optional.forEach(function (category) { choices[category] = allowed; });
                save(choices);
            }

            function open() {
                config.optional.forEach(function (category) {
                    var input = root.querySelector('[data-cc-category="' + category + '"]');
                    if (input) { input.checked = !!(config.preferences && config.preferences[category]); }
                });
                if (!dialog.open) { dialog.showModal(); }
            }

            root.addEventListener('click', function (event) {
                var target = event.target.closest('[data-cc-accept],[data-cc-reject],[data-cc-customise],[data-cc-save],[data-cc-close]');
                if (!target) { return; }

                if (target.hasAttribute('data-cc-accept')) { setAll(true); }
                else if (target.hasAttribute('data-cc-reject')) { setAll(false); }
                else if (target.hasAttribute('data-cc-customise')) { open(); }
                else if (target.hasAttribute('data-cc-close')) { dialog.close(); }
                else {
                    var choices = {};
                    config.optional.forEach(function (category) {
                        var input = root.querySelector('[data-cc-category="' + category + '"]');
                        choices[category] = !!(input && input.checked);
                    });
                    save(choices);
                }
            });

            // Close when clicking the backdrop.
            dialog.addEventListener('click', function (event) { if (event.target === dialog) { dialog.close(); } });

            // "Cookie settings" links anywhere on the page.
            document.addEventListener('click', function (event) {
                var link = event.target.closest('[data-cookie-consent-open],a[href$="#cookie-settings"]');
                if (link) { event.preventDefault(); open(); }
            });

            if (location.hash === '#cookie-settings') { open(); }

            window.CookieConsent = {
                open: open,
                allows: function (category) {
                    return config.required.indexOf(category) !== -1 || !!(config.preferences && config.preferences[category]);
                }
            };
        })();
    </script>
</div>
