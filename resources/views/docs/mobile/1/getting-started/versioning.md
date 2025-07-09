---
title: Release Notes
order: 600
---

## NativePHP Mobile Versioning Strategy

This document outlines how NativePHP Mobile itself will be versioned moving forward. As we develop tools to assist with OTA updates and full app bundles, our versioning strategy ensures predictable release cycles and update compatibility.

**Note**: You can version your own applications however you prefer - including codenames, custom schemes, or any versioning pattern that works for your project. This document specifically covers how NativePHP Mobile releases will be versioned.

## Our Release Types

### Patch Releases (Laravel-Only Changes)

When NativePHP Mobile updates contain only Laravel/PHP code changes, we will increment the patch version:

- **Laravel code improvements** - Core functionality, API enhancements, bug fixes
- **Configuration updates** - Default settings, environment handling
- **Documentation updates** - README, guides, examples
- **Dependency updates** - Composer package updates that don't affect native code

**Version Pattern**: `X.Y.Z` → `X.Y.Z+1` (patch increment)

```bash
# Example: Laravel bug fix in NativePHP Mobile
1.2.3 → 1.2.4
```

**Deployment**: These updates can be consumed immediately via Composer without requiring developers to rebuild their native applications.

### Minor Releases (Native Code Changes)

When NativePHP Mobile updates require changes to Kotlin/Swift code, we will increment the minor version:

- **Kotlin/Swift code changes** - New native functionality, native bug fixes
- **New native features** - Camera integration, location services, push notifications
- **Native dependency updates** - Changes to native SDKs or libraries
- **Platform-specific implementations** - iOS or Android specific code changes

**Version Pattern**: `X.Y.Z` → `X.Y+1.0` (minor increment, reset patch to 0)

```bash
# Example: New camera feature requiring native code updates
1.2.3 → 1.3.0
```

**Deployment**: These updates require developers to rebuild their applications and submit new versions to app stores.

## Why This Versioning Strategy Matters

By following this consistent versioning pattern, we guarantee:

1. **Predictable Update Impact** - Patch versions are safe to update without rebuilding apps
2. **Clear Breaking Change Communication** - Minor versions signal when native rebuilds are required
3. **OTA Update Compatibility** - Our tools can safely determine which updates can be deployed over-the-air
4. **Developer Confidence** - You know exactly what each version bump means for your development workflow

## Version Examples

### Patch Version Scenarios

```bash
# Bug fix in package command
1.2.3 → 1.2.4

# New Artisan command added
1.2.4 → 1.2.5

# Change the way we move files around on Windows
1.2.5 → 1.2.6

# Composer dependency security update
1.2.6 → 1.2.7
```

### Minor Version Scenarios

```bash
# New camera API integration
1.2.7 → 1.3.0

# iOS-specific push notification changes
1.3.0 → 1.4.0

# Android permissions handling update
1.4.0 → 1.5.0

# New native file system access
1.5.0 → 1.6.0
```

## Impact on Your Development

### For Patch Releases

When we release a patch version:
- **Update immediately** - Run `composer update` to get the latest improvements
- **No rebuild required** - Your existing native apps continue working
- **OTA compatible** - Any improvements are immediately available to your users

### For Minor Releases

When we release a minor version:
- **Update package** - Run `composer update` to get the latest version
- **Reinstall native code** - Run `php artisan native:install --force` to update native components
- **Review changelog** - Check what new native features or changes are included
- **Test thoroughly** - Ensure compatibility with your existing application code
- **Submit to app stores** - New native code requires store approval

## Release Communication

### Patch Releases
- **Immediate availability** - Available via Composer as soon as released
- **Minimal disruption** - No impact on your release schedule
- **Automatic compatibility** - Works with existing native builds

### Minor Releases
- **Advance notice** - Announced ahead of time to allow planning
- **Migration guides** - Documentation for any required changes
- **Testing period** - Beta releases available for early testing

## Implementation Details

### Version Code Management

Our version codes follow this pattern:

```bash
# Version 1.2.3 = Version Code 123
# Version 1.2.4 = Version Code 124
# Version 1.3.0 = Version Code 130
```

### Composer Integration

```json
{
    "require": {
        "nativephp/mobile": "^1.2.3"
    }
}
```

Using semantic versioning constraints ensures you receive patch updates automatically while controlling when to adopt minor releases.

## Your Application Versioning

While NativePHP Mobile follows this structured approach, **you have complete freedom** in how you version your own applications:

- **Semantic versioning** - `1.2.3`, `2.0.0`, etc.
- **Codenames** - `"Falcon"`, `"Eagle"`, `"Hawk"`
- **Date-based** - `2024.01.15`, `2024.02.01`
- **Build numbers** - `Build 1234`, `Version 5678`
- **Custom schemes** - Whatever works for your project

The key is that regardless of how you version your app, NativePHP Mobile's consistent versioning ensures you always know the impact of framework updates on your development process.

## Benefits of This Approach

### For Developers
- **Predictable updates** - Always know what to expect from version bumps
- **Reduced friction** - Patch updates don't disrupt your release cycle
- **Clear upgrade paths** - Minor versions provide structured upgrade opportunities

### For End Users
- **Faster improvements** - Laravel enhancements reach users immediately
- **Stable experience** - Native changes are properly tested before release
- **Reliable updates** - Consistent versioning prevents compatibility issues

This versioning strategy enables us to deliver improvements quickly while maintaining the stability and predictability you need for production applications.
