---
title: Release Notes
order: 600
---

## Version Policy

NativePHP Mobile follows [semantic versioning](https://semver.org) with a mobile-specific approach that distinguishes between Laravel-only changes and native code changes. This ensures predictable updates and optimal over-the-air (OTA) compatibility.

## Release Types

### Patch Releases
**Laravel/PHP code only** - Bug fixes, new Artisan commands, configuration updates, documentation, and dependency updates that don't affect native code.

- Safe to update immediately via `composer update`
- No app rebuild required
- Compatible with existing native builds
- Perfect for OTA updates

### Minor Releases  
**Native code changes** - New native APIs, Kotlin/Swift updates, platform-specific features, or native dependency changes.

- Require rebuilding your app with `php artisan native:install --force`
- Need app store submission for distribution
- Include advance notice and migration guides

## Support Policy

### What's Safe to Update
- **Patch releases** - Update immediately without rebuilding your app
- **Minor releases** - Plan for rebuild and app store submission

### Version Constraints
Use semantic versioning constraints in your `composer.json`:

```json
{
    "require": {
        "nativephp/mobile": "^1.1.0"
    }
}
```

This automatically receives patch updates while giving you control over minor releases.

## Your Application Versioning

You have complete freedom in versioning your own applications - use semantic versioning, codenames, date-based versions, or any scheme that works for your project. NativePHP Mobile's consistent approach ensures you always understand the impact of framework updates regardless of your chosen versioning strategy.
