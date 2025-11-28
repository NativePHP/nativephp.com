---
title: Icons
order: 9999
---

## Overview

NativePHP EDGE components use a smart icon mapping system that automatically converts icon names to platform-specific
icons. On iOS, icons render as [SF Symbols](https://developer.apple.com/sf-symbols/), while Android uses
[Material Icons](https://fonts.google.com/icons?icon.set=Material+Icons).

You don't need to worry about the differences! Just use a single, consistent icon name in your components, and the EDGE
handles the platform translation automatically.

## How It Works

The icon system uses a three-tier resolution strategy:

1. **Manual Mapping** - Explicit mappings for common icons and aliases (e.g., `home`, `settings`, `user`)
2. **Smart Fallback** - Attempts to auto-convert unmapped icon names to platform equivalents
3. **Default Fallback** - Uses a circle icon if no match is found

This approach means you can use intuitive icon names and get consistent results across iOS and Android, even when the
underlying platform icon names differ.

## Platform Differences

### iOS (SF Symbols)

On iOS, icons render as SF Symbols. Manual mappings convert common icon names to their SF Symbol equivalents.
For example:

- `home` → `house.fill`
- `settings` → `gearshape.fill`
- `check` → `checkmark.circle.fill`

If an icon name isn't manually mapped, the system attempts to find a matching SF Symbol by trying variations like
`.fill`, `.circle.fill`, and `.square.fill`.

### Android (Material Icons)

On Android, icons render as Material Icons with automatic support for filled and outlined variants. The filled variant
is used by default in most components, but components like bottom navigation can switch between filled (selected) and
outlined (unselected) states.

Manual mappings convert common icon names to their Material Icon equivalents. For example:

- `home` → `Icons.Filled.Home`
- `settings` → `Icons.Filled.Settings`
- `check` → `Icons.Filled.Check`

## Basic Usage

Use the `icon` attribute in any EDGE component that supports icons, simply passing the name of the icon you wish to use:

@verbatim
```blade
<native:bottom-nav-item
    id="home"
    icon="home"
    label="Home"
    url="/home"
/>
```
@endverbatim

## Icon Reference

Coming soon!

## Best Practices

Icons have meaning and most users will associate the visual cues of icons and the underlying behavior or section of an
application across apps. So try to maintain consistent use of icons to help guide users through your app.

- **Stay consistent** - Use the same icon name throughout your app for the same action
- **Test on both platforms** - If you use auto-converted icons, verify they appear correctly on iOS and Android
