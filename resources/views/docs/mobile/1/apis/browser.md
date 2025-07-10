---
title: Browser
order: 250
---

## Overview

The Browser API allows you to open URLs in an in-app browser that is "owned" by your application. This is essential when working with OAuth redirects since you need to provide a redirect URL that would naturally open in the user's default browser.

```php
use Native\Mobile\Facades\Browser;
```

## Methods

### `inApp()`

Opens a URL in an in-app browser window.

**Parameters:**
- `string $url` - The URL to open in the in-app browser

```php
Browser::inApp('https://nativephp.com/mobile');
```

## Use Cases

### OAuth Authentication

The in-app browser is particularly useful for OAuth flows where you need to:
- Redirect users to a third-party authentication provider
- Capture the redirect URL after authentication
- Return control to your app seamlessly

```php
use Native\Mobile\Facades\Browser;

class AuthController extends Component
{
    public function authenticateWithProvider()
    {
        // Open OAuth provider in in-app browser
        Browser::inApp('https://provider.com/oauth/authorize?client_id=your_client_id&redirect_uri=your_app_scheme://oauth/callback');
    }
}
```

### External Content

Display external content while keeping users within your app:

```php
// Open documentation
Browser::inApp('https://docs.example.com/help');

// Open terms of service
Browser::inApp('https://example.com/terms');

// Open privacy policy
Browser::inApp('https://example.com/privacy');
```

## Integration with Deep Links

Use the in-app browser in conjunction with App/Universal/Deep links for complete OAuth flows:

1. **Open OAuth provider** - Use `Browser::inApp()` to start authentication
2. **User authenticates** - User completes authentication in the in-app browser
3. **Redirect to app** - OAuth provider redirects to your app's deep link
4. **Handle in app** - Your app receives the deep link and processes the authentication

```php
use Native\Mobile\Facades\Browser;
use Livewire\Attributes\On;
use Native\Mobile\Events\DeepLink\Received;

class OAuthHandler extends Component
{
    public function startOAuth()
    {
        Browser::inApp('https://github.com/login/oauth/authorize?client_id=your_client_id&redirect_uri=myapp://oauth/callback');
    }
}
```

