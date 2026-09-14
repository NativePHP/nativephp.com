---
title: Localization
order: 600
---

NativePHP makes it easy to detect your user's locale and configure your app's supported languages.

## Reading the device locale

NativePHP reads localization data directly from the native operating system — not from the browser or web view. This
gives you access to the user's true device-level language, region, and timezone settings.

Use the `Device` facade to get the user's current localization settings at runtime:

```php
use Native\Mobile\Facades\Device;

$loc = Device::localization();

$loc->locale;             // "fr_FR"
$loc->languageCode;       // "fr"
$loc->regionCode;         // "FR"
$loc->timezone;           // "Europe/Paris"
$loc->currencyCode;       // "EUR"
$loc->preferredLanguage;  // "fr"
```

This returns a `Localization` data object with the following properties:

| Property            | Type   | Example            | Description                   |
|---------------------|--------|--------------------|-------------------------------|
| `locale`            | string | `en_US`            | Full locale identifier        |
| `languageCode`      | string | `en`               | ISO 639 language code         |
| `regionCode`        | string | `US`               | ISO 3166 region/country code  |
| `timezone`          | string | `America/New_York` | IANA timezone identifier      |
| `currencyCode`      | string | `USD`              | ISO 4217 currency code        |
| `preferredLanguage` | string | `en`               | User's top preferred language |

You can use this to set Laravel's locale to match the device:

```php
app()->setLocale($loc->languageCode);
```

## Configuring supported locales

To ensure both platforms report accurate locale data, declare your app's supported locales in `config/nativephp.php`:

```php
'locales' => ['en', 'fr', 'es', 'de', 'ja'],
```

This is opt-in. If the array is empty or contains fewer than 2 entries, no build-time changes are made.

<aside>

#### Why is this needed?

Without this configuration, iOS will only report a locale matching your app's declared development region — not the
user's actual locale. On Android 13+, this also enables the per-app language picker in system settings.

</aside>

Use standard BCP 47 language tags. Region-specific codes like `pt-BR` or `zh-Hans` are also supported.

## Putting it together

```php
// config/nativephp.php
'locales' => ['en', 'fr', 'es'],
```

```php
use Native\Mobile\Facades\Device;

$loc = Device::localization();

// Set Laravel's locale to match the device
app()->setLocale($loc->languageCode);
```
