---
title: Biometrics
order: 100
---

## Overview

The Biometrics API allows you to authenticate users using their device's biometric sensors like Face ID, Touch ID, or
fingerprint scanners.

```php
use Native\Mobile\Facades\Biometrics;
```

## Methods

### `prompt()`

Prompts the user for biometric authentication.

```php
use Native\Mobile\Facades\Biometrics;

Biometrics::prompt();
```

## Events

### `Completed`

Fired when biometric authentication completes (success or failure).

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Biometric\Completed;

#[On('native:'.Completed::class)]
public function handleBiometricAuth(bool $success)
{
    if ($success) {
        // User authenticated successfully
        $this->unlockSecureFeature();
    } else {
        // Authentication failed
        $this->showErrorMessage();
    }
}
```

## Platform Support

- **iOS:** Face ID, Touch ID
- **Android:** Fingerprint, Face unlock, other biometric methods
- **Fallback:** System authentication (PIN, password, pattern)

## Security Notes

- Biometric authentication provides **convenience**, not absolute security
- Always combine with other authentication factors for sensitive operations
- Consider implementing session timeouts for unlocked states
- Users can potentially bypass biometrics if their device is compromised
