---
title: Badge
order: 360
---

## Overview

A small count or text marker, typically used as an overlay on nav items, list rows, or buttons. Renders as a capsule
pill.

Per Model 3, colors come from the theme via the semantic `variant` prop — there are no per-instance overrides.

@verbatim
```blade
<native:badge :count="3" />
```
@endverbatim

## Props

- `count` - Numeric count. Renders as `"99+"` for values above 99 (optional, int)
- `label` - Arbitrary short text. Wins over `count` when both are set (optional, string)
- `variant` - Color variant (optional, string, default: `destructive`):
    - `destructive` — `theme.destructive` / `theme.onDestructive`
    - `primary` — `theme.primary` / `theme.onPrimary`
    - `accent` — `theme.accent` / `theme.onAccent`
- `a11y-label` - Accessibility label (optional)

<aside>

`<native:badge />` is a self-closing element. It does not accept children. For a badge attached to a navigation
icon, see the `badge` and `news` props on [`<native:bottom-nav-item>`](bottom-nav).

</aside>

## Examples

### Count badge

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:icon name="notifications" :size="24" />
    <native:badge :count="$unreadCount" />
</native:row>
```
@endverbatim

### Label badge

@verbatim
```blade
<native:badge label="New" variant="primary" />
```
@endverbatim

### Anchored to an icon

Use a [`<native:stack>`](stack) to layer the badge over its target:

@verbatim
```blade
<native:stack :width="40" :height="40">
    <native:icon name="cart" :size="32" />
    <native:column class="absolute" :top="-2" :right="-2">
        <native:badge :count="$cartItems" />
    </native:column>
</native:stack>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\Badge;

Badge::make()
    ->count(3)
    ->variant('primary');

Badge::make()
    ->label('New')
    ->variant('accent');
```

- `make()` - Create a badge
- `count(int $count)` - Numeric count (capped display at `99+`)
- `label(string $text)` - Short text label (wins over `count`)
- `variant(string $variant)` - `destructive | primary | accent`
- `a11yLabel(string $value)` - Accessibility label
