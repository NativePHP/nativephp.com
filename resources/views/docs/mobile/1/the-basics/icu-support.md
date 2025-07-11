---
title: ICU Support
order: 300
---

## Overview

ICU (International Components for Unicode) is a library that provides robust Unicode and locale support for applications. While NativePHP Mobile includes a lightweight PHP runtime by default, you can optionally enable ICU support for applications that require advanced internationalization features.

> **Note**: ICU support is currently only available on Android. We are working to add iOS support as soon as possible and will remove this note when it becomes available.

## What is ICU?

ICU provides:
- **Unicode support** - Full Unicode text processing
- **Localization** - Number, date, and currency formatting
- **Collation** - Language-sensitive string comparison
- **Text boundaries** - Word, sentence, and line breaking
- **Transliteration** - Script conversion between languages

## When Do You Need ICU?

### Specifically Required for:
- **[Filament](https://filamentphp.com/)** - Uses `intl` extension extensively
- **Laravel's localization helpers** that depend on `intl`
- **Third-party packages** that require `intl` extension
- **Multi-language applications** with complex formatting needs

## Installation

### During Initial Setup

When running `php artisan native:install`, you'll be prompted:

```bash
php artisan native:install

# You'll see:
? ➕ Include ICU-enabled PHP binary for Filament/intl requirements? (~30MB extra)
  > No (default - smaller app size)
    Yes (required for Filament and advanced i18n)
```

Select **"Yes"** if you need ICU support.

### After Installation

If you already installed without ICU and need to add it:

```bash
# Re-run the installer and select ICU support
php artisan native:install --force
```

## Performance Considerations

### Memory Usage
- ICU adds ~28MB to your app size
- Runtime memory usage increases slightly
- Complex formatting operations are slower
