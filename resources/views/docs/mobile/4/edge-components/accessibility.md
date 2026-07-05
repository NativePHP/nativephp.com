---
title: Accessibility
order: 160
---

## Overview

EDGE components render real native controls, so most accessibility behavior comes free from the platform:
VoiceOver (iOS) and TalkBack (Android) recognize buttons, switches, checkboxes, sliders, and tabs as what they
are, text scales with the user's system font size, and interactive controls maintain minimum touch-target sizes
(44pt on iOS, 48dp on Android) even when they render smaller visually.

What the platform can't know is *what your UI means*. That's where the accessibility props come in.

## Accessibility props

Every element accepts two attributes:

- `a11y-label` - What the screen reader announces for the element. Overrides the visible text.
- `a11y-hint` - Supplementary usage guidance, announced after the label (e.g. "Double-tap to open settings").

@verbatim
```blade
<native:button icon="gear" a11y-label="Settings" @press="openSettings" />
<native:slider :value="$volume" a11y-label="Volume" a11y-hint="Adjusts playback volume" />
```
@endverbatim

The same props are available on the fluent PHP API — `a11yLabel()` and `a11yHint()` live on the base element, so
**every** element and plugin component has them:

@verbatim
```php
Button::make()
    ->icon('gear')
    ->a11yLabel('Settings')
    ->onPress('openSettings');
```
@endverbatim

Components with visible text labels (buttons with labels, checkboxes, toggles, radios, tabs) announce their
visible text automatically — you only need `a11y-label` when there is no visible text or when the visible text
isn't descriptive enough on its own.

## Icon-only controls

**Always set `a11y-label` on icon-only buttons, chips, and tabs.** An icon-only control without a label is a
mystery to screen reader users.

@verbatim
```blade
{{-- Bad: VoiceOver has nothing meaningful to announce --}}
<native:button icon="trash" variant="destructive" @press="deleteItem" />

{{-- Good --}}
<native:button icon="trash" variant="destructive" a11y-label="Delete item" @press="deleteItem" />
```
@endverbatim

## Icons and images

Standalone `<native:icon />` elements are **decorative by default** — they are hidden from screen readers
unless you give them an `a11y-label`. If an icon conveys meaning on its own (or is tappable), label it:

@verbatim
```blade
<native:icon name="wifi_off" a11y-label="No internet connection" />
```
@endverbatim

Images take an `alt` attribute, mirroring the web. With `alt`, the image is announced; without it, the image
is treated as decorative and skipped:

@verbatim
```blade
<native:image src="{{ $product->photo }}" alt="{{ $product->name }}" :height="200" />
```
@endverbatim

## List items

The tappable trailing icon button on a [list item](list) takes its own label via `trailing-a11y-label`:

@verbatim
```blade
<native:list-item
    headline="Backups"
    trailingIconButton="info"
    trailing-a11y-label="Backup details"
    @trailing-press="showBackupInfo"
/>
```
@endverbatim

List rows group their content (headline, supporting text, leading and trailing decorations) into a single
screen-reader focus stop; interactive trailing controls remain individually focusable.

## Navigation chrome

Icon-only top-bar actions built with `NavAction` are unlabeled for screen readers unless you set a label. Give
every icon-only action an `a11yLabel()`:

@verbatim
```php
NavBarOptions::make()->actions([
    NavAction::make('search')->icon('magnifyingglass')->a11yLabel('Search')->press('openSearch'),
    NavAction::make('more')->icon('ellipsis')->a11yLabel('More options')->items([...]),
]);
```
@endverbatim

## What you get for free

- **Font scaling** — All component text respects the user's system font size (Dynamic Type on iOS, font scale
  on Android). Fixed-size text is not used anywhere in the component set.
- **Touch targets** — Small visual controls (checkboxes, radios, chips, icon buttons, nav actions) keep their
  compact look but expand their tappable area to the platform minimum.
- **State announcements** — Toggles, checkboxes, radios, chips, tabs, and sliders announce their current state
  ("Checked", "Selected", values) and stateful buttons announce loading.
- **Reduced motion** — On iOS, drawer and screen transitions respect the system Reduce Motion setting.
- **Contrast** — The default theme palette meets WCAG AA (4.5:1) for text on colored surfaces. If you customize
  the theme, keep your `on*` colors at 4.5:1 against their backgrounds.

## Testing accessibility

The [testing suite](../testing/advanced#accessibility-audits) can audit a rendered screen for missing labels
without a device. Sweep every screen in one data-driven test so a new screen can't ship an unlabeled control:

@verbatim
```php
use Native\Mobile\Edge\NativeRouter;

it('renders every screen accessibly', function () {
    foreach (array_keys(NativeRouter::registeredRoutes()) as $uri) {
        Native::visit($uri)->assertAccessible();
    }
});
```
@endverbatim

<aside>

Manual pass, too: turn on VoiceOver (iOS) or TalkBack (Android) and swipe through each screen. Every focus stop
should announce something a person could act on. Anything announced as a raw icon name, an empty element, or
"button" with no label needs an `a11y-label`.

</aside>
