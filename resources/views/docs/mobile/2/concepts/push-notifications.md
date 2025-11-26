---
title: Push Notifications
order: 400
---

## Overview

NativePHP for Mobile uses Firebase Cloud Messaging (FCM) to send push notifications to your users on both iOS and
Android devices.

To send a push notification to a user, your app must request a token. That token must then be stored securely (ideally
on a server application via a secure API) and associated with that user/device.

Requesting push notification will trigger an alert for the user to either approve or deny your request. If they approve,
your app will receive the token.

When you want to send a notification to that user, you pass this token along with a request to the FCM service and
Firebase handles sending the message to the right device.

<aside>

FCM automatically routes notifications through Apple's Push Notification Service (APNS) behind the scenes - you don't
need to configure APNS separately. You only need to configure Firebase with access to your APNS account.

</aside>

## Firebase

1. Create a [Firebase](https://firebase.google.com/) account
2. Create a project
3. Download the `google-services.json` file (for Android) and `GoogleService-Info.plist` file (for iOS)
4. These files contain the configuration for your app and is used by the Firebase SDK to retrieve tokens for each device

Place these files in the root of your application and NativePHP will automatically handle setting them up appropriately
for each platform.

You can ignore Firebase's further setup instructions as this is already taken care of by NativePHP.

### Service account

For sending push notifications from your server-side application, you'll also need a Firebase service account:

1. Go to your Firebase Console → Project Settings → Service Accounts
2. Click "Generate New Private Key" to download the service account JSON file
3. Save this file as `fcm-service-account.json` somewhere safe in your server application

## Getting push tokens

It's common practice to request push notification permissions during app bootup as tokens can change when:
- The app is restored on a new device
- The app data is restored from backup
- The app is updated
- Other internal FCM operations

To request a token, use the `PushNotifications::getToken()` method:

```php
use Native\Mobile\Facades\PushNotifications;

PushNotifications::getToken();
```

If the user has approved your app to use push notifications and the request to FCM succeeded, a `TokenGenerated` event
will fire.

Listen for this event to receive the token. Here's an example in a Livewire component:

```php
use App\Services\APIService;
use Livewire\Attributes\On;
use Native\Mobile\Facades\PushNotifications;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class PushNotifications extends Component
{
    #[OnNative(TokenGenerated::class)]
    public function storePushToken(APIService $api, string $token)
    {
        $api->storePushToken($token);
    }
}
```

## Sending push notifications

Once you have a token, you may use it from your server-side applications to trigger Push Notifications directly to your
user's device.

<aside>

The server-side implementation is out of scope for this documentation.

</aside>
