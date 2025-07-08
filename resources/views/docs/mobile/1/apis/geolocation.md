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

## Complete Example

```php
use Livewire\Component;
use Livewire\Attributes\On;
use Native\Mobile\Facades\Geolocation;
use Native\Mobile\Events\Geolocation\LocationReceived;
use Native\Mobile\Events\Geolocation\PermissionStatusReceived;
use Native\Mobile\Events\Geolocation\PermissionRequestResult;

class LocationTracker extends Component
{
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $accuracy = null;
    public ?string $provider = null;
    public bool $isLoading = false;
    public string $error = '';
    public bool $showSettingsPrompt = false;
    
    // Permission states
    public string $locationPermission = 'unknown';
    public string $coarsePermission = 'unknown';
    public string $finePermission = 'unknown';

    public function mount()
    {
        // Check current permissions on load
        $this->checkPermissions();
    }

    public function checkPermissions()
    {
        $this->error = '';
        Geolocation::checkPermissions();
    }

    public function requestPermissions()
    {
        $this->error = '';
        $this->isLoading = true;
        Geolocation::requestPermissions();
    }

    public function getCurrentLocation()
    {
        $this->isLoading = true;
        $this->error = '';
        
        // Use high accuracy GPS
        Geolocation::getCurrentPosition(true);
    }

    #[On('native:' . PermissionStatusReceived::class)]
    public function handlePermissionStatus($location, $coarseLocation, $fineLocation)
    {
        $this->locationPermission = $location;
        $this->coarsePermission = $coarseLocation;
        $this->finePermission = $fineLocation;
        
        if ($coarseLocation === 'granted' || $fineLocation === 'granted') {
            // Has some level of location permission
            $this->showLocationButton = true;
        } elseif ($location === 'denied') {
            // Permission denied - can request again
            $this->showRequestButton = true;
        } else {
            // Permission not determined - can request
            $this->showRequestButton = true;
        }
    }

    #[On('native:' . PermissionRequestResult::class)]
    public function handlePermissionRequest($location, $coarseLocation, $fineLocation, $message = null, $needsSettings = null)
    {
        $this->isLoading = false;
        
        if ($location === 'permanently_denied') {
            $this->error = $message ?? 'Location access permanently denied. Please enable location services in your device Settings app.';
            $this->showSettingsPrompt = true;
        } elseif ($coarseLocation === 'granted' || $fineLocation === 'granted') {
            // Permission granted - automatically get location
            $this->getCurrentLocation();
        } else {
            $this->error = 'Location permission is required to use this feature.';
        }
    }

    #[On('native:' . LocationReceived::class)]
    public function handleLocationReceived($success = null, $latitude = null, $longitude = null, $accuracy = null, $timestamp = null, $provider = null, $error = null)
    {
        $this->isLoading = false;
        
        if ($success) {
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            $this->accuracy = $accuracy;
            $this->provider = $provider;
            $this->error = '';
            
            // Store for later use
            session([
                'last_location' => [
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'accuracy' => $accuracy,
                    'timestamp' => $timestamp,
                    'provider' => $provider
                ]
            ]);
            
            Log::info('Location updated', [
                'lat' => $latitude,
                'lng' => $longitude,
                'accuracy' => $accuracy
            ]);
            
        } else {
            $this->error = $error ?? 'Failed to get current location';
            Log::warning('Location request failed', ['error' => $error]);
        }
    }

    public function openSettings()
    {
        // You might want to show instructions or deep link to settings
        $this->dispatch('show-settings-instructions');
    }

    public function render()
    {
        return view('livewire.location-tracker');
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

### Permission Flow

```php
class PermissionFlowExample extends Component
{
    public function handleLocationFlow()
    {
        // 1. Check current permissions
        Geolocation::checkPermissions();
    }
    
    #[On('native:' . PermissionStatusReceived::class)]
    public function handleCheck($location, $coarseLocation, $fineLocation)
    {
        if ($coarseLocation === 'granted' || $fineLocation === 'granted') {
            // 2a. Permission already granted - get location
            Geolocation::getCurrentPosition(true);
        } else {
            // 2b. Need to request permission
            Geolocation::requestPermissions();
        }
    }
    
    #[On('native:' . PermissionRequestResult::class)]
    public function handleRequest($location, $coarseLocation, $fineLocation, $message = null, $needsSettings = null)
    {
        if ($location === 'permanently_denied') {
            // 3a. User must enable in Settings
            $this->showSettingsInstructions($message);
        } elseif ($coarseLocation === 'granted' || $fineLocation === 'granted') {
            // 3b. Permission granted - get location
            Geolocation::getCurrentPosition(true);
        } else {
            // 3c. Permission denied - show explanation
            $this->showPermissionExplanation();
        }
    }
}
```

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

### User Experience Tips

```php
class LocationUX extends Component
{
    public function requestLocationWithExplanation()
    {
        // Show explanation first
        $this->showExplanation = true;
    }
    
    public function proceedWithLocationRequest()
    {
        $this->showExplanation = false;
        
        // Now request permission
        Geolocation::requestPermissions();
    }
    
    public function handleDeniedGracefully($location, $coarseLocation, $fineLocation)
    {
        if ($location === 'permanently_denied') {
            // Offer manual location entry
            $this->showManualLocationEntry = true;
        } else {
            // Show benefit of enabling location
            $this->showLocationBenefits = true;
        }
    }
}
```

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
