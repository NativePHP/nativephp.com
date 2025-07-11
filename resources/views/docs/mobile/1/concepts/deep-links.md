---
title: Deep, Universal, App Links and NFC
order: 300
---

## Overview

NativePHP for Mobile supports both **deep linking** and **web-based linking** into your mobile apps.

There are two types of link integrations you can configure:

- **Deep Links** (myapp://some/path)
- **Universal Links (iOS)** and **App Links (Android)** (https://example.net/some/path)

Each method has its use case, and NativePHP handles all the platform-specific configuration automatically when you provide the proper environment variables.

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

### Platform Behavior

- **iOS**: Deep links work immediately after installation
- **Android**: Deep links work immediately after installation
- **Both**: Apps will open directly when deep links are tapped

## Universal Links (iOS) and App Links (Android)

Universal Links and App Links allow real HTTPS URLs to open your app instead of a web browser, if the app is installed.

For example:
```dotenv
https://example.net/property/456
```

### User Experience

When a user taps this link:

- **If your app is installed**: It opens directly into the app
- **If not installed**: It opens normally in the browser
- **Seamless fallback**: No broken experience for users without the app

This provides a seamless user experience without needing a custom scheme.

### How It Works

1. You must prove to iOS and Android that you own the domain by hosting special files:
   - `.well-known/apple-app-site-association` (for iOS)
   - `.well-known/assetlinks.json` (for Android)
2. The mobile OS reads these files to verify the link association
3. Once verified, tapping a real URL will open your app instead of Safari or Chrome

**NativePHP handles all the technical setup automatically** - you just need to host the verification files and configure your domain.

### Configuration

To enable Universal Links and App Links, you must define:

- **Host**: The domain name (e.g., example.net)

These are configured in your .env:

```dotenv
NATIVEPHP_DEEPLINK_HOST=example.net
```

#### Complete Configuration Example

```dotenv
# For both deep links and universal/app links
NATIVEPHP_DEEPLINK_SCHEME=myapp
NATIVEPHP_DEEPLINK_HOST=example.net

# Your app will respond to:
# myapp://profile/123 (deep link)
# https://example.net/profile/123 (universal/app link)
```

## Domain Verification

### Required Files

The app stores generate the content for these files, but you must host them on your domain:

#### iOS - `.well-known/apple-app-site-association`

```json
{
  "applinks": {
    "details": [
      {
        "appIDs": ["TEAM_ID.com.yourcompany.yourapp"],
        "components": [
          {
            "*": "*"
          }
        ]
      }
    ]
  }
}
```

#### Android - `.well-known/assetlinks.json`

```json
[{
  "relation": ["delegate_permission/common.handle_all_urls"],
  "target": {
    "namespace": "android_app",
    "package_name": "com.yourcompany.yourapp",
    "sha256_cert_fingerprints": ["SHA256_FINGERPRINT"]
  }
}]
```

### Verification Process

1. **iOS**: Apple's servers periodically check the `apple-app-site-association` file
2. **Android**: Google Play Services verifies the `assetlinks.json` file
3. **Both**: Files must be accessible via HTTPS without redirects
4. **Content-Type**: Serve files as `application/json`

### Automatic Configuration

NativePHP automatically:
- Configures iOS `associatedDomains` in your app
- Sets up Android `intentFilters` for your domain
- Generates the correct fingerprints and app IDs
- Handles platform-specific URL routing

## Platform-Specific Behavior

### iOS Universal Links

- **Immediate**: Work as soon as the app is installed
- **Smart Banner**: iOS can display an install banner if app isn't installed
- **Fallback**: Opens in Safari if app isn't installed
- **Cross-app**: Work from any app, not just Safari

### Android App Links

- **Verification**: Requires domain verification before working
- **Default**: Can be set as default handler for domain links
- **Fallback**: Opens in Chrome if app isn't installed
- **Intent**: Uses Android's Intent system for routing

## Handling Universal/App Links

Once you've configured your deep link settings, you can handle the link in your app.

Simply set up a route in your web.php file and the deeplink will redirect to your route.

```dotenv
https://example.net/profile/123
```

```php
Route::get('/profile/{id}', function ($id) {
    // Handle the deep link
    // This works for both deep links and universal/app links
});
```

## Testing and Development

### Testing Limitations

- **Development builds**: Universal/App Links may not work in development
- **Production required**: Full testing requires production builds and domain verification
- **Simulator**: iOS Simulator may not handle Universal Links correctly

### Best Practices

1. **Test both link types** - Ensure deep links and universal/app links work
2. **Verify domain files** - Check that .well-known files are accessible
3. **Production testing** - Test universal/app links with production builds
4. **Fallback handling** - Ensure your website handles users without the app
5. **Analytics tracking** - Monitor which link types are most effective

## NFC

NFC is a technology that allows you to read and write NFC tags. 

NativePHP handles NFC tag "bumping" just like a Universal/App Link. 
You can use a tool like [NFC Tools](https://www.wakdev.com/en/) to write NFC tags.

Set the url to a Universal/App Link and the tag will be written to the NFC tag. 
"Bumping" the tag will open the app.

### NFC Configuration

NFC tags work best with Universal/App Links because:
- They provide fallback to website if app isn't installed
- They work across different devices and platforms
- They provide a better user experience than custom schemes

```bash
# Write this URL to an NFC tag
https://example.net/product/456

# When "bumped":
# - Opens your app if installed
# - Opens website if not installed
```

## Troubleshooting

### Common Issues

1. **Universal Links not working**: Check domain verification files
2. **Deep links not opening**: Verify URL scheme configuration
3. **Wrong app opening**: Check for conflicting URL schemes
4. **iOS Smart Banner**: Ensure proper app store metadata

### Debug Steps

1. **Verify .env configuration** - Check scheme and host values
2. **Test deep links first** - Easier to debug than universal links
3. **Check domain files** - Ensure .well-known files are accessible
4. **Use production builds** - Development builds may not work correctly
5. **Monitor app logs** - Check for link handling errors

Remember that NativePHP handles all the complex platform-specific setup automatically - you just need to configure your domain and environment variables correctly.
