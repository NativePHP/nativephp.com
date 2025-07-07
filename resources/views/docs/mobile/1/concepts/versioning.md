---
title: Versioning
order: 600
---

## Overview

Proper versioning is crucial for mobile app development, affecting app store submissions, user updates, and feature compatibility. NativePHP Mobile provides flexible versioning strategies for both development and production environments.

## Version Configuration

### Environment Variables

Configure your app version in `.env`:

```bash
# App identifier (must match across all platforms)
NATIVEPHP_APP_ID=com.yourcompany.yourapp

# Version string (shown to users)
NATIVEPHP_APP_VERSION=1.2.3

# Version code (integer, must increase with each release)
NATIVEPHP_APP_VERSION_CODE=123
```

### Dynamic Versioning

```php
// Set version programmatically
config(['nativephp.app.version' => '1.2.3']);
config(['nativephp.app.version_code' => 123]);
```

## Version Types

### Semantic Versioning (Recommended)

Follow [Semantic Versioning](https://semver.org/) principles:

```
MAJOR.MINOR.PATCH
```

- **MAJOR** - Breaking changes or significant new features
- **MINOR** - New features, backward compatible
- **PATCH** - Bug fixes, backward compatible

Examples:
```bash
NATIVEPHP_APP_VERSION=1.0.0    # Initial release
NATIVEPHP_APP_VERSION=1.1.0    # New features added
NATIVEPHP_APP_VERSION=1.1.1    # Bug fixes
NATIVEPHP_APP_VERSION=2.0.0    # Breaking changes
```

### Version Codes

Version codes are integers that must increase with each release:

```bash
# v1.0.0
NATIVEPHP_APP_VERSION_CODE=100

# v1.1.0  
NATIVEPHP_APP_VERSION_CODE=110

# v1.1.1
NATIVEPHP_APP_VERSION_CODE=111

# v2.0.0
NATIVEPHP_APP_VERSION_CODE=200
```

## Environment-Specific Versioning

### Development

```bash
# .env.development
NATIVEPHP_APP_VERSION=1.2.3-dev
NATIVEPHP_APP_VERSION_CODE=99999
```

### Staging

```bash
# .env.staging
NATIVEPHP_APP_VERSION=1.2.3-beta
NATIVEPHP_APP_VERSION_CODE=99998
```

### Production

```bash
# .env.production
NATIVEPHP_APP_VERSION=1.2.3
NATIVEPHP_APP_VERSION_CODE=123
```

## Automated Versioning

### Git Tag-Based Versioning

```bash
#!/bin/bash
# scripts/set-version.sh

# Get version from git tag
VERSION=$(git describe --tags --exact-match 2>/dev/null || echo "dev")

# Remove 'v' prefix if present
VERSION=${VERSION#v}

# Set in environment
export NATIVEPHP_APP_VERSION=$VERSION

# Generate version code from semantic version
MAJOR=$(echo $VERSION | cut -d. -f1)
MINOR=$(echo $VERSION | cut -d. -f2)
PATCH=$(echo $VERSION | cut -d. -f3)
VERSION_CODE=$((MAJOR * 10000 + MINOR * 100 + PATCH))

export NATIVEPHP_APP_VERSION_CODE=$VERSION_CODE
```

### CI/CD Integration

```yaml
# GitHub Actions
- name: Set version from tag
  run: |
    if [[ $GITHUB_REF == refs/tags/* ]]; then
      VERSION=${GITHUB_REF#refs/tags/v}
      echo "NATIVEPHP_APP_VERSION=$VERSION" >> .env
      
      # Generate version code
      MAJOR=$(echo $VERSION | cut -d. -f1)
      MINOR=$(echo $VERSION | cut -d. -f2)
      PATCH=$(echo $VERSION | cut -d. -f3)
      VERSION_CODE=$((MAJOR * 10000 + MINOR * 100 + PATCH))
      echo "NATIVEPHP_APP_VERSION_CODE=$VERSION_CODE" >> .env
    fi
```

### Build Number Integration

```bash
# Use CI build number for development versions
NATIVEPHP_APP_VERSION=1.2.3-build.${BUILD_NUMBER}
NATIVEPHP_APP_VERSION_CODE=${BUILD_NUMBER}
```

## Version Management in Code

### Accessing Current Version

```php
use Livewire\Component;

class VersionDisplay extends Component
{
    public function mount()
    {
        $this->version = config('nativephp.app.version');
        $this->versionCode = config('nativephp.app.version_code');
    }

    public function render()
    {
        return view('livewire.version-display', [
            'version' => $this->version,
            'versionCode' => $this->versionCode
        ]);
    }
}
```

### Version Comparison

```php
class VersionManager
{
    public function isNewerVersion(string $current, string $new): bool
    {
        return version_compare($new, $current, '>');
    }

    public function getCurrentVersion(): string
    {
        return config('nativephp.app.version');
    }

    public function checkForUpdates(): array
    {
        $currentVersion = $this->getCurrentVersion();
        $latestVersion = $this->getLatestVersionFromApi();

        return [
            'has_update' => $this->isNewerVersion($currentVersion, $latestVersion),
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion
        ];
    }

    private function getLatestVersionFromApi(): string
    {
        // Check your API for latest version
        $response = Http::get('https://api.yourapp.com/version/latest');
        return $response->json('version');
    }
}
```

## App Store Considerations

### iOS App Store

- **CFBundleShortVersionString** - User-facing version (1.2.3)
- **CFBundleVersion** - Build number (123)
- Version must be incremented for each submission
- Can skip version numbers (1.0 → 1.2 is allowed)

### Google Play Store

- **versionName** - User-facing version string  
- **versionCode** - Integer that must increase with every release
- Cannot decrease version code
- Can reuse version names with different codes
- **Important**: `NATIVEPHP_APP_VERSION_CODE` must be incremented for each Google Play Store release, even for minor updates

### Configuration Example

```php
// config/nativephp.php
return [
    'app' => [
        'id' => env('NATIVEPHP_APP_ID', 'com.example.app'),
        'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),
        'version_code' => env('NATIVEPHP_APP_VERSION_CODE', 1),
    ],
];
```

## Best Practices

### Version Strategy
1. **Use semantic versioning** for consistency
2. **Increment version codes** for every build
3. **Test version upgrades** thoroughly
4. **Document breaking changes** clearly
5. **Plan update strategies** in advance

### Development Workflow
1. **Branch naming** - Include version in branch names (`feature/v1.2.0-new-ui`)
2. **Tag releases** - Use git tags for version tracking
3. **Automate versioning** - Reduce manual errors
4. **Test backwards compatibility** - Ensure smooth upgrades
5. **Maintain changelog** - Document all changes

### Release Management
1. **Staged rollouts** - Release to small groups first
2. **Feature flags** - Control feature availability by version
3. **Emergency updates** - Have a fast-track process for critical fixes
4. **Version analytics** - Track version adoption rates
5. **Sunset planning** - Plan when to stop supporting old versions

### Common Pitfalls
- **Don't decrease version codes** - Google Play Store will reject decreasing `NATIVEPHP_APP_VERSION_CODE`
- **Don't reuse version codes** - Each release must have a unique, higher version code
- **Don't skip testing upgrade paths** - Test how users transition between versions
- **Don't forget to update all platform configs** - Keep versions synchronized
- **Don't ignore app store requirements** - Each platform has specific versioning rules

### Google Play Store Version Code Requirements

The `NATIVEPHP_APP_VERSION_CODE` is critical for Google Play Store submissions:

```bash
# ❌ WRONG - Cannot decrease or reuse version codes
NATIVEPHP_APP_VERSION_CODE=100  # v1.0.0
NATIVEPHP_APP_VERSION_CODE=99   # v1.0.1 - REJECTED!

# ✅ CORRECT - Always increase version code
NATIVEPHP_APP_VERSION_CODE=100  # v1.0.0
NATIVEPHP_APP_VERSION_CODE=101  # v1.0.1 - Accepted
NATIVEPHP_APP_VERSION_CODE=102  # v1.0.2 - Accepted
NATIVEPHP_APP_VERSION_CODE=200  # v2.0.0 - Accepted
```

**Remember**: Even for hotfixes or minor updates, the version code must always increase.
