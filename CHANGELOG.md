# Changelog

## 1.1.0 - 2026-11-15

- Reject all now has the same style as Accept all, so neither choice looks preferred (UK ICO consent guidance).
- New `default_allowed` option: categories that are on until the visitor objects, for the UK's statistical purposes exception (Data (Use and Access) Act 2025). The banner says so, and Reject still turns them off.
- The banner and settings window titles are no longer `<h2>` headings, so they don't add to every page's heading outline. The banner region and dialog are still named by them.
- Google Consent Mode v2: new `google_consent_mode` and `google_consent_types` options, a `<x-cookie-consent-google />` component for the defaults in `<head>`, and a `consent update` when the visitor chooses.

## 1.0.0 - 2026-10-06

- First release: Accept / Reject / Customise banner, settings window with Functional, Analytics and Marketing categories, automatic cookie discovery for 12 common services, Blade components and directive, facade, and a JavaScript API.
