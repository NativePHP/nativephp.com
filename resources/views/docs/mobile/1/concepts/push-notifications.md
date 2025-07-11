---
title: Push Notifications
order: 400
---

## Overview

NativePHP for Mobile uses Firebase Cloud Messaging (FCM) to send push notifications to your users on both iOS and Android devices.

**Cross-Platform Support**: FCM is the unified push notification service for both platforms. For iOS devices, FCM automatically routes notifications through Apple Push Notification Service (APNS) behind the scenes - you don't need to configure APNS separately.

**Supported Services**: NativePHP only supports FCM. Other push notification services are not supported.

## Setting up your app

### Firebase Project Setup

1. Create a Firebase account and create a project
2. You will be offered to download a `google-services.json` file for your app configuration
3. This file contains the configuration for your app and is used by the Firebase SDK to retrieve tokens for each device

### Service Account Setup

For server-side notifications, you'll also need a Firebase service account:

1. Go to your Firebase Console → Project Settings → Service Accounts
2. Click "Generate New Private Key" to download the service account JSON file
3. Save this file as `fcm-service-account.json` in your Laravel project's `public` directory

### NativePHP Configuration

Simply drag the `google-services.json` file into the root of your Laravel project, enable `push_notifications` in the config and it will be used automatically.

You will see more instructions on how to configure your app in the Firebase documentation, you can ignore all of those, NativePHP handles all of that for you.

## Receiving Push Tokens

### Token Management

FCM tokens are unique identifiers for each app installation. These tokens can change when:
- The app is restored on a new device
- The app data is restored from backup
- The app is updated on Android
- Other internal FCM operations

You should store both the FCM token and platform information for each user device.

### Listening for Tokens

To receive push notifications, you must register a listener for the event. For example, take a look at how easy it is to listen for a `TokenGenerated` event in Livewire:

```php
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class PushNotifications extends Component
{
    public function render()
    {
        return view('livewire.system.push-notifications');
    }

    #[On('native:' . TokenGenerated::class)]
    public function handlePushNotifications(string $token)
    {
        // Do something with the token...
    }
}
```

Because of the nature of mobile applications you need an api server to handle these tokens. You can use Laravel's built-in `Http` facade to `POST` the token to your server, on the server side you need to associate the token with the "user" that owns the device.

We **strongly** recommend using [Sanctum](https://laravel.com/docs/sanctum) to handle this for you.

## The flow

Your app authenticates users against your own api server, when users create an account or login the server validates and authenticates the user and passes back a Sanctum token.

The token is stored in your apps `session` and is used on subsequent requests to the api server.

When a push notification is received, the token is sent to your api server and the server stores it for the user who sent it.

> Optionally, you can have a `HasMany` relationship between your users and devices, this allows you to associate a device with a user and then use the device's token to send push notifications to that users devices.

## Sending Push Notifications

Once you have the token, you may use it from your server-based applications to trigger Push Notifications directly to your user's device. We use the `google/apiclient` package to send the notifications.

```bash
composer require google/apiclient
```

This is the exact code used by the NativePHP Kitchen Sink App:

```php
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('send-push-notification', PushNotificationController::class)->name('send-push-notification');
});
```

```php
namespace App\Http\Controllers;

use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = $request->get('token');

        $request->user()->update([
            'push_token' => $token
        ]);

        SendPushNotification::dispatch($token)->delay(now()->addMinutes(1));
    }
}
```

## SendPushNotification Job

Create a job file at `app/Jobs/SendPushNotification.php` with the following structure:

**Key Components:**
- Uses `Google_Client` to authenticate with Firebase
- Retrieves access token from service account JSON file
- Sends HTTP POST request to FCM API endpoint
- Includes notification title and body in the message payload

**Required Setup:**
1. Place your `fcm-service-account.json` file in the `public` directory
2. Configure `services.fcm.project_id` in your config
3. Install the `google/apiclient` package

The job handles token authentication and sends notifications to specific device tokens using Firebase Cloud Messaging.

**Core Implementation:**

```php
private function sendFcmPush(string $token): void
{
    $fcmToken = $token;

    $client = new Google_Client();
    $client->setAuthConfig(public_path('fcm-service-account.json'));
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $token = $client->fetchAccessTokenWithAssertion()['access_token'];

    $projectId = config('services.fcm.project_id');

    Http::withToken($token)->post(
        "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
        [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => 'NativePHP',
                    'body' => 'Thanks for testing NativePHP!',
                ],
            ],
        ]
    );
}
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```bash
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_CLIENT_EMAIL=your-service-account-email
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
```

### Config File

Add FCM configuration to your `config/services.php`:

```php
'fcm' => [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
    'private_key' => env('FIREBASE_PRIVATE_KEY'),
],
```

## Platform-Specific Options

### Android-Specific Configuration

You can customize Android notifications with additional options:

```php
'message' => [
    'token' => $fcmToken,
    'notification' => [
        'title' => 'NativePHP',
        'body' => 'Thanks for testing NativePHP!',
    ],
    'android' => [
        'notification' => [
            'channel_id' => 'default',
            'priority' => 'high',
            'sound' => 'default',
        ],
    ],
],
```

### iOS-Specific Configuration

For iOS devices, you can add APNS-specific options:

```php
'message' => [
    'token' => $fcmToken,
    'notification' => [
        'title' => 'NativePHP',
        'body' => 'Thanks for testing NativePHP!',
    ],
    'apns' => [
        'payload' => [
            'aps' => [
                'badge' => 1,
                'sound' => 'default',
                'content-available' => 1,
            ],
        ],
    ],
],

## Data Payloads

You can send custom data with your notifications by adding a `data` field to your message payload:

```php
'message' => [
    'token' => $fcmToken,
    'notification' => [
        'title' => 'New Message',
        'body' => 'You have a new message from John',
    ],
    'data' => [
        'user_id' => '123',
        'message_id' => '456',
        'action' => 'view_message',
        'deep_link' => 'myapp://messages/456',
    ],
],
```

### Handling Data in Your App

The data payload is available in your app when the notification is received:

```php
// In your notification event handler
#[On('native:' . PushNotificationReceived::class)]
public function handleNotificationData(array $data)
{
    $userId = $data['user_id'] ?? null;
    $messageId = $data['message_id'] ?? null;
    $action = $data['action'] ?? null;
    
    // Handle the data accordingly
    if ($action === 'view_message') {
        // Navigate to message view
        $this->redirectRoute('messages.show', ['id' => $messageId]);
    }
}
```
```