---
title: System
order: 800
---

## Overview

The System API provides access to basic system functions and serves as a legacy interface for methods that have been moved to dedicated facades in v1.1.

```php
use Native\Mobile\Facades\System;
```

## Current Methods

### `flashlight()`

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

```php
System::flashlight(); // Toggle flashlight state
```

## Deprecated Methods (v1.1+)

The following methods are deprecated and have been moved to dedicated facades for better organization:

### ~~`camera()`~~ → Use `Camera::getPhoto()`

```php
// ❌ Deprecated (still works but not recommended)
$path = System::camera();

// ✅ Use instead
use Native\Mobile\Facades\Camera;
$path = Camera::getPhoto();
```

### ~~`vibrate()`~~ → Use `Haptics::vibrate()`

```php
// ❌ Deprecated (still works but not recommended)
System::vibrate();

// ✅ Use instead
use Native\Mobile\Facades\Haptics;
Haptics::vibrate();
```

### ~~`promptForBiometricID()`~~ → Use `Biometrics::promptForBiometricID()`

```php
// ❌ Deprecated (still works but not recommended)
$result = System::promptForBiometricID();

// ✅ Use instead
use Native\Mobile\Facades\Biometrics;
$result = Biometrics::promptForBiometricID();
```

### ~~`enrollForPushNotifications()`~~ → Use `PushNotifications::enrollForPushNotifications()`

```php
// ❌ Deprecated (still works but not recommended)
System::enrollForPushNotifications();

// ✅ Use instead
use Native\Mobile\Facades\PushNotifications;
PushNotifications::enrollForPushNotifications();
```

### ~~`getPushNotificationsToken()`~~ → Use `PushNotifications::getPushNotificationsToken()`

```php
// ❌ Deprecated (still works but not recommended)
$token = System::getPushNotificationsToken();

// ✅ Use instead
use Native\Mobile\Facades\PushNotifications;
$token = PushNotifications::getPushNotificationsToken();
```

### ~~`secureSet()` / `secureGet()`~~ → Use `SecureStorage`

```php
// ❌ Deprecated (still works but not recommended)
System::secureSet('key', 'value');
$value = System::secureGet('key');

// ✅ Use instead
use Native\Mobile\Facades\SecureStorage;
SecureStorage::set('key', 'value');
$value = SecureStorage::get('key');
```

## Example Usage

```php
use Livewire\Component;
use Native\Mobile\Facades\System;

class FlashlightController extends Component
{
    public bool $isFlashlightOn = false;

    public function toggleFlashlight()
    {
        System::flashlight();
        $this->isFlashlightOn = !$this->isFlashlightOn;
    }

    public function render()
    {
        return view('livewire.flashlight-controller');
    }
}
```

## Migration Guide

If you're upgrading from an earlier version of NativePHP Mobile, here's how to migrate your code:

### Step 1: Update Import Statements

```php
// Before
use Native\Mobile\Facades\System;

// After (add the specific facades you need)
use Native\Mobile\Facades\System;      // Still needed for flashlight
use Native\Mobile\Facades\Camera;      // For camera operations
use Native\Mobile\Facades\Haptics;     // For vibration
use Native\Mobile\Facades\Biometrics;  // For biometric auth
use Native\Mobile\Facades\PushNotifications; // For push notifications
use Native\Mobile\Facades\SecureStorage;     // For secure storage
```

### Step 2: Replace Method Calls

```php
class MigratedComponent extends Component
{
    public function oldWay()
    {
        // ❌ Old approach
        System::vibrate();
        $photo = System::camera();
        $biometric = System::promptForBiometricID();
        System::enrollForPushNotifications();
        $token = System::getPushNotificationsToken();
        System::secureSet('key', 'value');
        $value = System::secureGet('key');
    }

    public function newWay()
    {
        // ✅ New approach
        Haptics::vibrate();
        $photo = Camera::getPhoto();
        $biometric = Biometrics::promptForBiometricID();
        PushNotifications::enrollForPushNotifications();
        $token = PushNotifications::getPushNotificationsToken();
        SecureStorage::set('key', 'value');
        $value = SecureStorage::get('key');
    }
}
```

### Step 3: Update Event Listeners

Event names remain the same, but you may want to update your code organization:

```php
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\Biometric\Completed;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class ModernComponent extends Component
{
    // Events work the same way, just organized better
    #[On('native:' . PhotoTaken::class)]
    public function handlePhoto($path) { /* ... */ }

    #[On('native:' . Completed::class)]
    public function handleBiometric($success) { /* ... */ }

    #[On('native:' . TokenGenerated::class)]
    public function handleToken($token) { /* ... */ }
}
```

## Platform Support

### Flashlight
- **iOS:** Controls camera flash LED
- **Android:** Controls camera flash LED
- **Permissions:** None required
- **Limitations:** May not work if camera is currently in use

## Future Deprecation Notice

The System facade will continue to exist for backward compatibility, but new features will be added to the dedicated facades. We recommend migrating to the new facades when convenient to take advantage of:

- Better code organization
- Clearer API surface
- Enhanced features in dedicated facades
- Better IDE autocompletion and documentation

## Why the Change?

The original System facade became too large and mixed different concerns. The new structure provides:

- **Better organization:** Related methods grouped together
- **Clearer purpose:** Each facade has a single responsibility
- **Enhanced features:** New facades can offer richer APIs
- **Better maintenance:** Easier to add features and fix bugs
- **Improved documentation:** Each API can be documented thoroughly

The migration improves code clarity and makes the NativePHP Mobile API more intuitive for new developers.