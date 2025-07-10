---
title: Overview
order: 1
---
# API Reference

Complete documentation for all NativePHP Mobile APIs. Each API provides access to native device capabilities through familiar PHP facades.

## Available APIs

### Biometrics
**Face ID, Touch ID, Fingerprint Authentication**
```php
Biometrics::promptForBiometricID();
```
Secure user authentication using device biometric sensors. Supports Face ID on iOS, Touch ID, and fingerprint readers on Android.

### Camera
**Photo Capture & Gallery Access**
```php
Camera::getPhoto();
Camera::pickImages('images', true, 5);
```
Take photos with the device camera or select images from the photo gallery. Supports both single and multiple image selection.

### Dialog
**Native UI Elements**
```php
Dialog::alert('Title', 'Message', $buttons, $callback);
Dialog::toast('Success message');
Dialog::share('Title', 'Text', 'https://example.com');
```
Display native alerts, toast notifications, and sharing interfaces that match platform design guidelines.

### Geolocation
**GPS & Location Services**
```php
Geolocation::getCurrentPosition(true); // High accuracy
Geolocation::checkPermissions();
Geolocation::requestPermissions();
```
Access device location services with configurable accuracy levels and proper permission handling.

### Haptics
**Vibration & Tactile Feedback**
```php
Haptics::vibrate();
```
Provide tactile feedback for user interactions, form validation, and important events.

### PushNotifications
**Firebase Cloud Messaging**
```php
PushNotifications::enrollForPushNotifications();
PushNotifications::getPushNotificationsToken();
```
Register devices for push notifications and manage FCM tokens for server-side notification delivery.

### SecureStorage
**Keychain & Keystore Operations**
```php
SecureStorage::set('api_token', $token);
$token = SecureStorage::get('api_token');
SecureStorage::delete('api_token');
```
Store sensitive data securely using iOS Keychain and Android Keystore with automatic encryption.

### System
**System Functions & Legacy API**
```php
System::flashlight(); // Toggle flashlight
```
Control system functions like the flashlight. Also provides deprecated methods that have moved to dedicated facades.

## API Patterns

### Synchronous APIs
Execute immediately and return results:
- `Haptics::vibrate()`
- `System::flashlight()`
- `Dialog::toast()`
- `SecureStorage::set()` / `get()`

### Asynchronous APIs
[Read more about asynchronous API methods here.](/docs/mobile/1/the-basics/asynchronous-methods)

## Platform Support

All APIs work on both iOS and Android with platform-appropriate implementations:
- **iOS**: Uses native iOS frameworks and APIs
- **Android**: Uses Android SDK and native libraries
- **Permissions**: Automatically handled with user prompts when required
- **Fallbacks**: Graceful degradation when features aren't available

- Each API documentation includes complete error handling examples and best practices.
