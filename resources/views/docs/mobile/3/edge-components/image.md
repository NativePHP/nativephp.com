---
title: Image
order: 320
---

## Overview

Displays an image from a URL. Loaded asynchronously by the native platform — `AsyncImage` on iOS, Coil on Android.

@verbatim
```blade
<native:image src="https://example.com/photo.jpg" :width="200" :height="150" :fit="2" :border-radius="12" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `src` - Image URL (required, string)
- `fit` - Content fit mode (optional, int, default: `1`):
    - `0` / `1` — fit (scale to fit within bounds, preserving aspect ratio)
    - `2` / `3` — fill (scale to fill bounds, cropping excess)
- `tint-color` - Apply a color tint as hex string (optional)

<aside>

The renderer collapses fit modes to two effective behaviors: `fit` and `fill`. Modes `0`/`1` both render as fit;
`2`/`3` both render as fill. Use `:fit="1"` for "scale within" and `:fit="2"` for "scale to fill / crop".

`<native:image />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Basic image

@verbatim
```blade
<native:image src="https://example.com/hero.jpg" class="w-full" :height="200" :fit="2" />
```
@endverbatim

### Rounded avatar

@verbatim
```blade
<native:image
    src="https://example.com/avatar.jpg"
    :width="64"
    :height="64"
    :fit="2"
    :border-radius="32"
/>
```
@endverbatim

### Tinted icon image

@verbatim
```blade
<native:image
    src="https://example.com/logo.png"
    :width="40"
    :height="40"
    tint-color="#7C3AED"
/>
```
@endverbatim

### Image in a card

@verbatim
```blade
<native:column class="w-full rounded-2xl" :border-width="1" border-color="#E2E8F0" bg="#FFFFFF">
    <native:image src="https://example.com/cover.jpg" class="w-full" :height="180" :fit="2" />
    <native:column class="p-4 gap-2">
        <native:text class="text-lg font-bold">Article Title</native:text>
        <native:text class="text-base text-slate-500">A brief description of the article.</native:text>
    </native:column>
</native:column>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Image;

Image::make('https://example.com/photo.jpg')
    ->fit(2)
    ->tintColor('#7C3AED');
```

- `make(string $src = '')` - Create an image with a source URL
- `fit(int $mode)` - `0`/`1` = fit, `2`/`3` = fill
- `tintColor(string $hex)` - Apply a color tint
