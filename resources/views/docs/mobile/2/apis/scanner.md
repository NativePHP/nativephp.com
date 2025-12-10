---
title: Scanner
order: 1200
---

## Overview

The Scanner API provides cross-platform barcode and QR code scanning capabilities through a native camera interface.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Scanner;
use Native\Mobile\Events\Scanner\CodeScanned;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { scanner, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Basic Usage

<x-snippet title="Basic Scanning">

<x-snippet.tab name="PHP">

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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { scanner, dialog, on, off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

// Open scanner
await scanner.scan();

// Listen for scan results
const handleScan = (payload) => {
    const { data, format, id } = payload;
    dialog.toast(`Scanned: ${data}`);
};

onMounted(() => {
    on(Events.Scanner.CodeScanned, handleScan);
});

onUnmounted(() => {
    off(Events.Scanner.CodeScanned, handleScan);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { scanner, dialog, on, off, Events } from '#nativephp';
import { useEffect } from 'react';

// Open scanner
await scanner.scan();

// Listen for scan results
const handleScan = (payload) => {
    const { data, format, id } = payload;
    dialog.toast(`Scanned: ${data}`);
};

useEffect(() => {
    on(Events.Scanner.CodeScanned, handleScan);

    return () => {
        off(Events.Scanner.CodeScanned, handleScan);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

## Configuration Methods

### `prompt(string $prompt)`

Set custom prompt text displayed on the scanner screen.

<x-snippet title="Custom Prompt">

<x-snippet.tab name="PHP">

```php
Scanner::scan()->prompt('Scan product barcode');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await scanner.scan()
    .prompt('Scan product barcode');
```

</x-snippet.tab>
</x-snippet>

### `continuous(bool $continuous = true)`

Keep scanner open to scan multiple codes. Default is `false` (closes after first scan).

<x-snippet title="Continuous Scanning">

<x-snippet.tab name="PHP">

```php
Scanner::scan()->continuous(true);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await scanner.scan()
    .continuous(true);
```

</x-snippet.tab>
</x-snippet>

### `formats(array $formats)`

Specify which barcode formats to scan. Default is `['qr']`.

**Available formats:** `qr`, `ean13`, `ean8`, `code128`, `code39`, `upca`, `upce`, `all`

<x-snippet title="Barcode Formats">

<x-snippet.tab name="PHP">

```php
Scanner::scan()->formats(['qr', 'ean13', 'code128']);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await scanner.scan()
    .formats(['qr', 'ean13', 'code128']);
```

</x-snippet.tab>
</x-snippet>

### `id(string $id)`

Set a unique identifier for the scan session. Useful for handling different scan contexts.

<x-snippet title="Scan Session ID">

<x-snippet.tab name="PHP">

```php
Scanner::scan()->id('checkout-scanner');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await scanner.scan()
    .id('checkout-scanner');
```

</x-snippet.tab>
</x-snippet>

### Combined Example

<x-snippet title="Advanced Scanner Configuration">

<x-snippet.tab name="PHP">

```php
Scanner::scan()
    ->prompt('Scan your ticket')
    ->continuous(true)
    ->formats(['qr', 'ean13'])
    ->id('ticket-scanner');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await scanner.scan()
    .prompt('Scan your ticket')
    .continuous(true)
    .formats(['qr', 'ean13'])
    .id('ticket-scanner');
```

</x-snippet.tab>
</x-snippet>

## Events

### `CodeScanned`

Fired when a barcode is successfully scanned.

**Properties:**
- `string $data` - The decoded barcode data
- `string $format` - The barcode format
- `string|null $id` - The scan session ID (if set)

<x-snippet title="CodeScanned Event">

<x-snippet.tab name="PHP">

```php
#[OnNative(CodeScanned::class)]
public function handleScan($data, $format, $id = null)
{
    if ($id === 'product-scanner') {
        $this->addProduct($data);
    }
}
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const handleScan = (payload) => {
    const { data, format, id } = payload;

    if (id === 'product-scanner') {
        addProduct(data);
    }
};

onMounted(() => {
    on(Events.Scanner.CodeScanned, handleScan);
});

onUnmounted(() => {
    off(Events.Scanner.CodeScanned, handleScan);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useEffect } from 'react';

const handleScan = (payload) => {
    const { data, format, id } = payload;

    if (id === 'product-scanner') {
        addProduct(data);
    }
};

useEffect(() => {
    on(Events.Scanner.CodeScanned, handleScan);

    return () => {
        off(Events.Scanner.CodeScanned, handleScan);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

## Notes

- **Platform Support:**
  - **Android:** ML Kit Barcode Scanning (API 21+)
  - **iOS:** AVFoundation (iOS 13.0+)
- **Permissions:** You must enable the `scanner` permission in `config/nativephp.php` to use the scanner. Camera 
  permissions are then handled automatically, and users will be prompted for permission the first time the scanner is
  used.
