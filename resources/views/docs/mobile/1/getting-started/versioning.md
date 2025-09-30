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
        "nativephp/mobile": "~1.1.0"
    }
}
```

This automatically receives patch updates while giving you control over minor releases.

## Your application versioning

Just because we're using semantic versioning for the `nativephp/mobile` package, doesn't mean your app must follow that
same scheme.

You have complete freedom in versioning your own applications! You may use semantic versioning, codenames,
date-based versions, or any scheme that works for your project, team or business.

Remember that your app versions are usually public-facing (e.g. in store listings and on-device settings and update
screens) and can be useful for customers to reference if they need to contact you for help and support.
