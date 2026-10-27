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
    fit="cover"
    class="rounded-xl"
/>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `src` - Image URL (required, string)
- `fit` <x-docs.version-badge changed="4.7" /> - How the image fills its frame, following CSS `object-fit`.
  Takes `none`, `contain`, `cover` or `fill`, an `ImageFit` case, or the integer `0`/`1`/`2`/`3` (optional,
  default: `contain`):
    - `none` (`0`) - Draws the image at its own size, one image pixel per point on iOS or per dp on Android,
      centered and cropped to the frame
    - `contain` (`1`) - Scales the image to fit inside the frame, keeping its aspect ratio, and centers it
    - `cover` (`2`) - Scales the image to fill the frame, keeping its aspect ratio, centered and cropped
    - `fill` (`3`) - Stretches the image to the frame's exact width and height, ignoring its aspect ratio
- `tint-color` - Apply a color tint as hex string (optional)
- `alt` - Accessibility alt text (optional). When set, screen readers announce the image with this label; when
  omitted, the image is treated as decorative and hidden from VoiceOver/TalkBack. See [Accessibility](../digging-deeper/accessibility)

<aside>

Before 4.7, `fit` only takes the integer. The `object-none`, `object-contain`, `object-cover` and `object-fill`
[classes](layout#supported-tailwind-classes) set it too.

An image with a width but no height, like a `w-full` image in a list, takes its height from the image's aspect
ratio. That holds for every `fit`.

`<native:image />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Basic image

@verbatim
```blade
<native:image src="https://picsum.photos/seed/hero/800/400" class="w-full rounded-xl" :height="200" fit="cover" />
```
@endverbatim

### Rounded avatar

@verbatim
```blade
<native:image
    src="https://i.pravatar.cc/128?img=12"
    :width="64"
    :height="64"
    fit="cover"
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
    fit="contain"
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
    <native:image src="https://picsum.photos/seed/cover/800/360" class="w-full rounded-xl" :height="180" fit="cover" />
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
use Native\Mobile\Edge\Enums\ImageFit;

Image::make('https://picsum.photos/seed/nativephp/400/300')
    ->fit(ImageFit::Cover)
    ->tintColor('#7C3AED');
```

- `make(string $src = '')` - Create an image with a source URL
- `fit(int|string|ImageFit $mode)` <x-docs.version-badge changed="4.7" /> - `none`/`contain`/`cover`/`fill`, an
  `ImageFit` case, or `0`/`1`/`2`/`3`
- `tintColor(string $hex)` - Apply a color tint
