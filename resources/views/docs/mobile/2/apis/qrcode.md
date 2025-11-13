---
title: QR Code Scanner
order: 350
---

## Overview

The QR Code Scanner API provides cross-platform barcode scanning capabilities through a native camera interface.

```php
use Native\Mobile\Facades\Scanner;
use Native\Mobile\Events\QrCode\Scanned;
```

## Basic Usage

```php
// Open scanner
Scanner::scan();

// Listen for scan results
#[On('native:'.Scanned::class)]
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

### `Scanned`

Fired when a barcode is successfully scanned.

**Properties:**
- `string $data` - The decoded barcode data
- `string $format` - The barcode format
- `string|null $id` - The scan session ID (if set)

```php
#[On('native:'.Scanned::class)]
public function handleScan($data, $format, $id = null)
{
    if ($id === 'product-scanner') {
        $this->addProduct($data);
    }
}
```

## Platform Support

- **Android:** ML Kit Barcode Scanning (API 21+)
- **iOS:** AVFoundation (iOS 13.0+)

Camera permissions are handled automatically on both platforms.