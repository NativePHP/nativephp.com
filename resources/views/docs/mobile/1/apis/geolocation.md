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

#[On('native:' . LocationReceived::class)]
public function handleLocationReceived($success = null, $latitude = null, $longitude = null, $accuracy = null, $timestamp = null, $provider = null, $error = null)
{
    if ($success) {
        // Location successfully retrieved
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->accuracy = $accuracy;
        $this->provider = $provider;
        
        Log::info('Location received', [
            'lat' => $latitude,
            'lng' => $longitude,
            'accuracy' => $accuracy,
            'provider' => $provider
        ]);
    } else {
        // Location request failed
        $this->error = $error ?? 'Failed to get location';
        Log::warning('Location request failed', ['error' => $error]);
    }
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

#[On('native:' . PermissionStatusReceived::class)]
public function handlePermissionStatus($location, $coarseLocation, $fineLocation)
{
    $this->locationPermission = $location;
    $this->coarsePermission = $coarseLocation;
    $this->finePermission = $fineLocation;
    
    if ($coarseLocation === 'granted' || $fineLocation === 'granted') {
        // At least some location permission is granted
        $this->canRequestLocation = true;
    } else {
        // No location permissions granted
        $this->showPermissionExplanation();
    }
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
        // Permission permanently denied - must go to Settings
        $this->error = $message ?? 'Location permission permanently denied. Please enable in Settings.';
        $this->showSettingsPrompt = true;
    } elseif ($coarseLocation === 'granted' || $fineLocation === 'granted') {
        // Permission granted - can now request location
        $this->permissionGranted = true;
        $this->getCurrentLocation();
    } else {
        // Permission denied but can ask again
        $this->error = 'Location permission is required for this feature.';
    }
}
```

## Understanding Permission States

### Permission Types

**Coarse Location (`ACCESS_COARSE_LOCATION` on Android)**
- Network-based location (WiFi, cellular towers)
- Lower accuracy (~100-1000 meters)
- Less battery usage
- Faster location fixes

**Fine Location (`ACCESS_FINE_LOCATION` on Android, Location Services on iOS)**
- GPS-based location
- Higher accuracy (~5-50 meters)  
- More battery usage
- Slower initial location fix


## Platform Support

### iOS
- Uses Core Location framework
- Requires location usage description in Info.plist
- Supports both "When in Use" and "Always" permissions
- Automatic permission prompts

### Android  
- Uses FusedLocationProviderClient (Google Play Services)
- Requires location permissions in AndroidManifest.xml
- Supports coarse and fine location permissions
- Runtime permission requests (Android 6+)

## Privacy Considerations

### Best Practices
- **Explain why** you need location access before requesting
- **Request at the right time** - when the feature is actually needed
- **Respect denials** - provide alternative functionality when possible
- **Use appropriate accuracy** - don't request fine location if coarse is sufficient
- **Limit frequency** - don't request location updates constantly

## Accuracy and Performance

### Choosing Accuracy Level

```php
// For general location (city-level)
Geolocation::getCurrentPosition(false); // ~100-1000m accuracy

// For precise location (navigation, delivery)
Geolocation::getCurrentPosition(true);  // ~5-50m accuracy
```

### Performance Considerations
- **Battery Usage** - GPS uses more battery than network location
- **Time to Fix** - GPS takes longer for initial position
- **Indoor Accuracy** - GPS may not work well indoors
- **Caching** - Consider caching recent locations for better UX

### Error Handling
- Always handle both success and failure cases
- Provide meaningful error messages to users
- Implement fallback strategies (manual entry, saved locations)
- Log errors for debugging but don't expose sensitive details

The Geolocation API provides powerful location capabilities while respecting user privacy and platform requirements. Always handle permissions gracefully and provide clear value propositions for why location access is needed.
