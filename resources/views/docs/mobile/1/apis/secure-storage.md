---
title: SecureStorage
order: 700
---

## Overview

The SecureStorage API provides secure storage using the device's native keychain (iOS) or keystore (Android) for sensitive data like tokens, passwords, and user credentials.

```php
use Native\Mobile\Facades\SecureStorage;
```

## Methods

### `set()`

Stores a secure value in the native keychain or keystore.

**Parameters:**
- `string $key` - The key to store the value under
- `string|null $value` - The value to store securely

**Returns:** `bool` - `true` if successfully stored, `false` otherwise

```php
$success = SecureStorage::set('api_token', 'abc123xyz');

if ($success) {
    // Token stored securely
} else {
    // Storage failed
}
```

### `get()`

Retrieves a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to retrieve the value for

**Returns:** `string|null` - The stored value or `null` if not found

```php
$token = SecureStorage::get('api_token');

if ($token) {
    // Use the retrieved token
    $this->authenticateWithToken($token);
} else {
    // Token not found, user needs to login
    $this->redirectToLogin();
}
```

### `delete()`

Deletes a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to delete the value for

**Returns:** `bool` - `true` if successfully deleted, `false` otherwise

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

## Best Practices

### What to Store
- API tokens and refresh tokens
- User credentials (if necessary)
- Encryption keys
- Sensitive user preferences
- Two-factor authentication secrets

### What NOT to Store
- Large amounts of data (use encrypted database instead)
- Non-sensitive configuration
- Temporary data
- Cached content


