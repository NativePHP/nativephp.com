---
title: Divider
order: 220
---

## Overview

A thin horizontal line separator. Renders as a 1pt rule. Color resolves from the `border-*` class if set, otherwise
the platform separator color (`UIColor.separator` on iOS, Material `outlineVariant` on Android).

@verbatim
```blade
<native:divider />
```
@endverbatim

`<native:horizontal-divider />` is an alias of `<native:divider />` exposed for use inside [side navigation](side-nav).

<aside>

`<native:divider />` is a self-closing element. It does not accept children.

The line is **always 1pt high**. For thicker rules, drop in a styled column instead:
`<native:column class="h-px bg-zinc-200" />` for 1px, `<native:column class="h-1 bg-zinc-200" />` for 4dp, etc.

</aside>

## Supported Tailwind classes

The classes that affect how a divider renders:

| Class | Effect |
|---|---|
| `border-{palette}-{shade}`, `border-[#hex]`, `border-theme-{token}` | Line color |
| `opacity-*`, `opacity-[0.5]` | Line opacity |
| `m-*`, `mx-*`, `my-*`, `mt-*` / `mr-*` / `mb-*` / `ml-*` | Spacing around the divider |
| `dark:border-*` | Dark-mode color override |
| `ios:border-*`, `android:border-*` | Platform-specific color |

## Examples

### Basic separator

@verbatim
```blade
<native:column class="w-full gap-4 p-4">
    <native:text class="text-lg font-bold">Section One</native:text>
    <native:text>Some content here.</native:text>
    <native:divider />
    <native:text class="text-lg font-bold">Section Two</native:text>
    <native:text>More content here.</native:text>
</native:column>
```
@endverbatim

### Themed divider with margin

@verbatim
```blade
<native:divider class="border-theme-outline my-2 mx-4" />
```
@endverbatim

### In a list

@verbatim
```blade
<native:column class="w-full">
    @foreach($items as $item)
        <native:column class="w-full p-4">
            <native:text class="text-base">{{ $item->name }}</native:text>
        </native:column>
        @unless($loop->last)
            <native:divider />
        @endunless
    @endforeach
</native:column>
```
@endverbatim

### Thicker rule (use a column)

@verbatim
```blade
<native:column class="w-full h-1 bg-theme-outline my-4" />
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Divider;

Divider::make()->borderColor('#E2E8F0');
```

- `make()` - Create a divider
