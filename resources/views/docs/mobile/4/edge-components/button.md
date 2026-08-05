---
title: Button
order: 140
---

## Overview

A native button. Renders as a SwiftUI `Button` with `buttonStyle(...)` on iOS and a Material3 `Button` on Android.

Visual styling follows Model 3 — colors, radius, shadow, and typography come from the theme. There are intentionally
**no per-instance** color, background, border, radius, shadow, font-size, or font-weight overrides. For full visual
control drop to a [`<native:pressable>`](pressable) wrapping your own content.

@verbatim
```blade
<native:button label="Get Started" @press="handleStart" />
```
@endverbatim

`@press` names a public method on your component — tapping this button calls `handleStart()`.

## Props

The label can be passed as the `label` attribute or as slot content between the tags. If both are set, `label` wins.
Slot content is treated as plain text — nested tags are stripped and whitespace is collapsed. Use the `icon` /
`icon-trailing` props to add icons rather than nesting elements in the slot.

- `label` - Button text (optional if using slot content)
- `variant` - Semantic style: `primary` (default), `secondary`, `destructive`, `ghost`. Each fills its theme
  token solid; for a softer tonal fill, set opacity on the token itself (e.g. `'secondary' => 'fuchsia-500/70'`
  in `config/native-ui.php`) — see [Theming](../digging-deeper/theming)
- `size` - `sm`, `md` (default), `lg`
- `icon` - A leading [icon](icon#icon-name-reference) name (optional)
- `ios-icon` / `android-icon` - Per-platform overrides for the leading icon: an [SF Symbol](icon#ios-sf-symbols)
  name on iOS, a [Material Icon](icon#android-material-icons) name on Android. `icon` is the fallback on whichever
  platform has no override — or skip it and declare only per-platform icons. `iosIcon` / `androidIcon` and the
  `<native:icon>`-style `ios` / `android` are accepted as aliases
- `icon-trailing` - A trailing [icon](icon#icon-name-reference) name (optional)
- `ios-icon-trailing` / `android-icon-trailing` - Per-platform overrides for the trailing icon, mirroring
  `ios-icon` / `android-icon` (camelCase aliases accepted; no `ios` / `android` shorthand for this slot)
- `font` - Custom font for the label: a `resources/fonts/` file token or a config alias like `accent` (optional, string) — see [Text › Custom fonts](text#custom-fonts)
- `line-height` - Label line height as a multiplier of the font size (optional, float)
- `line-height-px` - Label line height as an absolute value in pixels (optional, float)
- `disabled` - Disable the button (optional, boolean, default: `false`). Disabled buttons render with the theme's
  `surface-variant` fill and `on-surface-variant` label on both platforms
- `loading` - Show a spinner in place of the leading icon and prevent presses (optional, boolean, default: `false`).
  Styled like `disabled` while the spinner runs
- `a11y-label` - Accessibility label override (optional)
- `a11y-hint` - Accessibility hint (optional)
- `menu` - Attach a tap-to-open dropdown menu — an array of [`NavAction`](menus) items. Tapping opens the menu
  instead of firing `@press`. See [Menus](menus)

## Events

- `@press` - Component method to call when tapped

<aside>

Layout attributes (`width`, `height`, `flex-grow`, `margin`, `align-self`) flow through to position the button
inside its parent. Per-instance `padding`, `bg`, `border-*`, `border-radius`, `elevation`, `opacity`, `font-*`
attributes are intentionally dropped before reaching the renderer.

</aside>

## Examples

### Variants

@verbatim
```blade
<native:column class="w-full gap-3 p-4">
    <native:button label="Save"   variant="primary"     @press="save" />
    <native:button label="Cancel" variant="secondary"   @press="cancel" />
    <native:button label="Delete" variant="destructive" @press="delete" />
    <native:button label="Skip"   variant="ghost"       @press="skip" />
</native:column>
```
@endverbatim

### Sizes

@verbatim
```blade
<native:row class="gap-2 items-center">
    <native:button label="Small"  size="sm" @press="action" />
    <native:button label="Medium" size="md" @press="action" />
    <native:button label="Large"  size="lg" @press="action" />
</native:row>
```
@endverbatim

### With icons

@verbatim
```blade
<native:button
    label="Continue"
    icon="check"
    icon-trailing="forward"
    @press="next"
/>
```
@endverbatim

### Platform icons

When one shared name doesn't map well on both platforms, give each platform its own symbol — an
[SF Symbol](icon#ios-sf-symbols) name on iOS, a [Material Icon](icon#android-material-icons) name on Android:

@verbatim
```blade
<native:button
    label="Tap to Pay"
    ios-icon="wave.3.right"
    android-icon="nfc"
    @press="startPayment"
/>
```
@endverbatim

Resolution happens per platform: each side uses its override, falling back to `icon` where none is given. Bound
`:ios-icon` / `:android-icon` also accept `App\Icons` enum cases, exactly like
[`<native:icon>`'s `:ios` / `:android`](icon#typed-icon-enums). The trailing slot works the same way via
`ios-icon-trailing` / `android-icon-trailing`.

On iOS the icon's layout height is pinned to the size-driven icon size, so button height depends only on `size` —
not on which SF Symbol the button carries. Tall glyphs like `wave.3.right` no longer stretch the button.

### Loading state

@verbatim
```blade
<native:button label="Saving..." loading @press="save" />
```
@endverbatim

### Label as slot content

@verbatim
```blade
<native:button @press="save" variant="primary">
    Save Changes
</native:button>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Button;

Button::make('Save')
    ->variant('primary')
    ->size('md')
    ->icon('check')
    ->iconTrailing('forward')
    ->disabled(false)
    ->loading(false)
    ->onPress('save');
```

- `make(string $label = '')` - Create a button with an optional label
- `variant(string $value)` - `primary | secondary | destructive | ghost`
- `size(string $value)` - `sm | md | lg`
- `font(string $name)` - Custom label font (file token or config alias)
- `icon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  Leading icon; pass `ios:` / `android:` for per-platform symbols
- `iconTrailing(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  Trailing icon; pass `ios:` / `android:` for per-platform symbols
- `disabled(bool $value = true)` - Disable the button
- `loading(bool $value = true)` - Show a spinner and prevent presses
- `a11yLabel(string $value)` - Accessibility label override
- `a11yHint(string $value)` - Accessibility hint
- `onPress(string $method)` - Component method to invoke on tap
