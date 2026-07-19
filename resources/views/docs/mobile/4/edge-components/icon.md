---
title: Icon
order: 230
---

## Overview

Displays a platform-native icon. On iOS, icons render as [SF Symbols](https://developer.apple.com/sf-symbols/);
on Android, as [Material Icons](https://fonts.google.com/icons?icon.set=Material+Icons). You don't need to worry about
the differences — use one consistent icon name and EDGE's smart mapping system translates it to the right platform
symbol automatically.

@verbatim
```blade
<native:icon name="home" :size="24" class="text-theme-primary" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `name` - Icon name (required unless `ios`/`android` are given, string). See the [Icon name reference](#icon-name-reference)
- `ios` / `android` - Per-platform overrides: an [SF Symbol](#ios-sf-symbols) name for iOS and a [Material Icon](#android-material-icons) name
  for Android, so one tag renders the right symbol on each platform. Use in place of `name` when the platforms
  need different icons (`<native:icon ios="gearshape" android="settings" />`). When bound with `:ios` / `:android`,
  these also accept enum cases directly — see [Typed icon enums](#typed-icon-enums)
- `size` - Icon size in dp (optional, float, default: `24`)
- `color` - Icon color as hex string (optional, default: platform default)
- `dark-color` - Icon color when the device is in dark mode, as a hex string (optional). Overrides `color` in dark mode
- `a11y-label` - Accessibility label (optional). Icons are decorative by default — hidden from screen readers
  unless this is set. Label any icon that conveys meaning on its own. See [Accessibility](../digging-deeper/accessibility)

<aside>

`<native:icon />` is a self-closing element. It does not accept children. For a complete list of icon names guaranteed
to work on both platforms, see the [Icon name reference](#icon-name-reference) below.

</aside>

## Examples

### Basic icons

@verbatim
```blade
<native:row :gap="16" :align-items="1">
    <native:icon class="text-theme-primary" name="home" :size="24" />
    <native:icon class="text-theme-primary" name="search" :size="24" />
    <native:icon class="text-theme-primary" name="settings" :size="24" />
    <native:icon class="text-theme-primary" name="person" :size="24" />
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
    <native:icon name="email" :size="64" class="text-theme-on-surface-variant" />
    <native:text class="text-lg text-theme-on-surface-variant">No messages</native:text>
</native:column>
```
@endverbatim

### Platform-specific icons

Give each platform its own symbol with the `ios` / `android` attributes — resolution happens per platform, so one
tag renders the right icon on each:

@verbatim
```blade
<native:icon ios="gearshape" android="settings" :size="28" class="text-theme-primary" />
```
@endverbatim

### Typed icon enums

Bind `:ios` / `:android` to pass enum cases instead of strings for typed, autocompletable symbol names. Import
the enums into your Blade view with `@@use` first — compiled views have no namespace, so a bare `Ios::Gearshape`
won't resolve:

@verbatim
```blade
@use('App\Icons\Ios')
@use('App\Icons\Android')

<native:icon :ios="Ios::Gearshape" :android="Android::Settings" :size="28" class="text-theme-primary"/>
```
@endverbatim

The `AndroidOutlined` enum renders the outlined Material style instead of the filled one:

@verbatim
```blade
@use('App\Icons\Ios')
@use('App\Icons\AndroidOutlined')

<native:icon :ios="Ios::House" :android="AndroidOutlined::Home" :size="28" class="text-theme-primary"/>
```
@endverbatim

You can also combine a shared `name` with a per-platform enum override:

@verbatim
```blade
@use('App\Icons\Ios')

<native:icon name="share" :ios="Ios::SquareAndArrowUp" :size="28" class="text-theme-primary"/>
```
@endverbatim

If you'd rather skip the `@@use` import, fully-qualified cases work anywhere:
`:ios="\App\Icons\Ios::Gearshape"`.

<aside>

Three icon enums are generated into your app by the [native-ui](https://github.com/nativephp/native-ui) plugin:
`App\Icons\Ios` (SF Symbols), `App\Icons\Android` (filled Material Icons), and `App\Icons\AndroidOutlined`
(outlined Material Icons — its cases tell the renderer to use the outlined Material font). Run the command below
once to create them, then reference any symbol as a typed, autocompletable case:

```shell
php artisan native-ui:generate-icons
```

</aside>

## How names resolve

Every icon name — whether passed to `<native:icon>` or to the `icon` attribute of any other EDGE component — goes
through a four-tier resolution strategy:

1. **Direct platform icons** - On iOS, if the name contains a `.` it's used as a direct SF Symbol path (e.g., `car.side.fill`). On Android, any Material Icon ligature name works directly (e.g., `shopping_cart`)
2. **Manual mapping** - Explicit mappings for common icons and aliases (e.g., `home`, `settings`, `user`)
3. **Smart fallback** - Normalizes unmapped icon names to a platform equivalent
4. **Default fallback** - Uses a circle icon if no match is found

This approach means you can use intuitive icon names for common cases, leverage direct platform icons for advanced use
cases, and get consistent results across iOS and Android.

### iOS (SF Symbols)

On iOS, icons render as SF Symbols. Manual mappings convert common icon names to their SF Symbol equivalents.
For example:

- `home` → `house.fill`
- `settings` → `gearshape.fill`
- `check` → `checkmark.circle.fill`

Any name containing a `.` bypasses the mapping and is used as a direct SF Symbol path. Dotted paths are iOS-only,
so pair them with an `android` override:

@verbatim
```blade
<native:icon ios="car.side.fill" android="directions_car" :size="28" class="text-theme-primary" />
```
@endverbatim

If a name isn't manually mapped and isn't a dotted path, the smart fallback lower-cases it and strips dashes and
underscores (`archive-box` → `archivebox`). It deliberately does **not** guess filled or circled variations — many
SF Symbols ship their plain glyph at the bare name, so when you want the filled variant, ask for it explicitly
(`archivebox.fill`, or the `Ios::ArchiveboxFill` enum case).

### Android (Material Icons)

On Android, icons render using a lightweight font-based approach that supports the entire Material Icons library. You
can use any Material Icon by its ligature name directly (e.g., `shopping_cart`, `qr_code_2`) — no mapping required.

Manual mappings provide convenient aliases for common icon names. For example:

- `home` → `home`
- `settings` → `settings`
- `check` → `check`
- `cart` → `shopping_cart`

## Icons in other components

Every EDGE component with an `icon` attribute resolves names through this same system — pass it the same names you
would give `<native:icon>`:

@verbatim
```blade static
<native:bottom-nav-item
    id="home"
    icon="home"
    label="Home"
    url="/home"
/>
```
@endverbatim

The PHP Element builders take per-platform overrides everywhere too: any builder with an icon accepts
`icon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)`, so you can
pass a shared name, `ios:` / `android:` named arguments, or both.

For Blade components whose tag only takes a single `icon` string, pick the platform variant with the `System` facade:

@verbatim
```blade static
<native:bottom-nav-item
    id="flashlight"
    icon="{{ \Native\Mobile\Facades\System::isIos() ? 'flashlight.on.fill' : 'flashlight_on' }}"
    label="Flashlight"
    url="/flashlight"
/>
```
@endverbatim

## Icon name reference

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

## Finding icons

Browse the complete Material Icons library at [Google Fonts Icons](https://fonts.google.com/icons). Use the icon name
exactly as shown (with underscores, e.g., `shopping_cart`, `qr_code_2`).

For the complete SF Symbols library, download the [SF Symbols app](https://developer.apple.com/sf-symbols/) for macOS.
This [community Figma file](https://www.figma.com/community/file/1549047589273604548) is another great starting point,
though not comprehensive.

<aside>

SF Symbol names use dots (e.g., `house.fill`), while Material Icon names use underscores (e.g., `shopping_cart`).

</aside>

Icons carry meaning that users recognize across apps, so stay consistent: use the same icon name for the same action
throughout your app. And if you rely on auto-converted names, test that they appear correctly on both platforms.

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
