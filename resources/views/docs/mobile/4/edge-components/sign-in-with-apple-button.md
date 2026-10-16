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

Apple's own Sign in with Apple button. On iOS it draws the system button, so the logo, title, font and proportions
are always the ones [Apple's guidelines](https://developer.apple.com/design/human-interface-guidelines/sign-in-with-apple)
ask for. App Review checks these, so use this element rather than a `<native:button>` with an Apple logo.

The button doesn't sign anyone in by itself. Tapping it calls your `@press` method, and that method starts the sign-in
with whichever Sign in with Apple plugin your app uses. The button doesn't depend on any particular plugin.

@verbatim
```blade
<native:sign-in-with-apple-button @press="signInWithApple" class="w-full" />
```
@endverbatim

`signInWithApple()` is a public method on your component. Start the sign-in there and handle the result in the
plugin's events, as you would from any other button.

## Props

- `label` - The button's title: `sign-in` (default) for "Sign in with Apple", `continue` for "Continue with Apple", or
  `sign-up` for "Sign up with Apple". Apple translates the title into the device's language. These are the only
  titles Apple allows, so any other value throws an `InvalidArgumentException`.
- `button-style` - The button's colors: `auto` (default), `black`, `white` or `white-outline`. `auto` is black in light
  mode and white in dark mode. Use `white-outline` on white or very light backgrounds.
- `corner-radius` - Corner radius in points. A `rounded-*` class sets it too, and the attribute wins if you use both.
  Half the height or more draws a capsule, so `rounded-full` works. Without either, the button keeps the system's
  default corners.
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

- `@press` - Component method called when the button is tapped

## Android

Apple has no Sign in with Apple button on Android, so this element draws nothing there. If you want something in its
place on Android, put it between the tags. Children only render on Android, and iOS ignores them.

@verbatim
```blade
<native:sign-in-with-apple-button @press="signInWithApple" class="w-full">
    <native:button variant="secondary" @press="signInWithApple" class="w-full">Sign in with Apple</native:button>
</native:sign-in-with-apple-button>
```
@endverbatim

## Examples

### Under another sign-in button

Give both buttons the same width and height so they line up. Apple's guidelines ask that Sign in with Apple is no
smaller than the other sign-in buttons on the screen.

@verbatim
```blade
<native:column class="w-full px-6 gap-3">
    <native:button variant="primary" @press="signInWithGitHub" class="w-full h-[50]">Sign in with GitHub</native:button>
    <native:sign-in-with-apple-button @press="signInWithApple" class="w-full h-[50]" />
</native:column>
```
@endverbatim

### Titles and styles

@verbatim
```blade
<native:column class="w-full px-6 gap-3">
    <native:sign-in-with-apple-button label="continue" button-style="black" @press="signInWithApple" class="w-full" />
    <native:sign-in-with-apple-button label="sign-up" button-style="white-outline" @press="signUpWithApple" class="w-full" />
    <native:sign-in-with-apple-button button-style="white" class="w-full rounded-full" @press="signInWithApple" />
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
    ->cornerRadius(12)
    ->onPress('signInWithApple')
    ->fillWidth();
```
@endverbatim
