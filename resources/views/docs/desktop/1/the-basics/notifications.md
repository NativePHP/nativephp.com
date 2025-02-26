---
title: Notifications
order: 500
---

## Native Notifications

NativePHP allows you to send system notifications using an elegant PHP API. These notifications are, unlike Laravel's built-in notifications, actual UI notifications displayed by your operating system.

When used sparingly, notifications can be a great way to inform the user about events that are occurring in your application and to bring their attention back to it, especially if further input from them is required.

Notifications are sent using the `Notification` facade.

```php
use Native\Laravel\Facades\Notification;
```

### Sending Notifications

You may send a notification using the `Notification` facade.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->show();
```

This will show a system-wide notification to the user with the given title and message.

### Handling clicks on notifications

You may register a custom event along with your NativePHP notification. 
This event will be fired when a user clicks on the notification, so that you may add some custom logic within your application in this scenario.

To attach an event to your notification, you may use the `event` method. The argument passed to this method is the class name of the event that should get dispatched upon clicking on the notification.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->event(\App\Events\MyNotificationEvent::class)
    ->show();
```

### Notification References

To keep track of different notifications, you may use the notification's `$reference` property.

By default, a unique reference is generated for you, but you may manually set a reference by [chaining the `reference()`](#notification-reference) method when creating
the notification.

## Configuring Notifications

### Notification Title

You may set the notification's title using the `title()` method.

```php
Notification::title('Hello from NativePHP')
    ->show();
```

### Notification Reference

You can access the `$reference` property of a notification after it has been created:

```
$notification = Notification::title('Hello from NativePHP')->show();

$notification->reference;
```

You may chain the `reference()` method to set a custom reference when creating a notification:

```php
Notification::title('Hello from NativePHP')
    ->reference(Str::uuid())
    ->show();
```

The reference will be sent along with any event triggered by the notification and can be used to track which specific notification was clicked:

```php
use App\Events\PostNotificationClicked;
use App\Models\Post;

Post::recentlyCreated()
    ->get()
    ->each(function(Post $post) {
        Notification::title('New post: ' . $post->title)
            ->reference($post->id)
            ->event(PostNotificationClicked::class)
            ->show();
    });

Event::listen(PostNotificationClicked::class, function (PostNotificationClicked $event) {
    $post = Post::findOrFail($event->reference);

    Window::open()->url($post->url);
});
```

### Notification Message

You may set the notification's message using the `message()` method.

```php
Notification::title('Hello from NativePHP')
    ->message('This is a detail message coming from your Laravel app.')
    ->show();
```

### Notification Reply

On macOS, you can allow the user to reply to a notification using the `hasReply()` method. 

```php
Notification::title('Hello from NativePHP')
    ->hasReply()
    ->show();
```

The `hasReply()` method accepts a placeholder reply message as an argument.

```php
Notification::title('Hello from NativePHP')
    ->hasReply('This is a placeholder')
    ->show();
```

### Notification Actions

On macOS, you can add action buttons to a notification using the `addAction()` method. 

```php
Notification::title('Hello from NativePHP')
    ->addAction('Click here')
    ->show();
```

You can call the `addAction()` method multiple times if you need to add multiple buttons.

```php
Notification::title('Hello from NativePHP')
    ->addAction('Button One')
    ->addAction('Button Two')
    ->show();
```

When an action button is clicked, it will trigger the [`NotificationActionClicked`](#codenotificationactionclickedcode) event.

This event contains an `$index` property, which refers to the index of the action button that was clicked. Action button indexes start at `0`:

```php
use Native\Laravel\Events\Notifications\NotificationActionClicked;

Notification::title('Do you accept?')
    ->addAction('Accept')  // This action will be $index = 0
    ->addAction('Decline') // This action will be $index = 1
    ->show();

Event::listen(NotificationActionClicked::class, function (NotificationActionClicked $event) {
    if ($event->index === 0) {
        // 'Accept' clicked
    } elseif ($event->index === 1) {
        // 'Decline' clicked
    }
});
```

## Events

### `NotificationClicked`
The `Native\Laravel\Events\Notifications\NotificationClicked` event is dispatched when a user clicks on a notification.

### `NotificationClosed`
The `Native\Laravel\Events\Notifications\NotificationClosed` event is dispatched when a user closes a notification.

### `NotificationReply`
The `Native\Laravel\Events\Notifications\NotificationReply` event is dispatched when a user replies to a notification.

### `NotificationActionClicked`
The `Native\Laravel\Events\Notifications\NotificationActionClicked` event is dispatched when a user clicks an action button on a notification.
