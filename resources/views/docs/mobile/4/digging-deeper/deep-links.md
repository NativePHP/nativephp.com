---
title: Deep Links
order: 300
---

## Overview

NativePHP for Mobile supports **deep linking** into your app via Custom URL Schemes and Associated Domains:

- **Custom URL Scheme**
    ```
    myapp://some/path
    ```
- **Associated Domains** (a.k.a. Universal Links on iOS, App Links on Android)
    ```
    https://example.net/some/path
    ```

In each case, your app can be opened directly at the route matching `/some/path`.

Each method has its use cases, and NativePHP handles all the platform-specific configuration automatically when you
provide the proper environment variables.

You can even use both approaches at the same time in a single app!

## Custom URL Scheme

Custom URL schemes are a great way to allow apps to pass data between themselves. If your app is installed when a user
uses a deep link that incorporates your custom scheme, your app will open immediately to the desired route.

But note that custom URL schemes can only work when your app has been installed and cannot aid in app discovery. If a
user interacts with URL with a custom scheme for an app they don't have installed, there will be no prompt to install
an app that can load that URL.

To enable your app's custom URL scheme, define it in your `.env`:

```dotenv
NATIVEPHP_DEEPLINK_SCHEME=myapp
```

You should choose a scheme that is unique to your app to avoid confusion with other apps. Note that some schemes are
reserved by the system and cannot be used (e.g. `https`).

## Associated domains

Universal Links/App Links allow real HTTPS URLs to open your app instead of in a web browser, if the app is installed.
If the app is not installed, the URL will load as normal in the browser.

This flow increases the opportunity for app discovery dramatically and provides a much better overall user experience.

### How it works

1. You must prove to the operating system on the user's device that your app is legitimately associated with the domain
    you are trying to redirect by hosting special files on your server:
   - `.well-known/apple-app-site-association` (for iOS)
   - `.well-known/assetlinks.json` (for Android)
2. The mobile OS reads these files to verify the link association
3. Once verified, tapping a real URL will open your app instead of opening it in the user's browser

**NativePHP handles all the technical setup automatically** - you just need to host the verification files and
configure your domain correctly.

To enable an app-associated domain, define it in your `.env`:

```dotenv
NATIVEPHP_DEEPLINK_HOST=example.net
```

## Testing & troubleshooting

Associated Domains do not usually work in simulators. Testing on a real device that connects to a publicly-accessible
server for verification is often the best way to ensure these are operating correctly.

If you are experiencing issues getting your associated domain to open your app, try:
- Completely deleting and reinstalling the app. Registration verifications (including failures) are often cached
    against the app.
- Validating that your associated domain verification files are formatted correctly and contain the correct data.

There is usually no such limitation for Custom URL Schemes.

## Use cases

Deep linking is great for bringing users from another context directly to a key place in your app. Universal/App Links
are usually the more appropriate choice for this because of their flexibility in falling back to simple loading a URL
in the browser.

They're also more likely to behave the same across both platforms.

Then you could use Universal/App Links in:
- NFC tags
- QR codes
- Email/SMS marketing
