---
title: Sign in with Apple Button
order: 375
---

## Overview

<x-docs.version-badge package="mobile-ui" since="0.9" />

<div class="images-two-up not-prose">

![](/img/docs/edge-sign-in-with-apple-button-ios-light.png)

![](/img/docs/edge-sign-in-with-apple-button-ios-dark.png)

</div>

Apple's own Sign in with Apple button. On iOS it is SwiftUI's `SignInWithAppleButton`, so the logo, title, font and
proportions are always the ones [Apple's guidelines](https://developer.apple.com/design/human-interface-guidelines/sign-in-with-apple)
ask for. App Review checks these, so use this element rather than a `<native:button>` with an Apple logo.

Tapping the button runs the Apple ID request, as the system button always does. Apple's answer comes back to your
component as a native event, so you don't call anything to start the sign-in.

@verbatim
```blade
<native:sign-in-with-apple-button :nonce="$nonce" class="w-full" />
```
@endverbatim

```php
use Native\Mobile\Attributes\On;
use Native\Mobile\UI\Events\SignInWithAppleCompleted;
use Native\Mobile\UI\Events\SignInWithAppleFailed;

#[On(SignInWithAppleCompleted::class)]
public function signedInWithApple(string $userId, ?string $identityToken = null, ?string $nonce = null): void
{
    // Send the identity token and your raw nonce to your server to verify.
}

#[On(SignInWithAppleFailed::class)]
public function appleSignInFailed(string $code, string $message = ''): void
{
    if ($code === SignInWithAppleFailed::Canceled) {
        return;
    }

    // Show the person what went wrong.
}
```

On iOS the button needs `nativephp/mobile` 4.7 or later. Signing in also needs the Sign in with Apple capability on
your App ID and the `com.apple.developer.applesignin` entitlement in the app. NativePHP adds entitlements from plugins,
so use a plugin that declares it.

## Props

- `label` - The button's title: `sign-in` (default) for "Sign in with Apple", `continue` for "Continue with Apple", or
  `sign-up` for "Sign up with Apple". Apple translates the title into the device's language. These are the only
  titles Apple allows, so any other value throws an `InvalidArgumentException`.
- `button-style` - The button's colors: `auto` (default), `black`, `white` or `white-outline`. `auto` is black in light
  mode and white in dark mode. Use `white-outline` on white or very light backgrounds.
- `nonce` - Sent to Apple as you give it, and echoed back on the completed event (optional). Pass the SHA-256 of a
  random nonce you keep in memory, as hex. Apple's identity token carries the hash, and your server checks it against
  the raw nonce. The raw value never needs to be in your view.
- `scopes` - What to ask for besides the user ID: any of `email` and `full-name`, as an array or a comma-separated
  string (optional, default: none). Apple only sends them the first time someone signs in to your app.
- `state` - Any string, echoed back on both events (optional)
- `corner-radius` - Corner radius in points. A `rounded-*` class sets it too, and the attribute wins if you use both.
  It can round the corners more than Apple does, up to a capsule at half the height or more, so `rounded-full` works.
  It can't make them squarer than Apple's.
- `height` - Height in points, or an `h-*` class. Defaults to 44. The title and logo scale with the height. Apple asks
  for at least 30.
- `disabled` - Dims the button and ignores taps (optional, boolean, default: `false`)
- `a11y-label` - Accessibility label. VoiceOver reads Apple's title when it's not set (optional)
- `a11y-hint` - Accessibility hint (optional)

The button takes the width its parent gives it, like other controls. Use `w-full` to fill the row, or `w-[240]` for a
fixed width. Apple asks for at least 140 points.

Background, border, shadow and padding classes are dropped, since Apple's guidelines fix how the button looks. Margins
and other layout attributes work as usual.

## Events

- `@press` - Component method called when the button is tapped, as Apple's request starts. Useful for clearing an
  earlier error.

The result arrives as one of two native events, which you listen for with `#[On(...)]` as above.

`Native\Mobile\UI\Events\SignInWithAppleCompleted`:

- `userId` - Apple's stable ID for this person in your app
- `identityToken` - The JWT Apple signed. Send it to your server to verify
- `authorizationCode` - A short-lived code your server can exchange with Apple
- `email`, `givenName`, `familyName` - Only on the first sign-in, and only for the scopes you asked for
- `nonce` - The `nonce` the button was given
- `state` - The `state` the button was given
- `realUserStatus` - `likely_real`, `unknown` or `unsupported`

`Native\Mobile\UI\Events\SignInWithAppleFailed`:

- `code` - `canceled` when the person closed Apple's sheet. Otherwise `failed`, `invalid_response`, `not_handled`,
  `not_interactive` or `unknown`
- `message` - Apple's description of the error
- `state` - The `state` the button was given

`unknown` is Apple's catch-all error 1000. On a phone it usually means the phone isn't signed in to an Apple Account, or
the app is missing the entitlement. Builds for the simulator always get it, since they don't carry entitlements, so test
a real sign-in on a device.

## Android

Apple has no Sign in with Apple button on Android, so this element draws nothing there. If you want something in its
place on Android, put it between the tags. Children only render on Android, and iOS ignores them.

@verbatim
```blade
<native:sign-in-with-apple-button :nonce="$nonce" class="w-full">
    <native:button variant="secondary" @press="signInWithAppleOnTheWeb" class="w-full">Sign in with Apple</native:button>
</native:sign-in-with-apple-button>
```
@endverbatim

## Examples

### A fresh nonce for every attempt

Keep the raw nonce on the component and give the button its hash. Make a new one after every answer, so a stale
answer can't be used again.

```php
public string $appleNonce = '';

public function mount(): void
{
    $this->appleNonce = bin2hex(random_bytes(32));
}

#[On(SignInWithAppleCompleted::class)]
public function signedInWithApple(string $userId, ?string $identityToken = null, ?string $nonce = null): void
{
    $raw = $this->appleNonce;
    $this->appleNonce = bin2hex(random_bytes(32));

    if ($nonce !== hash('sha256', $raw) || $identityToken === null) {
        return;
    }

    // Send $identityToken and $raw to your server.
}
```

@verbatim
```blade
<native:sign-in-with-apple-button :nonce="hash('sha256', $appleNonce)" class="w-full" />
```
@endverbatim

### Titles and styles

@verbatim
```blade
<native:column class="w-full px-6 gap-3">
    <native:sign-in-with-apple-button label="continue" button-style="black" class="w-full" />
    <native:sign-in-with-apple-button label="sign-up" button-style="white-outline" :scopes="['email', 'full-name']" class="w-full" />
    <native:sign-in-with-apple-button button-style="white" class="w-full rounded-full" />
</native:column>
```
@endverbatim

### Building it in PHP

@verbatim
```php
use Native\Mobile\UI\Elements\SignInWithAppleButton;

SignInWithAppleButton::make()
    ->label('continue')
    ->buttonStyle('black')
    ->scopes(['email'])
    ->nonce(hash('sha256', $this->appleNonce))
    ->cornerRadius(12)
    ->fillWidth();
```
@endverbatim
