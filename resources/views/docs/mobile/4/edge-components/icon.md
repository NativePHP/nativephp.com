---
title: Icon
order: 230
---

## Overview

Displays a platform-native icon. On iOS, icons render as SF Symbols. On Android, icons render as Material Icons.
A smart mapping system translates common icon names across platforms automatically.

@verbatim
```blade
<native:icon name="home" :size="24" color="#1E293B" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `name` - Icon name (required unless `ios`/`android` are given, string). See the [Icons](icons) reference
- `ios` / `android` - Per-platform overrides: an [SF Symbol](icons) name for iOS and a [Material Icon](icons) name
  for Android, so one tag renders the right symbol on each platform. Use in place of `name` when the platforms
  need different icons (`<native:icon ios="gearshape" android="settings" />`). When bound with `:ios` / `:android`,
  these also accept enum cases directly — `:ios="Ios::Gearshape"`, `:android="Android::Settings"`
- `size` - Icon size in dp (optional, float, default: `24`)
- `color` - Icon color as hex string (optional, default: platform default)
- `dark-color` - Icon color when the device is in dark mode, as a hex string (optional). Overrides `color` in dark mode
- `a11y-label` - Accessibility label (optional). Icons are decorative by default — hidden from screen readers
  unless this is set. Label any icon that conveys meaning on its own. See [Accessibility](../digging-deeper/accessibility)

<aside>

`<native:icon />` is a self-closing element. It does not accept children. For a complete list of available icon names
and platform-specific usage, see the [Icons](icons) reference page.

</aside>

## Examples

### Basic icons

@verbatim
```blade
<native:row :gap="16" :align-items="1">
    <native:icon name="home" :size="24" />
    <native:icon name="search" :size="24" />
    <native:icon name="settings" :size="24" />
    <native:icon name="person" :size="24" />
</native:row>
```
@endverbatim

### Colored icon with label

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:icon name="check" :size="20" color="#22C55E" />
    <native:text class="text-base" color="#22C55E">Verified</native:text>
</native:row>
```
@endverbatim

### Large icon

@verbatim
```blade
<native:column center :padding="32">
    <native:icon name="email" :size="64" color="#94A3B8" />
    <native:text class="text-lg text-theme-on-surface-variant">No messages</native:text>
</native:column>
```
@endverbatim

### Platform-specific icon

Give each platform its own symbol with the `ios` / `android` attributes — resolution happens per platform, so one
tag renders the right icon on each:

@verbatim
```blade
<native:icon ios="gearshape" android="settings" :size="28" />
```
@endverbatim

Or bind enum cases directly with `:ios` / `:android`:

@verbatim
```blade
<native:icon :ios="Ios::Gearshape" :android="Android::Settings" :size="28" />
```
@endverbatim

## Element

```php
use App\Icons\Android;
use App\Icons\Ios;
use Native\Mobile\Edge\Elements\Icon;

Icon::make('home')->size(24)->color('#1E293B');

// Per-platform symbols — a shared name, enum overrides, or both:
Icon::make(ios: Ios::Gearshape, android: Android::Settings);
Icon::make('share', ios: Ios::SquareAndArrowUp);
```

- `make(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  Create an icon from a shared name, per-platform overrides, or both
- `name(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  Set the icon; pass `ios:` / `android:` named args for per-platform overrides
- `size(float $size)` - Icon size in dp
- `color(string $hex)` - Icon color
- `darkColor(string $hex)` - Icon color in dark mode (overrides `color`)
- `a11yLabel(string $label)` - Accessibility label (icons are hidden from screen readers without one)
