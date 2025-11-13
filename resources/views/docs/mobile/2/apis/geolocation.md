---
title: Geolocation
order: 400
---

## Overview

The Geolocation API provides access to the device's GPS and location services to determine the user's current position.

```php
use Native\Mobile\Facades\Geolocation;
```

## Methods

### `getCurrentPosition()`

Gets the current GPS location of the device.

**Parameters:**
- `bool $fineAccuracy` - Whether to use high accuracy mode (GPS vs network) (default: `false`)

**Returns:** Location data via events

```php
// Get location using network positioning (faster, less accurate)
Geolocation::getCurrentPosition();

// Get location using GPS (slower, more accurate)
Geolocation::getCurrentPosition(true);
```

### `checkPermissions()`

Checks the current location permissions status.

**Returns:** Permission status via events

```php
Geolocation::checkPermissions();
```

### `requestPermissions()`

Requests location permissions from the user.

**Returns:** Permission status after request via events

```php
Geolocation::requestPermissions();
```

## Events

### `LocationReceived`

Fired when location data is requested (success or failure).

**Event Parameters:**
- `bool $success` - Whether location was successfully retrieved
- `float $latitude` - Latitude coordinate (when successful)
- `float $longitude` - Longitude coordinate (when successful)
- `float $accuracy` - Accuracy in meters (when successful)
- `int $timestamp` - Unix timestamp of location fix
- `string $provider` - Location provider used (GPS, network, etc.)
- `string $error` - Error message (when unsuccessful)

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Geolocation\LocationReceived;

#[On('native:'.LocationReceived::class)]
public function handleLocationReceived(
    $success = null,
    $latitude = null,
    $longitude = null,
    $accuracy = null,
    $timestamp = null,
    $provider = null,
    $error = null
) {
    // ...
}
```

### `PermissionStatusReceived`

Fired when permission status is checked.

**Event Parameters:**
- `string $location` - Overall location permission status
- `string $coarseLocation` - Coarse location permission status  
- `string $fineLocation` - Fine location permission status

**Permission Values:**
- `'granted'` - Permission is granted
- `'denied'` - Permission is denied
- `'not_determined'` - Permission not yet requested

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Geolocation\PermissionStatusReceived;

#[On('native:'.PermissionStatusReceived::class)]
public function handlePermissionStatus($location, $coarseLocation, $fineLocation)
{
    // ...
}
```

### `PermissionRequestResult`

Fired when a permission request completes.

**Event Parameters:**
- `string $location` - Overall location permission result
- `string $coarseLocation` - Coarse location permission result
- `string $fineLocation` - Fine location permission result
- `string $message` - Optional message (for permanently denied)
- `bool $needsSettings` - Whether user needs to go to Settings

**Special Values:**
- `'permanently_denied'` - User has permanently denied permission

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Geolocation\PermissionRequestResult;

#[On('native:' . PermissionRequestResult::class)]
public function handlePermissionRequest($location, $coarseLocation, $fineLocation, $message = null, $needsSettings = null)
{
    if ($location === 'permanently_denied') {
        $this->error = 'Location permission permanently denied. Please enable in Settings.';
    } elseif ($coarseLocation === 'granted' || $fineLocation === 'granted') {
        $this->getCurrentLocation();
    } else {
        $this->error = 'Location permission is required for this feature.';
    }
}
```

## Privacy Considerations

- **Explain why** you need location access before requesting
- **Request at the right time** - when the feature is actually needed
- **Respect denials** - provide alternative functionality when possible
- **Use appropriate accuracy** - don't request fine location if coarse is sufficient
- **Limit frequency** - don't request location updates constantly

### Performance Considerations
- **Battery Usage** - GPS uses more battery than network location
- **Time to Fix** - GPS takes longer for initial position
- **Indoor Accuracy** - GPS may not work well indoors
- **Caching** - Consider caching recent locations for better UX

