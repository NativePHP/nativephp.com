---
title: CI/CD Integration
order: 500
---

## Overview

NativePHP for Mobile provides robust CLI commands designed for automated CI/CD environments. With proper configuration,
you can build, package, and deploy mobile apps without manual intervention.

## Key Commands for CI/CD

### Installation Command

Install NativePHP dependencies in automated environments:

```shell
# Install Android platform, overwriting existing files
php artisan native:install android --force --no-tty

# Install with ICU support for Filament/intl features
php artisan native:install android --force --with-icu

# Install both platforms
php artisan native:install both --force
```

### Build Commands

Build your app for different environments:

```shell
# Build debug version (development)
php artisan native:run android --build=debug --no-tty

# Build release version (production)
php artisan native:run android --build=release --no-tty

# Build app bundle for Play Store
php artisan native:run android --build=bundle --no-tty
```

### Packaging Command

Package signed releases for distribution:

```bash
# Package signed APK using environment variables
php artisan native:package android --build-type=release --output=/artifacts --no-tty

# Package signed App Bundle for Play Store
php artisan native:package android --build-type=bundle --output=/artifacts --no-tty
```

## Environment Variables

Store sensitive signing information in environment variables:

```bash
# Android Signing
ANDROID_KEYSTORE_FILE="/path/to/keystore.jks"
ANDROID_KEYSTORE_PASSWORD="your-keystore-password"
ANDROID_KEY_ALIAS="your-key-alias"
ANDROID_KEY_PASSWORD="your-key-password"
```

## Command Line Options

### `--no-tty` Flag
Essential for CI/CD environments where TTY is not available:
- Disables interactive prompts
- Provides non-interactive output
- Shows build progress without real-time updates
- Required for most automated environments

### `--force` Flag
Overwrites existing files and directories:
- Useful for clean builds in CI
- Ensures fresh installation of NativePHP scaffolding
- Prevents build failures from existing files
- Do this whenever you are updating the `nativephp/mobile` package.

### Build Types
- `--build=debug`: Development builds with debugging enabled
- `--build=release`: Production builds optimized for distribution
- `--build=bundle`: App bundles for Play Store distribution

## Signing Configuration

### Using Command Line Options
```bash
php artisan native:package android \
  --build-type=release \
  --keystore=/path/to/keystore.jks \
  --keystore-password=your-password \
  --key-alias=your-alias \
  --key-password=your-key-password \
  --output=./artifacts \
  --no-tty
```

### Using Environment Variables (Recommended)
```bash
# Set environment variables in CI
export ANDROID_KEYSTORE_FILE="/path/to/keystore.jks"
export ANDROID_KEYSTORE_PASSWORD="your-password"
export ANDROID_KEY_ALIAS="your-alias"
export ANDROID_KEY_PASSWORD="your-key-password"

# Run packaging command
php artisan native:package android --build-type=release --output=./artifacts --no-tty
```

## Common CI Workflows

### Development Pipeline
1. Install dependencies: `composer install`
2. Setup environment: copy `.env`, generate key
3. Install NativePHP: `native:install android --force`
4. Build debug: `native:run android --build=debug --no-tty`

### Release Pipeline
1. Install dependencies: `composer install --no-dev --optimize-autoloader`
2. Setup environment with production settings
3. Install NativePHP: `native:install android --force --with-icu`
4. Package release: `native:package android --build-type=release --no-tty`

### Play Store Pipeline
1. Same as release pipeline through step 3
2. Package bundle: `native:package android --build-type=bundle --no-tty`
3. Upload to Play Console

## Error Handling

NativePHP commands provide proper exit codes for CI/CD:
- `0`: Success
- `1`: General error
- Build errors are logged and reported

Monitor build logs for:
- Compilation errors
- Signing failures
- Missing dependencies
- Permission issues

## Performance Tips

### Caching
Cache these directories in CI for faster builds:
- `vendor/` (Composer dependencies)
- `nativephp/android/` (Android project)
- Android SDK components

### Optimization
- Use `--no-dev` for production Composer installs
- Enable Composer autoloader optimization
- Minimize included files with cleanup configuration

The `--no-tty` flag and environment variable support make NativePHP Mobile well-suited for modern CI/CD pipelines, enabling fully automated mobile app builds and deployments.
