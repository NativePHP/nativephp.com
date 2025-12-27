---
title: SecureStorage
order: 1300
---

## Overview

The SecureStorage API provides secure storage using the device's native keychain (iOS) or keystore (Android). It's
ideal for storing sensitive data like tokens, passwords, and user credentials.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\SecureStorage;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { secureStorage } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `set()`

Stores a secure value in the native keychain or keystore.

**Parameters:**
- `string $key` - The key to store the value under
- `string|null $value` - The value to store securely

**Returns:** `bool` - `true` if successfully stored, `false` otherwise

<x-snippet title="Set Secure Value">

<x-snippet.tab name="PHP">

```php
SecureStorage::set('api_token', 'abc123xyz');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await secureStorage.set('api_token', 'abc123xyz');

if (result.success) {
    // Value stored securely
}
```

</x-snippet.tab>
</x-snippet>

### `get()`

Retrieves a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to retrieve the value for

**Returns:** `string|null` - The stored value or `null` if not found

<x-snippet title="Get Secure Value">

<x-snippet.tab name="PHP">

```php
$token = SecureStorage::get('api_token');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await secureStorage.get('api_token');
const token = result.value; // or null if not found
```

</x-snippet.tab>
</x-snippet>

### `delete()`

Deletes a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to delete the value for

**Returns:** `bool` - `true` if successfully deleted, `false` otherwise

<x-snippet title="Delete Secure Value">

<x-snippet.tab name="PHP">

```php
SecureStorage::delete('api_token');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
const result = await secureStorage.delete('api_token');

if (result.success) {
    // Value deleted
}
```

</x-snippet.tab>
</x-snippet>

## Platform Implementation

### iOS - Keychain Services
- Uses the iOS Keychain Services API
- Data is encrypted and tied to your app's bundle ID
- Survives app deletion and reinstallation if iCloud Keychain is enabled
- Protected by device passcode/biometrics

### Android - Keystore
- Uses Android Keystore system
- Hardware-backed encryption when available
- Data is automatically deleted when app is uninstalled
- Protected by device lock screen

## Security Features

- **Encryption:** All data is automatically encrypted
- **App Isolation:** Data is only accessible by your app
- **System Protection:** Protected by device authentication
- **Tamper Resistance:** Hardware-backed security when available

## What to Store
- API tokens and refresh tokens
- User credentials (if necessary)
- Encryption keys
- Sensitive user preferences
- Two-factor authentication secrets

## What NOT to Store
- Large amounts of data (use encrypted database instead)
- Non-sensitive data
- Temporary data
- Cached content


