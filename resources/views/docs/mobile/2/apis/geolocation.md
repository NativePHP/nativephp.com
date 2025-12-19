---
title: Geolocation
order: 700
---

## Overview

The Geolocation API provides access to the device's GPS and location services to determine the user's current position.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Geolocation;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { geolocation, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `getCurrentPosition()`

Gets the current GPS location of the device.

**Parameters:**
- `bool $fineAccuracy` - Whether to use high accuracy mode (GPS vs network) (default: `false`)

**Returns:** Location data via events

<x-snippet title="Get Current Position">

<x-snippet.tab name="PHP">

```php
// Get location using network positioning (faster, less accurate)
Geolocation::getCurrentPosition();

// Get location using GPS (slower, more accurate)
Geolocation::getCurrentPosition(true);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Get location using network positioning (faster, less accurate)
await geolocation.getCurrentPosition();

// Get location using GPS (slower, more accurate)
await geolocation.getCurrentPosition()
    .fineAccuracy(true);

// With identifier for tracking
await geolocation.getCurrentPosition()
    .fineAccuracy(true)
    .id('current-loc');
```

</x-snippet.tab>
</x-snippet>

### `checkPermissions()`

Checks the current location permissions status.

**Returns:** Permission status via events

<x-snippet title="Check Permissions">

<x-snippet.tab name="PHP">

```php
Geolocation::checkPermissions();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await geolocation.checkPermissions();
```

</x-snippet.tab>
</x-snippet>

### `requestPermissions()`

Requests location permissions from the user.

**Returns:** Permission status after request via events

<x-snippet title="Request Permissions">

<x-snippet.tab name="PHP">

```php
Geolocation::requestPermissions();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await geolocation.requestPermissions();

// With remember flag
await geolocation.requestPermissions()
    .remember();
```

</x-snippet.tab>
</x-snippet>

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

<x-snippet title="LocationReceived Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Geolocation\LocationReceived;

#[OnNative(LocationReceived::class)]
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const location = ref({ latitude: null, longitude: null });
const error = ref('');

const handleLocationReceived = (payload) => {
    if (payload.success) {
        location.value = {
            latitude: payload.latitude,
            longitude: payload.longitude
        };
    } else {
        error.value = payload.error;
    }
};

onMounted(() => {
    on(Events.Geolocation.LocationReceived, handleLocationReceived);
});

onUnmounted(() => {
    off(Events.Geolocation.LocationReceived, handleLocationReceived);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [location, setLocation] = useState({ latitude: null, longitude: null });
const [error, setError] = useState('');

const handleLocationReceived = (payload) => {
    if (payload.success) {
        setLocation({
            latitude: payload.latitude,
            longitude: payload.longitude
        });
    } else {
        setError(payload.error);
    }
};

useEffect(() => {
    on(Events.Geolocation.LocationReceived, handleLocationReceived);

    return () => {
        off(Events.Geolocation.LocationReceived, handleLocationReceived);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

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

<x-snippet title="PermissionStatusReceived Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Geolocation\PermissionStatusReceived;

#[OnNative(PermissionStatusReceived::class)]
public function handlePermissionStatus($location, $coarseLocation, $fineLocation)
{
    // ...
}
```

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const permissionStatus = ref('');

const handlePermissionStatus = (payload) => {
    const { location } = payload;
    permissionStatus.value = location;
};

onMounted(() => {
    on(Events.Geolocation.PermissionStatusReceived, handlePermissionStatus);
});

onUnmounted(() => {
    off(Events.Geolocation.PermissionStatusReceived, handlePermissionStatus);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [permissionStatus, setPermissionStatus] = useState('');

const handlePermissionStatus = (payload) => {
    const { location } = payload;
    setPermissionStatus(location);
};

useEffect(() => {
    on(Events.Geolocation.PermissionStatusReceived, handlePermissionStatus);

    return () => {
        off(Events.Geolocation.PermissionStatusReceived, handlePermissionStatus);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

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

<x-snippet title="PermissionRequestResult Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const error = ref('');

const handlePermissionRequest = (payload) => {
    const { location, coarseLocation, fineLocation } = payload;

    if (location === 'permanently_denied') {
        error.value = 'Please enable location in Settings.';
    } else if (coarseLocation === 'granted' || fineLocation === 'granted') {
        getCurrentLocation();
    } else {
        error.value = 'Location permission is required.';
    }
};

onMounted(() => {
    on(Events.Geolocation.PermissionRequestResult, handlePermissionRequest);
});

onUnmounted(() => {
    off(Events.Geolocation.PermissionRequestResult, handlePermissionRequest);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [error, setError] = useState('');

const handlePermissionRequest = (payload) => {
    const { location, coarseLocation, fineLocation } = payload;

    if (location === 'permanently_denied') {
        setError('Please enable location in Settings.');
    } else if (coarseLocation === 'granted' || fineLocation === 'granted') {
        getCurrentLocation();
    } else {
        setError('Location permission is required.');
    }
};

useEffect(() => {
    on(Events.Geolocation.PermissionRequestResult, handlePermissionRequest);

    return () => {
        off(Events.Geolocation.PermissionRequestResult, handlePermissionRequest);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>

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

