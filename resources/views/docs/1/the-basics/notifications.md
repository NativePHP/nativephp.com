---
title: Notifications
order: 500
---

## Native Notifications

NativePHP allows you to send system notifications using an elegant PHP API. These notifications are, unlike Laravel's built-in notifications, actual UI notifications displayed by your operating system.

When used sparingly, notifications can be a great way to inform the user about events that are occurring in your application and to bring their attention back to it, especially if further input from them is required.

### Sending Notifications

You may send a notification using the `Notification` class and a `Client` object.

```php
use Native\Laravel\Notification;
use Native\Laravel\Client\Client;

$client = new Client;
$notification = new Notification($client)

$notifcation->title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->show();
```

This will show a system-wide notification to the user with the given title and message.

## Events

### Notification Click Events

You may register a custom event along with your NativePHP notification. 
This event will be fired when a user clicks on the notification, so that you may add some custom logic within your application in this scenario.

To attach an event to your notification, you may use the `event` method. The argument passed to this method is the class name of the event that should get dispatched upon clicking on the notification.

```php
use Native\Laravel\Notification;
use Native\Laravel\Client\Client;

$client = new Client;
$notification = new Notification($client)

$notification->title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->event(\App\Events\MyNotificationEvent::class)
    ->show();
```
