---
title: Image
order: 320
---

## Overview

Displays an image from a URL. Supports multiple content fit modes and optional tinting. Images are loaded
asynchronously by the native platform.

@verbatim
```blade
<native:image src="https://example.com/photo.jpg" :width="200" :height="150" :fit="2" :border-radius="12" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `src` - Image URL (required, string)
- `fit` - Content fit mode (optional, int, default: `1`):
  - `0` - None (original size)
  - `1` - Fit (scale to fit within bounds, preserving aspect ratio)
  - `2` - Crop (scale to fill bounds, cropping excess)
  - `3` - Fill (stretch to fill bounds, may distort)
- `tint-color` - Apply a color tint as hex string (optional)

<aside>

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
