---
title: Asynchronous Methods
order: 200
---

## Overview

Many native mobile operations take time to complete and require user interaction. NativePHP Mobile handles these through asynchronous methods that use Laravel's event system to notify your app when operations complete.

## Understanding Async vs Sync

### Synchronous Methods ⚡
Execute immediately and return results directly.

```php
// These complete instantly
Haptics::vibrate();
System::flashlight();
Dialog::toast('Hello!');
```

### Asynchronous Methods 🔄
Trigger operations that complete later and fire events when done.

```php
// These trigger operations and fire events when complete
Camera::getPhoto();           // → PhotoTaken event
Biometrics::promptForBiometricID(); // → Completed event
PushNotifications::enrollForPushNotifications(); // → TokenGenerated event
```

## Event Handling Pattern

All asynchronous methods follow the same pattern:

1. **Call the method** to trigger the operation
2. **Listen for events** to handle the result
3. **Update your UI** based on the outcome

### Basic Event Structure

```php
use Livewire\Component;
use Livewire\Attributes\On;
use Native\Mobile\Facades\Camera;
use Native\Mobile\Events\Camera\PhotoTaken;

class PhotoComponent extends Component
{
    public bool $isCapturing = false;
    public ?string $photoPath = null;

    // Step 1: Trigger the async operation
    public function takePhoto()
    {
        $this->isCapturing = true;
        Camera::getPhoto();
    }

    // Step 2: Handle the result event
    #[On('native:' . PhotoTaken::class)]
    public function handlePhotoTaken(string $path)
    {
        $this->isCapturing = false;
        $this->photoPath = $path;
    }

    public function render()
    {
        return view('livewire.photo-component');
    }
}
```

## Event Naming Convention

All frontend events use the `native:` prefix to prevent naming collisions:

```php
// Backend event class
Native\Mobile\Events\Camera\PhotoTaken

// Frontend Livewire event (with prefix)
native:Native\Mobile\Events\Camera\PhotoTaken
```

## Common Async Operations

### Camera Operations

```php
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\Gallery\MediaSelected;

class MediaManager extends Component
{
    public function capturePhoto()
    {
        Camera::getPhoto();
    }

    public function selectFromGallery()
    {
        Camera::pickImages('images', true);
    }

    #[On('native:' . PhotoTaken::class)]
    public function handlePhoto(string $path)
    {
        // Handle captured photo
    }

    #[On('native:' . MediaSelected::class)]
    public function handleGallerySelection($success, $files, $count)
    {
        // Handle selected media
    }
}
```

### Biometric Authentication

```php
use Native\Mobile\Events\Biometric\Completed;

class SecureFeature extends Component
{
    public function authenticate()
    {
        Biometrics::promptForBiometricID();
    }

    #[On('native:' . Completed::class)]
    public function handleBiometric(bool $success)
    {
        if ($success) {
            $this->unlockFeature();
        } else {
            $this->showAuthError();
        }
    }
}
```

### Push Notification Registration

```php
use Native\Mobile\Events\PushNotification\TokenGenerated;

class NotificationSetup extends Component
{
    public function enableNotifications()
    {
        PushNotifications::enrollForPushNotifications();
    }

    #[On('native:' . TokenGenerated::class)]
    public function handleToken(string $token)
    {
        // Send token to your backend
        $this->registerToken($token);
    }
}
```

### Location Services

```php
use Native\Mobile\Events\Geolocation\LocationReceived;
use Native\Mobile\Events\Geolocation\PermissionStatusReceived;

class LocationTracker extends Component
{
    public function getCurrentLocation()
    {
        Geolocation::getCurrentPosition(true); // High accuracy
    }

    #[On('native:' . LocationReceived::class)]
    public function handleLocation($success = null, $latitude = null, $longitude = null, $accuracy = null, $timestamp = null, $provider = null, $error = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    #[On('native:' . PermissionStatusReceived::class)]
    public function handlePermissionStatus($location, $coarseLocation, $fineLocation)
    {
        if (!$coarseLocation == 'granted') {
            $this->showLocationPermissionRequest();
        }
    }
}
```

## Loading States

Provide visual feedback during async operations:

```php
class LoadingStatesExample extends Component
{
    public bool $isLoading = false;
    public string $loadingMessage = '';

    public function performAsyncOperation()
    {
        $this->isLoading = true;
        $this->loadingMessage = 'Taking photo...';
        
        Camera::getPhoto();
    }

    #[On('native:' . PhotoTaken::class)]
    public function handleComplete($path)
    {
        $this->isLoading = false;
        $this->loadingMessage = '';
        
        // Process result
    }

    public function render()
    {
        return view('livewire.loading-states-example');
    }
}
```

## Advanced Patterns

### Chaining Async Operations

```php
class ChainedOperations extends Component
{
    public function authenticateAndCapture()
    {
        // Step 1: Authenticate first
        Biometrics::promptForBiometricID();
    }

    #[On('native:' . Completed::class)]
    public function handleAuthComplete(bool $success)
    {
        if ($success) {
            // Step 2: Then capture photo
            Camera::getPhoto();
        }
    }

    #[On('native:' . PhotoTaken::class)]
    public function handlePhotoComplete(string $path)
    {
        // Step 3: Process the authenticated photo
        $this->processSecurePhoto($path);
    }
}
```

### Multiple Event Listeners

```php
class MultiEventComponent extends Component
{
    public function performMultipleOperations()
    {
        Camera::getPhoto();
        PushNotifications::enrollForPushNotifications();
        Geolocation::getCurrentPosition();
    }

    #[On('native:' . PhotoTaken::class)]
    public function handlePhoto(string $path) { /* ... */ }

    #[On('native:' . TokenGenerated::class)]
    public function handlePushToken(string $token) { /* ... */ }

    #[On('native:' . LocationReceived::class)]
    public function handleLocation($success = null, $latitude = null, $longitude = null, $accuracy = null) { /* ... */ }
}
```

## Troubleshooting

### Debugging Async Operations

```php
class DebuggingComponent extends Component
{
    public function startDebugOperation()
    {
        Log::info('Starting async operation');
        $this->isLoading = true;
        
        Camera::getPhoto();
    }

    #[On('native:' . PhotoTaken::class)]
    public function handleResult($path)
    {
        Log::info('Async operation completed', ['result' => $result]);
        $this->isLoading = false;
    }
}
```

Understanding asynchronous methods is crucial for building responsive mobile apps with NativePHP. The event-driven pattern ensures your UI stays responsive while native operations complete in the background.
