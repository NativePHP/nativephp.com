---
title: Push Notifications - Firebase
order: 400
---


## Overview

NativePHP for Mobile uses [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging) to send push notifications to your users.

## Setting up your app

Once you create a Firebase account and create a project you will be offered to download a `google-services.json` file.

This file contains the configuration for your app and is used by the Firebase SDK to retrieve tokens for each device using your app.

Simply drag this file into the root of your Laravel project, enable `push_notifications` in the [config](/docs/mobile/1/getting-started/configuration) it will be used automatically.

You will see more instructions on how to configure your app in the Firebase documentation, you can ignore all of those, NativePHP handles all of that for you.

## Receiving Push Tokens

To receive push notifications, you must register a listener for the event. For example,
take a look at how easy it is to listen for a `TokenGenerated` event in Livewire:

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

Because of the nature of mobile applications you need an api server to handle these tokens. You can use Laravel's built-in `Http` facade to 
`POST` the token to your server, on the server side you need to associate the token with the "user" that owns the device.

We **strongly** recommend using [Sanctum](https://laravel.com/docs/12.x/sanctum#main-content) to handle this for you.

## The flow

Your app authenticates users against your own api server, when users create an account or login the server validates and authenticates the user and passes back a Sanctum token.

The token is stored in your apps `session` and is used on subsequent requests to the api server.

When a push notification is received, the token is sent to your api server and the server stores it for the user who sent it.

> Optionally, you can have a `HasMany` relationship between your users and devices, 
> this allows you to associate a device with a user and then use the device's token 
> to send push notifications to that users devices.

## Sending Push Notifications

Once you have the token, you may use it from your server-based applications to trigger Push Notifications directly to
your user's device. We use a package like [google/apiclient](https://github.com/googleapis/google-api-php-client) to send the notifications.

This is the exact code used by the NativePHP Kitchen Sink App (available soon on all app stores and GitHub):

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