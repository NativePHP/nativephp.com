---
title: Spacer
order: 390
---

## Overview

A flexible space element that expands to fill remaining space within a column or row. Spacers are the simplest way to
push elements apart without calculating explicit sizes.

@verbatim
```blade static
<native:spacer />
```
@endverbatim

<aside>

`<native:spacer />` is a self-closing element. It does not accept children.

</aside>

## Default behavior

Spacer ships with `flex-grow: 1` applied automatically. Inside a `<native:row>` or `<native:column>`, it claims all
leftover space along the parent's main axis. Inside a non-flex parent (like `<native:stack>`), it does nothing — it has
no children and no intrinsic size to draw.

## Supported Tailwind classes

The classes that affect how a spacer renders:

| Class | Effect |
|---|---|
| `w-N`, `w-[N]`, `w-1/2` etc. | Lock the spacer to a fixed or fractional width |
| `h-N`, `h-[N]` | Lock the spacer to a fixed height |
| `flex-grow`, `flex-grow-0` | Override the default grow=1 (use `flex-grow-0` for a fixed-size spacer) |
| `flex-shrink`, `flex-shrink-0` | Control shrink behavior |
| `bg-*`, `bg-theme-*`, `bg-[#hex]`, alpha `/N` | Paint a visible background |
| `opacity-*` | Adjust visibility |
| `dark:*`, `ios:*`, `android:*` | Variant prefixes |

See the full shared list at [Layout & Styling](layout#supported-tailwind-classes).

## Examples

### Push content to bottom

@verbatim
```blade
<native:column class="w-full h-[220] p-4 bg-theme-surface-variant rounded-xl">
    <native:text class="text-2xl font-bold text-theme-primary">Welcome</native:text>
    <native:text class="text-base text-theme-on-surface-variant">Get started with your app.</native:text>
    <native:spacer />
    <native:button label="Continue" @press="next" />
</native:column>
```
@endverbatim

In a real app this column is usually the page root with `h-full`, so the spacer pushes the button to the bottom of the
screen. The fixed `h-[220]` here just gives the preview a bounded height — a spacer can only grow when its parent's
main axis is constrained.

### Toolbar with right-aligned trailing icon

@verbatim
```blade
<native:row class="w-full px-4 items-center">
    <native:text class="text-xl font-bold text-theme-on-surface-variant">Title</native:text>
    <native:spacer />
    <native:icon name="settings" class="text-theme-on-surface-variant" :size="24" />
</native:row>
```
@endverbatim

### Fixed-height spacer

@verbatim
```blade
<native:column class="w-full">
    <native:text class="text-theme-on-surface-variant">Section One</native:text>
    <native:spacer class="h-8 flex-grow-0" />
    <native:text class="text-theme-on-surface-variant">Section Two</native:text>
</native:column>
```
@endverbatim

<aside>

A fixed-size spacer needs `flex-grow-0` alongside its height (or width, in a row) — without it, the default
`flex-grow: 1` takes precedence over the explicit size. Fixed-size spacers are useful, but a margin on the next
element (`<native:text class="mt-8">…</native:text>`) is often more readable.

</aside>

## Element

```php
use Native\Mobile\Edge\Elements\Spacer;

Spacer::make();                            // flex-grow: 1
Spacer::make()->height(8)->flexGrow(0);    // fixed 8dp vertical
```
