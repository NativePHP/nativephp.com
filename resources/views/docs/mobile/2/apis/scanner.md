---
title: Scanner
order: 1200
---

## Overview

The Scanner API provides cross-platform barcode and QR code scanning capabilities through a native camera interface.

```php
use Native\Mobile\Facades\Scanner;
use Native\Mobile\Events\Scanner\CodeScanned;
```

## Basic Usage

```php
// Open scanner
Scanner::scan();

// Listen for scan results
#[OnNative(CodeScanned::class)]
public function handleScan($data, $format, $id = null)
{
    Dialog::toast("Scanned: {$data}");
}
```

## Configuration Methods

### `prompt(string $prompt)`

Set custom prompt text displayed on the scanner screen.

```php
Scanner::scan()->prompt('Scan product barcode');
```

### `continuous(bool $continuous = true)`

Keep scanner open to scan multiple codes. Default is `false` (closes after first scan).

```php
Scanner::scan()->continuous(true);
```

### `formats(array $formats)`

Specify which barcode formats to scan. Default is `['qr']`.

**Available formats:** `qr`, `ean13`, `ean8`, `code128`, `code39`, `upca`, `upce`, `all`

```php
Scanner::scan()->formats(['qr', 'ean13', 'code128']);
```

### `id(string $id)`

Set a unique identifier for the scan session. Useful for handling different scan contexts.

```php
Scanner::scan()->id('checkout-scanner');
```

## Events

### `CodeScanned`

Fired when a barcode is successfully scanned.

**Properties:**
- `string $data` - The decoded barcode data
- `string $format` - The barcode format
- `string|null $id` - The scan session ID (if set)

```php
#[OnNative(CodeScanned::class)]
public function handleScan($data, $format, $id = null)
{
    if ($id === 'product-scanner') {
        $this->addProduct($data);
    }
}
```

## Notes

- **Platform Support:**
  - **Android:** ML Kit Barcode Scanning (API 21+)
  - **iOS:** AVFoundation (iOS 13.0+)
- **Permissions:** You must enable the `scanner` permission in `config/nativephp.php` to use the scanner. Camera 
  permissions are then handled automatically, and users will be prompted for permission the first time the scanner is
  used.
