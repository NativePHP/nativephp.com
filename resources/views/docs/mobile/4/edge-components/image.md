---
title: Image
order: 250
---

## Overview

Displays an image from a URL. Loaded asynchronously by the native platform — `AsyncImage` on iOS, Coil on Android.

@verbatim
```blade
<native:image
    src="https://picsum.photos/seed/nativephp/400/300"
    :width="200"
    :height="150"
    :fit="2"
    class="rounded-xl"
/>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `src` - Image URL (required, string)
- `fit` - Content fit mode (optional, int, default: `1`):
    - `0` / `1` — fit (scale to fit within bounds, preserving aspect ratio)
    - `2` / `3` — fill (scale to fill bounds, cropping excess)
- `tint-color` - Apply a color tint as hex string (optional)
- `alt` - Accessibility alt text (optional). When set, screen readers announce the image with this label; when
  omitted, the image is treated as decorative and hidden from VoiceOver/TalkBack. See [Accessibility](../digging-deeper/accessibility)

<aside>

The renderer collapses fit modes to two effective behaviors: `fit` and `fill`. Modes `0`/`1` both render as fit;
`2`/`3` both render as fill. Use `:fit="1"` for "scale within" and `:fit="2"` for "scale to fill / crop".

`<native:image />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Basic image

@verbatim
```blade
<native:image src="https://picsum.photos/seed/hero/800/400" class="w-full rounded-xl" :height="200" :fit="2" />
```
@endverbatim

### Rounded avatar

@verbatim
```blade
<native:image
    src="https://i.pravatar.cc/128?img=12"
    :width="64"
    :height="64"
    :fit="2"
    class="rounded-full"
/>
```
@endverbatim

### Tinted icon image

@verbatim
```blade static
<native:image
    src="https://www.php.net/images/logos/new-php-logo.png"
    :width="80"
    :height="42"
    :fit="1"
    tint-color="#7C3AED"
/>
```
@endverbatim

<aside>

Use an image with an alpha channel (a monochrome logo or template asset) for tinting — tinting an opaque photo
just fills the frame with a solid block of color. Tint rendering is still being stabilized across platforms, so
this example is shown as code only.

</aside>

### Image in a card

@verbatim
```blade
<native:column class="w-full p-2 rounded-2xl border border-theme-outline bg-theme-surface">
    <native:image src="https://picsum.photos/seed/cover/800/360" class="w-full rounded-xl" :height="180" :fit="2" />
    <native:column class="p-3 gap-2">
        <native:text class="text-lg font-bold text-theme-on-surface">Article Title</native:text>
        <native:text class="text-base text-theme-on-surface-variant">A brief description of the article.</native:text>
    </native:column>
</native:column>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Image;

Image::make('https://picsum.photos/seed/nativephp/400/300')
    ->fit(2)
    ->tintColor('#7C3AED');
```

- `make(string $src = '')` - Create an image with a source URL
- `fit(int $mode)` - `0`/`1` = fit, `2`/`3` = fill
- `tintColor(string $hex)` - Apply a color tint
