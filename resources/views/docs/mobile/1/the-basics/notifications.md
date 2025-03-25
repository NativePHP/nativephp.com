---
title: Notifications
order: 500
---

## Notifications

NativePHP allows you to send system notifications using an elegant PHP API. These notifications are, unlike Laravel's
built-in notifications, actual device notifications displayed by the device's native notification UI.

When used sparingly, notifications can be a great way to inform the user about events that are occurring in your
application and to bring their attention back to it, especially if further input from them is required.

Notifications are sent using the `Notification` facade.

```php
use Native\Ios\Facades\Notification;
```

### Sending Notifications

**COMING SOON**

You may send a notification using the `Notification` facade.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->send();
```
