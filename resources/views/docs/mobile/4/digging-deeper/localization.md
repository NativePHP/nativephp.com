---
title: Localization
order: 95
---

## Overview

<x-docs.version-badge since="4.6" />

Tell NativePHP which languages your app supports and your users can pick one for your app, separately from the
language of their device. On Android your app appears in the system's per-app language picker. On iOS it gets a
Preferred Language setting, and the App Store lists those languages on your product page.

You declare the languages in one config key. Translating your app still happens the Laravel way, with `lang/` files
and `__()`.

## Declaring your languages

List the languages in the `supported_locales` array in `config/nativephp.php`:

```php
'supported_locales' => ['fr', 'nl', 'zh-Hans'],
```

Your app's own language, `config('app.locale')`, is always included and always comes first, so users can switch back
to it. You don't list it yourself. If `app.locale` is `en`, the config above gives your app four languages: `en`,
`fr`, `nl` and `zh-Hans`.

- Entries are BCP 47 codes such as `nl`, `pt-BR` or `zh-Hans`. Laravel-style `nl_NL` is accepted and becomes `nl-NL`.
- Duplicates are dropped, ignoring case.
- Invalid entries are skipped with a [build warning](#build-warnings).
- An empty array means your app supports one language, `app.locale`.

<aside>

The list is read at build time. The base language of a build is whatever `APP_LOCALE` was on the machine that built
it.

</aside>

## What your users see

### Android

On Android 13 and later, your app appears in the system's per-app language picker under Settings → Apps → your app →
Language. Before 4.6, a NativePHP app could only follow the system language.

To do this, NativePHP writes `res/xml/locales_config.xml` and points your app's manifest at it. This only happens when
your app supports two or more languages. With one language the app is left untouched and no Language entry shows.

### iOS

NativePHP declares the languages as `CFBundleLocalizations` in your app's `Info.plist`. Your app then gets a Preferred
Language row under Settings → your app.

<aside>

iOS only shows the Preferred Language row when the device has more than one preferred language set under
Settings → General → Language & Region. If the row is missing while you test, add a second language there.

</aside>

### App Store

The App Store lists exactly these languages on your app's product page.

## Translating your app

Listing a locale only lets the user pick it. Translating your app is still your job. Add
[Laravel language files](https://laravel.com/docs/localization) as you would in any Laravel app, either
`lang/fr/...` or `lang/fr.json`, and use `__()` for your strings.

## Using the chosen language

NativePHP doesn't set Laravel's locale for you. The language the user picked is reported by
[`Device::getInfo()`](../the-basics/device#device-info) in the `language` field, as a BCP 47 tag such as `fr-FR`.

Read it early, for example in `AppServiceProvider::boot()`, and pass it to `App::setLocale()`:

```php
use Illuminate\Support\Facades\App;
use Native\Mobile\Facades\Device;

// AppServiceProvider::boot()
$info = Device::getInfo(); // JSON string, or null when not on a device

if ($info !== null) {
    $language = json_decode($info, true)['language'] ?? ''; // 'fr-FR'
    $locale = explode('-', $language)[0]; // 'fr'

    if (in_array($locale, ['fr', 'nl'], true)) {
        App::setLocale($locale);
    }
}
```

The tag includes a region (`fr-FR`), while a Laravel `lang/` folder is usually just `fr`, so the example keeps only the
language part. It also only switches to a locale the app has translations for. Anything else stays on `app.locale`.

<aside>

`boot()` runs once each time PHP starts. iOS quits your app when the user changes its language, so `boot()` runs again
when they reopen it. Android only rebuilds the screen and, with the default
[persistent runtime](../getting-started/configuration#persistent-runtime), keeps PHP running. There, a change made
while your app is open applies the next time the app starts.

</aside>

## Permission strings

On iOS, translated permission strings are only written for the languages your app supports. That covers your own
`permission_localizations` and the translations your plugins ship. Entries for any other locale are skipped.

A plugin can bring translations for its permission strings, but it can't add a language to your app. See
[Localizing iOS Permission Strings](../getting-started/configuration#localizing-ios-permission-strings).

## Build warnings

NativePHP prints these warnings while it builds your app, during `native:run` and when you package it.

```
Translations exist for de but nativephp.supported_locales does not list them, so the app will not offer them
```

Your `lang/` directory has a folder or `.json` file for a locale that isn't in `supported_locales`. Add the locale to
the list if you want users to be able to pick it. `lang/vendor` is ignored.

NativePHP doesn't scan `lang/` to decide which languages ship. The list stays explicit, so a translation you've only
just started isn't offered to users by accident.

```
Ignoring invalid locale 'english' in nativephp.supported_locales
```

An entry in `supported_locales` isn't a locale code. The entry is skipped and the rest of the list is used.

## Removing a language

Take the locale out of `supported_locales` and rebuild. You don't need to run `native:install --force`.

- **Android:** the locale is removed from the locale config. If only one language is left, NativePHP deletes the locale
  config and takes the attribute back out of the manifest.
- **iOS:** `CFBundleLocalizations` is rewritten and the `.lproj` folder NativePHP generated for that language is
  deleted. An `.lproj` folder holding anything besides the generated `InfoPlist.strings` is left alone.
