---
title: Icons
order: 9999
---

## Overview

NativePHP EDGE components use a smart icon mapping system that automatically converts icon names to platform-specific icons. On iOS, icons render as SF Symbols, while Android uses Material Icons. You use the same icon name in your Blade components, and the system handles the platform translation automatically.

## How It Works

The icon system uses a three-tier resolution strategy:

1. **Manual Mapping** - Explicit mappings for common icons and aliases (e.g., `home`, `settings`, `user`)
2. **Smart Fallback** - Attempts to auto-convert unmapped icon names to platform equivalents
3. **Default Fallback** - Uses a circle icon if no match is found

This approach means you can use intuitive icon names and get consistent results across iOS and Android, even when the underlying platform icon names differ.

## Platform Differences

### iOS (SF Symbols)

On iOS, icons render as SF Symbols. Manual mappings convert common icon names to their SF Symbol equivalents. For example:

- `home` → `house.fill`
- `settings` → `gearshape.fill`
- `check` → `checkmark.circle.fill`

If an icon name isn't manually mapped, the system attempts to find a matching SF Symbol by trying variations like `.fill`, `.circle.fill`, and `.square.fill`.

### Android (Material Icons)

On Android, icons render as Material Icons with automatic support for filled and outlined variants. The filled variant is used by default in most components, but components like bottom navigation can switch between filled (selected) and outlined (unselected) states.

Manual mappings convert common icon names to their Material Icon equivalents. For example:

- `home` → `Icons.Filled.Home`
- `settings` → `Icons.Filled.Settings`
- `check` → `Icons.Filled.Check`

## Basic Usage

Use the `icon` attribute in any EDGE component that supports icons:

@verbatim
```blade
<x-native:bottom-nav-item
    id="home"
    icon="home"
    label="Home"
    url="/home"
/>

<x-native:fab
    icon="add"
    label="Create"
    event="create-item"
/>

<x-native:side-nav-item
    id="settings"
    icon="settings"
    label="Settings"
    url="/settings"
/>
```
@endverbatim

## Icon Reference

Icons are organized by category. All icons listed here are manually mapped and guaranteed to work consistently across iOS and Android.

### Navigation

| Icon | Description |
|------|-------------|
| `dashboard` | Grid-style dashboard view |
| `home` | House/home screen |
| `menu` | Three-line hamburger menu |
| `settings` | Gear/settings |
| `account`, `profile`, `user` | User account or profile |
| `person` | Single person |
| `people`, `connections`, `contacts` | Multiple people |
| `group`, `groups` | Group of people |

### Business & Commerce

| Icon | Description |
|------|-------------|
| `orders`, `receipt` | Receipt or order |
| `cart`, `shopping` | Shopping cart |
| `shop`, `store` | Store or storefront |
| `products`, `inventory` | Products or inventory |

### Charts & Data

| Icon | Description |
|------|-------------|
| `chart`, `barchart` | Bar chart |
| `analytics` | Analytics/analysis |
| `summary`, `report`, `assessment` | Summary or report |

### Time & Scheduling

| Icon | Description |
|------|-------------|
| `clock`, `schedule`, `time` | Clock or time |
| `calendar` | Calendar |
| `history` | History or recent |

### Actions

| Icon | Description |
|------|-------------|
| `add`, `plus` | Add or create new |
| `edit` | Edit or modify |
| `delete` | Delete or remove |
| `save` | Save |
| `search` | Search |
| `filter` | Filter |
| `refresh` | Refresh or reload |
| `share` | Share |
| `download` | Download |
| `upload` | Upload |

### Communication

| Icon | Description |
|------|-------------|
| `notifications` | Notifications or alerts |
| `message` | Message or SMS |
| `email`, `mail` | Email |
| `chat` | Chat or conversation |
| `phone` | Phone or call |

### Navigation Arrows

| Icon | Description |
|------|-------------|
| `back` | Back or previous |
| `forward` | Forward or next |
| `up` | Up arrow |
| `down` | Down arrow |

### Status

| Icon | Description |
|------|-------------|
| `check`, `done` | Check or complete |
| `close` | Close or dismiss |
| `warning` | Warning |
| `error` | Error |
| `info` | Information |

### Authentication

| Icon | Description |
|------|-------------|
| `login` | Login |
| `logout`, `exit` | Logout or exit |
| `lock` | Locked |
| `unlock` | Unlocked |

### Content

| Icon | Description |
|------|-------------|
| `favorite`, `heart` | Favorite or like |
| `star` | Star or rating |
| `bookmark` | Bookmark |
| `image`, `photo` | Image or photo |
| `image-plus` | Add photo |
| `video` | Video |
| `folder` | Folder |
| `folder-lock` | Locked folder |
| `file`, `description` | Document or file |
| `book-open` | Book |
| `newspaper`, `news`, `article` | News or article |

### Device & Hardware

| Icon | Description |
|------|-------------|
| `camera` | Camera |
| `qr`, `qrcode`, `qr-code` | QR code scanner |
| `device-phone-mobile`, `smartphone` | Mobile phone |
| `vibrate` | Vibration |
| `bell` | Bell or notification |
| `finger-print`, `fingerprint` | Fingerprint or biometric |
| `light-bulb`, `lightbulb`, `flashlight` | Light bulb or flashlight |
| `map`, `location` | Map or location |
| `globe-alt`, `globe`, `web` | Globe or web |
| `bolt`, `flash` | Lightning bolt or flash |

### Audio & Volume

| Icon | Description |
|------|-------------|
| `speaker`, `speaker-wave` | Speaker with sound |
| `volume-up` | Volume up |
| `volume-down` | Volume down |
| `volume-mute`, `mute` | Muted |
| `volume-off` | Volume off |
| `music`, `audio`, `music-note` | Music or audio |
| `microphone`, `mic` | Microphone |

### Miscellaneous

| Icon | Description |
|------|-------------|
| `help` | Help or question |
| `about`, `information-circle` | Information or about |
| `more` | More options |
| `list` | List view |
| `visibility` | Visible |
| `visibility_off` | Hidden |

## Auto-Conversion

If an icon name isn't in the manual mapping, the system attempts to auto-convert it to a platform-specific icon.

### iOS Auto-Conversion

The system normalizes the icon name by removing hyphens and underscores, then tries SF Symbol patterns like:

- `{icon}.fill` (e.g., `newspaper.fill`)
- `{icon}` (e.g., `newspaper`)
- `{icon}.circle.fill` (e.g., `newspaper.circle.fill`)
- `{icon}.square.fill` (e.g., `newspaper.square.fill`)

For example, if you use `newspaper` and it's not manually mapped, the system will try to find `newspaper.fill` as an SF Symbol.

### Android Auto-Conversion

The system converts kebab-case and snake_case to PascalCase, then uses reflection to find the corresponding Material Icon in the Filled or Outlined variants.

For example, if you use `shopping-cart`, the system will:

1. Convert to PascalCase: `ShoppingCart`
2. Look for `Icons.Filled.ShoppingCart`
3. Fall back to `Icons.AutoMirrored.Filled.ShoppingCart` if the standard icon isn't found

## Custom Icons

Since the system supports auto-conversion, you can use any valid SF Symbol name on iOS or Material Icon name on Android. While we can't guarantee the icon will exist on both platforms, you can leverage platform-specific auto-conversion for icons not in the manual mapping.

For the best cross-platform experience, stick to icon names in the reference table above. These are guaranteed to work consistently across iOS and Android.

## Best Practices

- **Use semantic names** - Choose icon names that match the action or concept (e.g., `home` for the home screen, not `front_page`)
- **Stay consistent** - Use the same icon name throughout your app for the same action
- **Test on both platforms** - If you use auto-converted icons, verify they appear correctly on iOS and Android
- **Prefer manual mappings** - Icons in the reference table are tested and consistent across platforms
- **Avoid generic names** - Use specific names like `settings` instead of just `gear`
