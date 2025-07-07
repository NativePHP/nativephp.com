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

## Code Examples

### Number Formatting

```php
use NumberFormatter;

class LocalizedNumbers
{
    public function formatCurrency(float $amount, string $locale, string $currency): string
    {
        if (!extension_loaded('intl')) {
            // Fallback for non-ICU builds
            return $currency . ' ' . number_format($amount, 2);
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }

    public function formatPercent(float $value, string $locale): string
    {
        if (!extension_loaded('intl')) {
            return number_format($value * 100, 1) . '%';
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::PERCENT);
        return $formatter->format($value);
    }
}

// Usage
$numbers = new LocalizedNumbers();

echo $numbers->formatCurrency(1234.56, 'en_US', 'USD'); // $1,234.56
echo $numbers->formatCurrency(1234.56, 'de_DE', 'EUR'); // 1.234,56 €
echo $numbers->formatPercent(0.1234, 'en_US'); // 12.3%
```

### Date Formatting

```php
use IntlDateFormatter;

class LocalizedDates
{
    public function formatDate(\DateTime $date, string $locale): string
    {
        if (!extension_loaded('intl')) {
            return $date->format('M j, Y');
        }

        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE
        );
        
        return $formatter->format($date);
    }
}

// Usage
$dates = new LocalizedDates();
$date = new DateTime('2024-03-15');

echo $dates->formatDate($date, 'en_US'); // March 15, 2024
echo $dates->formatDate($date, 'de_DE'); // 15. März 2024
echo $dates->formatDate($date, 'ja_JP'); // 2024年3月15日
```

### String Collation

```php
use Collator;

class LocalizedSorting
{
    public function sortNames(array $names, string $locale): array
    {
        if (!extension_loaded('intl')) {
            // Simple ASCII sort fallback
            sort($names);
            return $names;
        }

        $collator = new Collator($locale);
        $collator->sort($names);
        return $names;
    }
}

// Usage
$sorter = new LocalizedSorting();
$names = ['Müller', 'Mueller', 'Miller', 'Möller'];

$sorted = $sorter->sortNames($names, 'de_DE');
// Proper German sorting with umlauts
```

## Framework Integration

### Laravel Localization

```php
// config/app.php
return [
    'locale' => 'en',
    'available_locales' => ['en', 'es', 'fr', 'de', 'ja'],
];

// With ICU support, you can use advanced formatters
class LocalizedContent
{
    public function getLocalizedPrice(float $price): string
    {
        $locale = app()->getLocale();
        $currency = config('app.currency.' . $locale, 'USD');
        
        if (extension_loaded('intl')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($price, $currency);
        }
        
        return $currency . ' ' . number_format($price, 2);
    }
}
```

### Filament Integration

```php
// Filament requires ICU for proper operation
use Filament\Forms\Components\TextInput;

TextInput::make('price')
    ->numeric()
    ->formatStateUsing(function ($state) {
        // This formatting requires ICU
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($state, 'USD');
    });
```

## Graceful Degradation

Design your app to work with or without ICU:

```php
class InternationalizationHelper
{
    public static function hasICU(): bool
    {
        return extension_loaded('intl');
    }

    public static function formatNumber(float $number, string $locale = 'en_US'): string
    {
        if (self::hasICU()) {
            $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
            return $formatter->format($number);
        }
        
        // Fallback formatting
        return number_format($number, 2);
    }

    public static function compareStrings(string $a, string $b, string $locale = 'en_US'): int
    {
        if (self::hasICU()) {
            $collator = new Collator($locale);
            return $collator->compare($a, $b);
        }
        
        // Fallback to simple comparison
        return strcmp($a, $b);
    }
}
```

## Performance Considerations

### Memory Usage
- ICU adds ~28MB to your app size
- Runtime memory usage increases slightly
- Complex formatting operations are slower

### Optimization Tips

```php
class OptimizedFormatting
{
    private static array $formatters = [];

    public static function getCachedFormatter(string $locale, int $style): NumberFormatter
    {
        $key = $locale . '_' . $style;
        
        if (!isset(self::$formatters[$key])) {
            self::$formatters[$key] = new NumberFormatter($locale, $style);
        }
        
        return self::$formatters[$key];
    }

    public static function formatCurrency(float $amount, string $locale, string $currency): string
    {
        $formatter = self::getCachedFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
}
```

## Testing ICU Features

```php
// tests/Feature/ICUSupportTest.php
namespace Tests\Feature;

use Tests\TestCase;

class ICUSupportTest extends TestCase
{
    public function test_icu_extension_loaded()
    {
        if (config('app.requires_icu')) {
            $this->assertTrue(extension_loaded('intl'), 'ICU extension is required but not loaded');
        }
    }

    public function test_number_formatting_works()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ICU not available');
        }

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $result = $formatter->formatCurrency(1234.56, 'USD');
        
        $this->assertEquals('$1,234.56', $result);
    }

    public function test_fallback_formatting_works()
    {
        $helper = new InternationalizationHelper();
        $result = $helper->formatNumber(1234.56);
        
        $this->assertIsString($result);
        $this->assertStringContains('1234', $result);
    }
}
```

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
