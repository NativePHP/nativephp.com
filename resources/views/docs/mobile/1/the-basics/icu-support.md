---
title: ICU Support
order: 300
---

## Overview

ICU (International Components for Unicode) is a library that provides robust Unicode and locale support for applications. While NativePHP Mobile includes a lightweight PHP runtime by default, you can optionally enable ICU support for applications that require advanced internationalization features.

## What is ICU?

ICU provides:
- **Unicode support** - Full Unicode text processing
- **Localization** - Number, date, and currency formatting
- **Collation** - Language-sensitive string comparison
- **Text boundaries** - Word, sentence, and line breaking
- **Transliteration** - Script conversion between languages

## When Do You Need ICU?

### Required for:
- **Number formatting** with locale-specific rules
- **Date/time formatting** with localized patterns  
- **Currency formatting** with proper symbols and rules
- **String collation** for sorting in different languages
- **Text normalization** and case conversion
- **Complex text rendering** for right-to-left languages

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

## Size Considerations

| PHP Runtime | Size  | Use Case |
|-------------|-------|----------|
| Standard (no ICU) | ~7MB  | Basic apps, simple localization |
| With ICU | ~34MB | Filament, complex i18n, number formatting |

The ICU-enabled runtime is approximately **5x larger**, so only enable it if you specifically need these features.

## Feature Comparison

### Without ICU (Default)

```php
// ✅ Works - Basic functionality
$date = now()->format('Y-m-d');
$number = number_format(1234.56, 2);

// ❌ Won't work - Requires intl extension
$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
$collator = new Collator('en_US');
```

### With ICU Enabled

```php
// ✅ All basic functionality works
$date = now()->format('Y-m-d');
$number = number_format(1234.56, 2);

// ✅ Advanced internationalization works
$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
$price = $formatter->formatCurrency(1234.56, 'USD'); // $1,234.56

$collator = new Collator('en_US');
$result = $collator->compare('apple', 'äpple'); // Proper Unicode comparison
```


## Performance Considerations

### Memory Usage
- ICU adds ~28MB to your app size
- Runtime memory usage increases slightly
- Complex formatting operations are slower

## Decision Matrix

Use this matrix to decide if you need ICU support:

| Feature Needed | ICU Required | Alternative |
|----------------|--------------|-------------|
| Basic number formatting | ❌ | `number_format()` |
| Locale-specific currency | ✅ | Manual formatting |
| Date localization | ✅ | Carbon with locales |
| String sorting (non-ASCII) | ✅ | Basic `sort()` |
| Filament admin panel | ✅ | Custom admin |
| Multi-language text processing | ✅ | Limited alternatives |
| Unicode normalization | ✅ | Basic string functions |

## Best Practices

1. **Evaluate early** - Decide on ICU support before building your app
2. **Design for fallbacks** - Always provide non-ICU alternatives
3. **Cache formatters** - Reuse NumberFormatter and Collator instances
4. **Test both scenarios** - Test your app with and without ICU
5. **Document requirements** - Clearly state if your app requires ICU
6. **Monitor app size** - Consider the trade-off between features and size
7. **Profile performance** - ICU operations can be slower than simple alternatives

Choose ICU support if you're building a truly international app, using Filament, or need advanced text processing. For simpler apps, the default lightweight runtime is usually sufficient.
