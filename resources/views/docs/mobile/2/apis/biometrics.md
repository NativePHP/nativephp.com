---
title: Biometrics
order: 100
---

## Overview

The Biometrics API allows you to authenticate users using their device's biometric sensors like Face ID, Touch ID, or
fingerprint scanners.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Biometrics;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { biometric, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `prompt()`

Prompts the user for biometric authentication.

<x-snippet title="Biometric Prompt">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Biometrics;

Biometrics::prompt();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Basic usage
await biometric.prompt();

// With an identifier for tracking
await biometric.prompt()
    .id('secure-action-auth');
```

</x-snippet.tab>
</x-snippet>

## Events

### `Completed`

Fired when biometric authentication completes (success or failure).

<x-snippet title="Completed Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Biometric\Completed;

#[OnNative(Completed::class)]
public function handle(bool $success)
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { biometric, on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const isAuthenticated = ref(false);

const handleBiometricComplete = (payload) => {
    if (payload.success) {
        isAuthenticated.value = true;
        unlockSecureFeature();
    } else {
        showErrorMessage();
    }
};

const authenticate = async () => {
    await biometric.prompt();
};

onMounted(() => {
    on(Events.Biometric.Completed, handleBiometricComplete);
});

onUnmounted(() => {
    off(Events.Biometric.Completed, handleBiometricComplete);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { biometric, on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [isAuthenticated, setIsAuthenticated] = useState(false);

const handleBiometricComplete = (payload) => {
    if (payload.success) {
        setIsAuthenticated(true);
        unlockSecureFeature();
    } else {
        showErrorMessage();
    }
};

const authenticate = async () => {
    await biometric.prompt();
};

useEffect(() => {
    on(Events.Biometric.Completed, handleBiometricComplete);

    return () => {
        off(Events.Biometric.Completed, handleBiometricComplete);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

## Platform Support

- **iOS:** Face ID, Touch ID
- **Android:** Fingerprint, Face unlock, other biometric methods
- **Fallback:** System authentication (PIN, password, pattern)

## Security Notes

- Biometric authentication provides **convenience**, not absolute security
- Always combine with other authentication factors for sensitive operations
- Consider implementing session timeouts for unlocked states
- Users can potentially bypass biometrics if their device is compromised
