---
title: System
order: 800
---

## Native System

NativePHP allows you to trigger many native system functions.

System functions are called using the `System` facade.

```php
use Native\Mobile\Facades\System;
```
---

# Synchronous Functions
---

### Vibration

You may vibrate the user's device by calling the `vibrate` method:

```php
System::vibrate()
```
---
### Flashlight

You may toggle the device flashlight (on/off) by calling the `flashlight` method:

```php
System::flashlight()
```
---

# Asynchronous Functions
---

### Camera 
```php
Front End Event: `native:Native\Mobile\Events\Camera\PhotoTaken`
Back End Event: `Native\Mobile\Events\Camera\PhotoTaken`
```

You may request the native camera interface to take a photograph by calling the `System::camera()` method:

When the user takes a photograph the event is fired with a payload array that contains one item: `path` 
which is a string containing the path to the photo.

```php
use Native\Mobile\Events\Camera\PhotoTaken;

System::camera();

// Later...
#[On('native:' . PhotoTaken::class)]
public function handlePhotoTaken($path)
{
    $data   = base64_encode(file_get_contents($path));
    $mime   = mime_content_type($path);

    $this->photoDataUrl = "data:$mime;base64,$data";
}
```

**Note: The first time your application asks to use the camera, the user will be prompted to grant permission. If they
decline, triggering the camera API will silently fail.**

---

### Push Notifications
```php
Front End Event: `native:Native\Mobile\Events\PushNotification\TokenGenerated`
Back End Event: `Native\Mobile\Events\PushNotification\TokenGenerated`
```
Currently, NativePHP uses [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging) to send push notifications to your users.

Simply use the `enrollForPushNotifications` method to trigger enrolment. If this is the first time that your app tries
to enrol this device for push notifications, the user will be presented with a native alert, allowing them to opt-in.

Then use the `getPushNotificationsToken` method to retrieve the token. If enrolment was unsuccessful for some reason,
this method will return `null`.

```php
use Native\Mobile\Events\PushNotification\TokenGenerated;

System::enrollForPushNotifications();

// Later...
#[On('native:' . TokenGenerated::class)]
public function handlePushNotifications(string $token)
{
    // Do something with the token...
}
```
Once you have the token, you may use it from your server-based applications to trigger Push Notifications directly to
your user's device.

> Learn more about [what to do with push tokens here](/docs/mobile/1/digging-deeper/push-notifications).

---

### Biometric ID
```php
Front End Event: `native:Native\Mobile\Events\Biometric\Completed`
Back End Event: `Native\Mobile\Events\Biometric\Completed`
```

For devices that support some form of biometric identification, you can use this to protect and unlock various parts
of your application.

```php
use Native\Mobile\Events\Biometric\Completed;

System::promptForBiometricID()

// Later...
#[On('native:' . Completed::class)]
public function handleBiometricAuth(boolean $success)
{
    if ($success) {
        // Do your super secret activity here
    }
}
```

Using this, you can gate certain parts of your app, allowing you to offer an extra layer of protection for your user's
data.

**Note: Despite the name, Biometric identification only gives you *greater confidence* that the person using your app
is *someone* who has the capacity to unlock the device your app is installed on. It does not allow you to *identify*
that user or prove that they are willingly taking this action.**

