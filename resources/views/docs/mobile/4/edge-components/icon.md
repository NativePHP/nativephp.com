---
title: Icon
order: 230
---

## Overview

Displays a platform-native icon. On iOS, icons render as [SF Symbols](https://developer.apple.com/sf-symbols/);
on Android, as [Material Icons](https://fonts.google.com/icons?icon.set=Material+Icons). You don't need to worry about
the differences — use one consistent icon name and EDGE's smart mapping system translates it to the right platform
symbol automatically. When no system icon fits, you can ship [your own SVG icons](#custom-icons).

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
<native:row :gap="16" align-items="center">
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
<native:row :gap="8" align-items="center">
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

A name that starts with `custom:` points at one of your [custom icons](#custom-icons) instead.

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

## Custom icons

<x-docs.version-badge since="4.7" /> <x-docs.version-badge package="mobile-ui" since="0.9" />

Ship your own icons as SVG files. When NativePHP builds your app, it turns each one into a native icon: a template image
in the iOS asset catalog, with its vector data kept, and a VectorDrawable on Android.

Custom icons are tinted in one color, the same way system icons are. They take the color you give `<native:icon>`, and
a tab follows the bar's active and inactive colors. You can use them in tab bars, top-bar actions and their menus,
`<native:icon>`, and the icon props of other EDGE components such as buttons and list items.

Tab bars, top-bar actions and their menus need `nativephp/mobile` 4.7. `<native:icon>` and icons in other EDGE
components also need `nativephp/mobile-ui` 0.9.

### Adding an icon

Save each icon as `resources/icons/<name>.svg`, directly in that folder rather than in a subfolder. The file name is the
icon's name. It must be lowercase kebab-case: letters, numbers and single hyphens, such as `git-commit-vertical`. A
`.svg` file with any other name fails the build. Files that aren't `.svg` are ignored.

Here's `resources/icons/git-commit-vertical.svg`, downloaded from [Lucide](https://lucide.dev):

```xml
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 3v6" />
    <circle cx="12" cy="12" r="3" />
    <path d="M12 15v6" />
</svg>
```

<aside>

Icons are built into the native project, so adding or changing an SVG needs a native build. Run
`php artisan native:run` to see it. Hot reload won't pick up icon changes.

</aside>

### Using an icon

Pass `custom:<name>` anywhere an icon name goes:

@verbatim
```blade static
<native:icon name="custom:git-commit-vertical" :size="20" />
```
@endverbatim

The same string works in any `icon` attribute, such as `<native:bottom-nav-item icon="custom:git-commit-vertical">`.

In PHP, `Icon::custom()` builds that string for you. It throws an `InvalidArgumentException` if the name isn't
lowercase kebab-case:

```php
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Edge\Layouts\Builders\Tab;
use Native\Mobile\Icon\Icon;

// A tab in your layout's tabBar()
Tab::link('Log', '/log', Icon::custom('git-commit-vertical'));

// A top-bar action, or an item in its menu
NavAction::make('star')->icon(Icon::custom('star'))->press('toggleStar');
```

`Icon::custom('star')` returns the string `'custom:star'`, so you can pass the string itself if you prefer. This
`Icon` class shares its short name with the [`Icon` element](#element). If a file needs both, alias one of them:
`use Native\Mobile\Icon\Icon as CustomIcon;`.

### Per-platform custom icons

The `ios` and `android` overrides take custom icons too, so you can use your icon on one platform and keep the system
icon on the other:

```php
use App\Icons\AndroidOutlined;
use Native\Mobile\Edge\Layouts\Builders\NavAction;
use Native\Mobile\Icon\Icon;

// Your bell on iOS, the outlined Material bell on Android
NavAction::make('alerts')->icon(Icon::custom('bell'), android: AndroidOutlined::Notifications);

// The system bell on Android, your bell on iOS
NavAction::make('alerts')->icon('bell', ios: Icon::custom('bell'));
```

In Blade, pass the string:

@verbatim
```blade static
<native:icon name="bell" ios="custom:bell" />
```
@endverbatim

### Which SVGs work

SVGs downloaded from Lucide, [Tabler](https://tabler.io/icons) and similar stroked sets, or from
[Material Symbols](https://fonts.google.com/icons), work as they are. NativePHP supports:

- `path`, `circle`, `ellipse`, `line`, `rect` (including rounded corners), `polyline` and `polygon`, with or without
  `g` groups around them
- Strokes as well as fills. Lucide's style of `stroke-width="2"`, round caps and joins, `fill="none"` and
  `currentColor` is kept
- The presentation attributes `fill`, `stroke`, `stroke-width`, `stroke-linecap`, `stroke-linejoin`,
  `stroke-miterlimit`, `fill-rule`, `opacity`, `fill-opacity` and `stroke-opacity`, set on the shape or inherited from
  a parent `svg` or `g`. They also work inside a `style` attribute
- Any `viewBox`. Icons are scaled so their longer side is 24pt on iOS and 24dp on Android, so a 24×24 Lucide icon and
  a 960×960 Material Symbols icon come out the same size. A file without a `viewBox` needs numeric `width` and `height`

`title`, `desc`, comments and an empty `defs` are ignored. Because icons are tinted in one color, the color in the file
doesn't matter as long as there's only one: `currentColor`, `black` and any single hex or `rgb()` color all become the
tint.

Anything else fails the build with an error that names the file, the element and the attribute. That includes:

- `transform` attributes
- Gradients, patterns, masks, clip paths and filters
- `use`, `symbol` and `marker` elements, and anything inside `defs`
- `text`, `image` and `foreignObject` elements
- `<style>` elements, and any property in a `style` attribute other than the presentation attributes above
- More than one color in the same icon

To fix one, flatten it to plain paths in a single color in your design tool, then export it again.

<aside>

Lucide is released under the ISC license. Whichever icon set you use, check its license and credit it the way it asks.

</aside>

### Missing icons

When a build has no asset for a custom icon, NativePHP logs a warning and draws the system icon with the same name,
using the usual [name resolution](#how-names-resolve). So `custom:bell` shows the system bell. This happens in
[Jump](../the-basics/jump), which can't include your app's icons, and in any build made before you added the SVG.

Naming an icon after the system icon it stands in for gives you a sensible fallback.

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
