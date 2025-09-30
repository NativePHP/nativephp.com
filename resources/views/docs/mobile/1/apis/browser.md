---
title: Browser
order: 150
---

## Overview

The Browser API provides three methods for opening URLs, each designed for specific use cases:
in-app browsing, system browser navigation, and web authentication flows.

```php
use Native\Mobile\Facades\Browser;
```

## Methods

### `inApp()`

Opens a URL in an embedded browser within your app using Custom Tabs (Android) or SFSafariViewController (iOS).

```php
Browser::inApp('https://nativephp.com/mobile');
```

### `system()`

Opens a URL in the device's default browser app, leaving your application entirely.

```php
Browser::system('https://nativephp.com/mobile');
```

### `auth()`

Opens a URL in a specialized authentication browser designed for OAuth flows with automatic `nativephp://` redirect handling.

```php
Browser::auth('https://provider.com/oauth/authorize?client_id=123&redirect_uri=nativephp://127.0.0.1/auth/callback');
```

## Use Cases

### When to Use Each Method

**`inApp()`** - Keep users within your app experience:
- Documentation, help pages, terms of service
- External content that relates to your app
- When you want users to easily return to your app

**`system()`** - Full browser experience needed:
- Complex web applications
- Content requiring specific browser features
- When users need bookmarking or sharing capabilities

**`auth()`** - OAuth authentication flows:
- Login with WorkOS, Auth0, Google, Facebook, etc.
- Secure authentication with automatic redirects
- Isolated browser session for security
