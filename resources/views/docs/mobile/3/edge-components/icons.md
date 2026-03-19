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

The icon system uses a four-tier resolution strategy:

1. **Direct Platform Icons** - On iOS, if the name contains a `.` it's used as a direct SF Symbol path (e.g., `car.side.fill`). On Android, any Material Icon ligature name works directly (e.g., `shopping_cart`).
2. **Manual Mapping** - Explicit mappings for common icons and aliases (e.g., `home`, `settings`, `user`)
3. **Smart Fallback** - Attempts to auto-convert unmapped icon names to platform equivalents
4. **Default Fallback** - Uses a circle icon if no match is found

This approach means you can use intuitive icon names for common cases, leverage direct platform icons for advanced use
cases, and get consistent results across iOS and Android.

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

On Android, icons render using a lightweight font-based approach that supports the entire Material Icons library. You
can use any Material Icon by its ligature name directly (e.g., `shopping_cart`, `qr_code_2`).

Manual mappings provide convenient aliases for common icon names. For example:

- `home` → `home`
- `settings` → `settings`
- `check` → `check`
- `cart` → `shopping_cart`

## Direct Platform Icons

For advanced use cases, you can use platform-specific icon names directly.

### iOS SF Symbols

On iOS, include a `.` in the icon name to use an SF Symbol path directly:

@verbatim
```blade
<native:bottom-nav-item icon="car.side.fill" ... />
<native:bottom-nav-item icon="flashlight.on.fill" ... />
<native:bottom-nav-item icon="figure.walk" ... />
```
@endverbatim

### Android Material Icons

On Android, use any Material Icon ligature name (with underscores):

@verbatim
```blade
<native:bottom-nav-item icon="qr_code_2" ... />
<native:bottom-nav-item icon="flashlight_on" ... />
<native:bottom-nav-item icon="space_dashboard" ... />
```
@endverbatim

## Platform-Specific Icons

When you need different icons on each platform, use the `System` facade:

@verbatim
```blade
<native:bottom-nav-item
    id="flashlight"
    icon="{{ \Native\Mobile\Facades\System::isIos() ? 'flashlight.on.fill' : 'flashlight_on' }}"
    label="Flashlight"
    url="/flashlight"
/>
```
@endverbatim

This is useful when the mapped icon doesn't match your needs or you want to use platform-specific variants.

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

All icons listed here are manually mapped and guaranteed to work consistently across iOS and Android.

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

## Best Practices

Icons have meaning and most users will associate the visual cues of icons and the underlying behavior or section of an
application across apps. So try to maintain consistent use of icons to help guide users through your app.

- **Stay consistent** - Use the same icon name throughout your app for the same action
- **Test on both platforms** - If you use auto-converted icons, verify they appear correctly on iOS and Android

## Finding Icons

### Android Material Icons

Browse the complete Material Icons library at [Google Fonts Icons](https://fonts.google.com/icons). Use the icon name
exactly as shown (with underscores, e.g., `shopping_cart`, `qr_code_2`).

### iOS SF Symbols

Browse SF Symbols using this [community Figma file](https://www.figma.com/community/file/1549047589273604548). While not
comprehensive, it's a great starting point for discovering available symbols.

For the complete library, download the [SF Symbols app](https://developer.apple.com/sf-symbols/) for macOS.

<aside>

SF Symbol names use dots (e.g., `house.fill`), while Material Icon names use underscores (e.g., `shopping_cart`).

</aside>
