---
title: PushNotifications
order: 600
---

## Overview

The PushNotifications API handles device registration for Firebase Cloud Messaging to receive push notifications.

```php
use Native\Mobile\Facades\PushNotifications;
```

## Methods

### `enrollForPushNotifications()`

Requests permission and enrolls the device for push notifications.

**Returns:** `void`

```php
PushNotifications::enrollForPushNotifications();
```

### `getPushNotificationsToken()`

Retrieves the current FCM token for this device.

**Returns:** `string|null` - The FCM token, or `null` if not available

```php
$token = PushNotifications::getPushNotificationsToken();

if ($token) {
    // Send token to your server
    $this->registerTokenWithServer($token);
} else {
    // Token not available, enrollment may have failed
}
```

## Events

#### `Native\Mobile\Events\PushNotification\TokenGenerated`

Fired when a push notification token is successfully generated.

**Payload:** `string $token` - The FCM token for this device

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\PushNotification\TokenGenerated;

#[On('native:' . TokenGenerated::class)]
public function handlePushToken(string $token)
{
    // Send token to your backend
    $this->sendTokenToServer($token);
}
```

## Example Usage

```php
use Livewire\Component;
use Livewire\Attributes\On;
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class NotificationManager extends Component
{
    public bool $isRegistered = false;
    public bool $isRegistering = false;
    public string $error = '';

    public function mount()
    {
        // Check if already registered
        $this->checkExistingRegistration();
    }

    public function enableNotifications()
    {
        $this->isRegistering = true;
        $this->error = '';
        
        // Request permission and get token
        PushNotifications::enrollForPushNotifications();
    }

    #[On('native:' . TokenGenerated::class)]
    public function handleTokenGenerated(string $token)
    {
        $this->isRegistering = false;
        
        try {
            // Send token to your backend API
            $response = Http::withToken(session('api_token'))
                ->post('/api/push-tokens', [
                    'token' => $token,
                    'device_id' => $this->getDeviceId(),
                    'platform' => $this->getPlatform(),
                    'user_id' => auth()->id()
                ]);

            if ($response->successful()) {
                $this->isRegistered = true;
                session(['push_token' => $token]);
                
                Log::info('Push notification token registered', [
                    'user_id' => auth()->id(),
                    'token_preview' => substr($token, 0, 10) . '...'
                ]);
            } else {
                throw new Exception('Server rejected token registration');
            }

        } catch (Exception $e) {
            $this->error = 'Failed to register for notifications: ' . $e->getMessage();
            
            Log::error('Push token registration failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
        }
    }

    public function disableNotifications()
    {
        $token = session('push_token');
        
        if ($token) {
            try {
                // Remove token from server
                Http::withToken(session('api_token'))
                    ->delete("/api/push-tokens/{$token}");

                session()->forget('push_token');
                $this->isRegistered = false;

            } catch (Exception $e) {
                $this->error = 'Failed to disable notifications';
            }
        }
    }

    private function checkExistingRegistration()
    {
        $existingToken = session('push_token');
        
        if ($existingToken) {
            // Verify token is still valid
            $currentToken = PushNotifications::getPushNotificationsToken();
            
            if ($currentToken === $existingToken) {
                $this->isRegistered = true;
            } else {
                // Token changed, need to re-register
                session()->forget('push_token');
                $this->isRegistered = false;
            }
        }
    }

    private function getDeviceId(): string
    {
        if (!session()->has('device_id')) {
            session(['device_id' => Str::uuid()]);
        }
        
        return session('device_id');
    }

    private function getPlatform(): string
    {
        // Detect platform from user agent or environment
        return request()->header('X-Platform', 'unknown');
    }

    public function render()
    {
        return view('livewire.notification-manager');
    }
}
```

## Backend Integration

### Database Schema

```php
// Migration for storing push tokens
Schema::create('push_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('token')->unique();
    $table->string('device_id')->nullable();
    $table->enum('platform', ['ios', 'android', 'unknown']);
    $table->timestamp('last_used_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'platform']);
});
```

### API Controller

```php
namespace App\Http\Controllers\Api;

use App\Models\PushToken;
use Illuminate\Http\Request;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:255',
            'device_id' => 'nullable|string|max:255',
            'platform' => 'required|in:ios,android,unknown'
        ]);

        PushToken::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'device_id' => $validated['device_id']
            ],
            [
                'token' => $validated['token'],
                'platform' => $validated['platform'],
                'last_used_at' => now()
            ]
        );

        return response()->json(['message' => 'Token registered successfully']);
    }

    public function destroy(Request $request, string $token)
    {
        PushToken::where('user_id', $request->user()->id)
            ->where('token', $token)
            ->delete();

        return response()->json(['message' => 'Token removed successfully']);
    }
}
```

### Sending Notifications

```php
namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\FirebaseCloudMessaging;

class PushNotificationService
{
    public function sendToUser(int $userId, array $notification, array $data = [])
    {
        $tokens = PushToken::where('user_id', $userId)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            throw new Exception('No push tokens found for user');
        }

        return $this->sendToTokens($tokens, $notification, $data);
    }

    private function sendToTokens(array $tokens, array $notification, array $data = [])
    {
        $client = new GoogleClient();
        $client->setAuthConfig(base_path('google-services.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
        $fcm = new FirebaseCloudMessaging($client);
        $projectId = config('services.firebase.project_id');
        
        $results = [];
        
        foreach ($tokens as $token) {
            try {
                $message = [
                    'token' => $token,
                    'notification' => $notification,
                    'data' => array_map('strval', $data)
                ];

                $response = $fcm->projects_messages->send($projectId, [
                    'message' => $message
                ]);

                $results[] = [
                    'token' => substr($token, 0, 10) . '...',
                    'success' => true,
                    'message_id' => $response->getName()
                ];

            } catch (Exception $e) {
                $results[] = [
                    'token' => substr($token, 0, 10) . '...',
                    'success' => false,
                    'error' => $e->getMessage()
                ];

                // Remove invalid tokens
                if (str_contains($e->getMessage(), 'registration-token-not-registered')) {
                    PushToken::where('token', $token)->delete();
                }
            }
        }
        
        return $results;
    }
}
```

## Configuration Requirements

### Firebase Setup

1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com/)
2. Add your mobile app to the project
3. Download `google-services.json` (Android) and `GoogleService-Info.plist` (iOS)
4. Place these files in your Laravel project root
5. Enable push notifications in your NativePHP config:

```php
// config/nativephp.php
return [
    'permissions' => [
        'push_notifications' => true,
    ],
];
```

### Environment Variables

```bash
NATIVEPHP_APP_ID=com.yourcompany.yourapp
FIREBASE_PROJECT_ID=your-firebase-project-id
```

## Permission Flow

1. User taps "Enable Notifications"
2. App calls `enrollForPushNotifications()`
3. System shows permission dialog
4. If granted, FCM generates token
5. `TokenGenerated` event fires with token
6. App sends token to backend
7. Backend stores token for user
8. Server can now send notifications to this device

## Error Handling

```php
public function handleRegistrationFailure()
{
    // Common failure scenarios:
    
    // 1. User denied permission
    if (!$this->hasNotificationPermission()) {
        $this->showPermissionExplanation();
        return;
    }
    
    // 2. Network error
    if (!$this->hasNetworkConnection()) {
        $this->showNetworkError();
        return;
    }
    
    // 3. Firebase configuration missing
    if (!$this->hasFirebaseConfig()) {
        Log::error('Firebase configuration missing');
        return;
    }
    
    // 4. Backend API error
    $this->showGenericError();
}
```

## Migration from System Facade

```php
// Old way (deprecated)
use Native\Mobile\Facades\System;
System::enrollForPushNotifications();
$token = System::getPushNotificationsToken();

// New way (recommended)
use Native\Mobile\Facades\PushNotifications;
PushNotifications::enrollForPushNotifications();
$token = PushNotifications::getPushNotificationsToken();
```

## Best Practices

- Request permission at the right time (not immediately on app launch)
- Explain the value of notifications to users
- Handle permission denial gracefully
- Clean up invalid tokens on your backend
- Implement retry logic for network failures
- Log registration events for debugging
- Respect user preferences and provide opt-out
