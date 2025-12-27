---
title: Browser
order: 200
---

## Overview

The Browser API provides three methods for opening URLs, each designed for specific use cases:
in-app browsing, system browser navigation, and web authentication flows.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Browser;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { browser } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `inApp()`

Opens a URL in an embedded browser within your app using Custom Tabs (Android) or SFSafariViewController (iOS).

<x-snippet title="In-App Browser">

<x-snippet.tab name="PHP">

```php
Browser::inApp('https://nativephp.com/mobile');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await browser.inApp('https://nativephp.com/mobile');
```

</x-snippet.tab>
</x-snippet>

### `open()`

Opens a URL in the device's default browser app, leaving your application entirely.

<x-snippet title="System Browser">

<x-snippet.tab name="PHP">

```php
Browser::open('https://nativephp.com/mobile');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await browser.open('https://nativephp.com/mobile');
```

</x-snippet.tab>
</x-snippet>

### `auth()`

Opens a URL in a specialized authentication browser designed for OAuth flows with automatic `nativephp://` redirect handling.

<x-snippet title="Authentication Browser">

<x-snippet.tab name="PHP">

```php
Browser::auth('https://provider.com/oauth/authorize?client_id=123&redirect_uri=nativephp://127.0.0.1/auth/callback');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await browser.auth('https://provider.com/oauth/authorize?client_id=123&redirect_uri=nativephp://127.0.0.1/auth/callback');
```

</x-snippet.tab>
</x-snippet>

## Use Cases

### When to Use Each Method

**`inApp()`** - Keep users within your app experience:
- Documentation, help pages, terms of service
- External content that relates to your app
- When you want users to easily return to your app

**`open()`** - Full browser experience needed:
- Complex web applications
- Content requiring specific browser features
- When users need bookmarking or sharing capabilities

**`auth()`** - OAuth authentication flows:
- Login with WorkOS, Auth0, Google, Facebook, etc.
- Secure authentication with automatic redirects
- Isolated browser session for security
