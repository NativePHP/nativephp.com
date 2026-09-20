---
title: Versioning Policy
order: 500
---

NativePHP for Mobile follows [semantic versioning](https://semver.org) with a mobile-specific approach that distinguishes between
Laravel-only changes and native code changes. This ensures predictable updates and optimal compatibility.

Our aim is to limit the amount of work you need to do to get the latest updates and ensure everything works.

We will aim to post update instructions with each release.

## Release types

### Patch releases

Patch releases of `nativephp/mobile` should have **no breaking changes** and **only change Laravel/PHP code**.
This will typically include bug fixes and dependency updates that don't affect native code.

These releases should be completely compatible with the existing version of your native applications.

This means that you can:

- Safely update via `composer update`.
- Avoid a complete rebuild (no need to `native:install --force`).
- Allow for easier app updates avoiding the app stores.

### Minor releases  

Minor releases may contain **native code changes**. Respecting semantic versioning, these still should not contain
breaking changes, but there may be new native APIs, Kotlin/Swift updates, platform-specific features, or native
dependency changes.

Minor releases will:

- Require a complete rebuild (`php artisan native:install --force`) to work with the latest APIs.
- Need app store submission for distribution.
- Include advance notice and migration guides where necessary.

### Major releases

Major releases are reserved for breaking changes. This will usually follow a period of deprecations so that you have
time to make the necessary changes to your application code.

## Version constraints

We recommend using the [tilde range operator](https://getcomposer.org/doc/articles/versions.md#tilde-version-range-)
with a full minimum patch release defined in your `composer.json`:

```json
{
    "require": {
        "nativephp/mobile": "~2.0.0"
    }
}
```

This automatically receives patch updates while giving you control over minor releases.

## Version labels

Anything documented in this version of the docs has been here since 4.0 unless it carries a label. Labels appear next
to a page title, under a section heading, or beside the individual prop or class they describe:

| Label | Meaning |
|-------|---------|
| <x-docs.version-badge since="4.2" /> | Added in that minor release. Upgrade to at least that version to use it |
| <x-docs.version-badge changed="4.2" /> | Behaviour or signature changed in that release — check it against what your app relies on before upgrading |
| <x-docs.version-badge deprecated="4.2" /> | Still works, but slated for removal. Move off it when convenient |
| <x-docs.version-badge removed="4.2" /> | Gone as of that release. Documented only so you know what replaced it |

Remember that a minor release [may contain native code changes](#minor-releases), so picking up a labelled feature
means rebuilding with `php artisan native:install --force` rather than a `composer update` alone.

### Jump labels

[Jump](../the-basics/jump) ships on its own release cadence, so a feature can be released in NativePHP and still not
render on your phone when you scan a QR code. Where that's the case, you'll see:

| Label | Meaning |
|-------|---------|
| <x-docs.jump-badge since="99.0" /> | Needs a newer Jump than the one on the stores. Build to a simulator or device to try it today |
| <x-docs.jump-badge unavailable /> | No Jump build supports it. It'll work in a packaged build of your app |

These disappear on their own as Jump catches up. Pages carrying one don't offer the "Preview in Jump" QR code, since
scanning it wouldn't show you the component.

## Your application versioning

Just because we're using semantic versioning for the `nativephp/mobile` package, doesn't mean your app must follow that
same scheme.

You have complete freedom in versioning your own applications! You may use semantic versioning, codenames,
date-based versions, or any scheme that works for your project, team or business.

Remember that your app versions are usually public-facing (e.g. in store listings and on-device settings and update
screens) and can be useful for customers to reference if they need to contact you for help and support.
