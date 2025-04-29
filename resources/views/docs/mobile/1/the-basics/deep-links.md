---
title: Deep, Universal, App Links and NFC
order: 900
---

## Overview

NativePHP for Mobile supports both **deep linking** and **web-based linking** into your mobile apps.

There are two types of link integrations you can configure:

- **Deep Links** (myapp://some/path)
- **Universal Links (iOS)** and **App Links (Android)** (https://yourdomain.com/some/path)

Each method has its use case, and NativePHP allows you to configure and handle both easily.

---

## Deep Links

Deep links use a **custom URL scheme** to open your app.

For example:

```
myapp://profile/123
```


When a user taps a deep link, the mobile operating system detects the custom scheme and opens your app directly.

### Configuration

To enable deep linking, you must define:

- **Scheme**: The protocol (e.g., myapp)
- **Host**: An optional domain-like segment (e.g., open)

These are configured in your .env:

```dotenv
NATIVEPHP_DEEPLINK_SCHEME=myapp
NATIVEPHP_DEEPLINK_HOST=open
```

## Universal Links (iOS) and App Links (Android)

Universal Links and App Links allow real HTTPS URLs to open your app instead of a web browser, if the app is installed.

For example:
```dotenv
https://bifrost.tech/property/456
```

When a user taps this link:

 - If your app is installed, it opens directly into the app.
 - If not, it opens normally in the browser.

This provides a seamless user experience without needing a custom scheme.

### How It Works
1. You must prove to iOS and Android that you own the domain by hosting a special file:
 - .well-known/apple-app-site-association (for iOS)
 - .well-known/assetlinks.json (for Android)
2. The mobile OS reads these files to verify the link association.
3. Once verified, tapping a real URL will open your app instead of Safari or Chrome.

NativePHP for Mobile handles all of this for you.

### Configuration

To enable Universal Links and App Links, you must define:

- **Host**: The domain name (e.g., bifrost-tech.com)

These are configured in your .env:

```dotenv
NATIVEPHP_DEEPLINK_HOST=bifrost-tech.com
```

## Handling Universal/App Links

Once you've configured your deep link settings, you can handle the link in your app.

Simply setup a route in your web.php file and the deeplink will redirect to your route.

```dotenv
https://bifrost-tech.com/profile/123
```

```php
Route::get('/profile/{id}', function ($id) {
    // Handle the deep link
});
```

## NFC
NFC is a technology that allows you to read and write NFC tags. 

NativePHP handles NFC tag "bumping" just like a Universal/App Link. 
You can use a tool like [NFC Tools](https://www.wakdev.com/en/) to test write NFC tags.

Set the url to a Universal/App Link and the tag will be written to the NFC tag. 
"Bumping" the tag will open the app.



