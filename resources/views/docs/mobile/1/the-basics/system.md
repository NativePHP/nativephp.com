---
title: System
order: 800
---

## The System

One of the main advantages of building a native application is having more direct access to system resources, such as
device sensors and APIs that aren't typically accessible inside a browser's sandbox. 

NativePHP makes it trivial to access these resources and APIs.

One of the main challenges - particularly when writing cross-platform apps - is that each operating system has
its own set of available APIs, along with their own idiosyncrasies.

NativePHP smooths over as much of this as possible, to offer a simple and consistent set of interfaces regardless of
the platform on which your app is running.

While some features are platform-specific, NativePHP gracefully handles this for you so that you don't have to think
about whether a particular feature is specific to iOS or Android.

Most of the system-related features are available through the `System` facade.

## Synchronous vs. Asynchronous Methods

It is important to understand the difference between synchronous and asynchronous methods. Some methods
like `flashlight` and `vibrate` are synchronous, meaning that they will block the current thread until the
operation is complete. Other methods like `camera` and `biometric` are asynchronous, meaning that they
will return immediately and the operation will be performed in the background. When the operation is
complete, the method will `broadcast an event` to your frontend via an injected javascript event.

In order to receive these events, you must register a listener for the event. 

```php
use Native\Ios\Facades\System;
```

## Camera (Async)
Event: `native:\Native\Events\Camera`

You may request the native camera interface to take a photograph by calling the `camera` method:

```php
$imageData = System::camera()
```

When the user takes a photograph the event is fired with a payload array that contains one item: `photoPath` 
which is a string containing the path to the photo.

**Note: The first time your application asks to use the camera, the user will be prompted to grant permission. If they
decline, triggering the camera API will silently fail.**

## Vibration

You may vibrate the user's device by calling the `vibrate` method:

```php
System::vibrate()
```

_Coming Soon_ Options: `duration` and `intensity` 

## Push Notifications

Currently, NativePHP uses [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging) to send push notifications to your users.

Simply use the `enrollForPushNotifications` method to trigger enrolment. If this is the first time that your app tries
to enrol this device for push notifications, the user will be presented with a native alert, allowing them to opt-in.

Then use the `getPushNotificationsToken` method to retrieve the token. If enrolment was unsuccessful for some reason,
this method will return `null`.

```php
System::enrolForPushNotifications();

// Later...

if ($token = System::getPushNotificationsToken()) {
    // Do something with the token...
}
```

Once you have the token, you may use it from your server-based applications to trigger Push Notifications directly to
your user's device.

## Accelerometer

**COMING SOON**

## GPS

**COMING SOON**

## Wallet

**COMING SOON**

## Encryption / Decryption

**COMING SOON**

Almost every non-trivial application will require some concept of secure data storage and retrieval. For example, if
you want to generate and store an API key to access a third-party service on behalf of your user.

You shouldn't ship these sorts of secrets _with_ your app, but rather generate them or ask your user for them at
runtime.

But when your app is running on a user's device, you have
[far less control and fewer guarantees](/docs/digging-deeper/security) over the safety of any secrets stored.

On a traditional server-rendered application, this is a relatively simple problem to solve using server-side encryption
with keys which are hidden from end users.

For this to work on the user's device, you need to be able to generate and store an encryption key securely.

NativePHP takes care of the key generation and storage for you, all that's left for you to do is encrypt, store and
decrypt the secrets that you need to store on behalf of your user.

NativePHP allows you to encrypt and decrypt data in your application easily:

```php
if (System::canEncrypt()) {
    $encrypted = System::encrypt('secret_key_a79hiunfw86...');

    // $encrypted => 'djEwJo+Huv+aeBgUoav5nIJWRQ=='
}
```

You can then safely store the encrypted string in a database or the filesystem.

When you need to get the original value, you can decrypt it:

```php
if (System::canEncrypt()) {
    $decrypted = System::decrypt('djEwJo+Huv+aeBgUoav5nIJWRQ==');

    // $decrypted = 'secret_key_a79hiunfw86...'
}
```

## Biometric ID

For devices that support some form of biometric identification, you can use this to protect and unlock various parts
of your application.

```php
if (System::promptForBiometricID()) {
    // Do your super secret activity here
}
```

Using this, you can gate certain parts of your app, allowing you to offer an extra layer of protection for your user's
data.

**Note: Despite the name, Biometric identification only gives you *greater confidence* that the person using your app
is *someone* who has the capacity to unlock the device your app is installed on. It does not allow you to *identify*
that user or prove that they are willingly taking this action.**

## Time Zones

**COMING SOON**

PHP and your Laravel application are configured to work with the time zone that the device reports it is currently
operating in.

This means that, for the most part, any dates and times your show will already be in the appropriate time zone for the
user without having to ask your users to manually select their current time zone.

Your app will also be responsive to changes in the system's time zone settings, e.g. in case the user moves between
time zones.
