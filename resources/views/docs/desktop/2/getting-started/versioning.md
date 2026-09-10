---
title: Versioning Policy
order: 600
---

NativePHP for Desktop follows [semantic versioning](https://semver.org) for the `nativephp/desktop` package itself.

Unlike distributing through an app store, updating a NativePHP desktop app doesn't require your users to
manually download and reinstall anything — the [built-in updater](../publishing/updating) checks your configured
provider (GitHub Releases, S3, or DigitalOcean Spaces) for a newer build and installs it automatically. This means
there's no separate "requires a rebuild" release tier the way there is for app-store-distributed platforms: any new
`php artisan native:publish` you push out reaches your users the same way, regardless of what changed.

Your app's own version — set in `config/nativephp.php` — is separate from the `nativephp/desktop` package version.
It's what the [updater](../publishing/updating) and your app's database migrations key off, and you're free to use
whatever scheme suits you.

## Version constraints

We recommend using the [tilde range operator](https://getcomposer.org/doc/articles/versions.md#tilde-version-range-)
with a full minimum patch release defined in your `composer.json`:

```json
{
    "require": {
        "nativephp/desktop": "~2.0.0"
    }
}
```

This automatically receives patch updates while giving you control over minor releases.

## Version labels

Anything documented in this version of the docs has been here since 2.0 unless it carries a label. Labels appear
next to a page title, under a section heading, or beside the individual prop or class they describe:

| Label | Meaning |
|-------|---------|
| <x-docs.version-badge since="2.2" /> | Added in that minor release |
| <x-docs.version-badge changed="2.2" /> | Behaviour or signature changed in that release — check it against what your app relies on before upgrading |
| <x-docs.version-badge deprecated="2.2" /> | Still works, but slated for removal. Move off it when convenient |
| <x-docs.version-badge removed="2.2" /> | Gone as of that release. Documented only so you know what replaced it |

See everything labelled across these docs, grouped by release, on the [What's New]({{ \App\Support\DocsLabels::whatsNewUrl() }}) page.
