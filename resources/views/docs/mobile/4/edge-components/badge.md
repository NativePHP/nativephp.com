---
title: Badge
order: 110
---

## Overview

A small count or text marker, typically used as an overlay on nav items, list rows, or buttons. Renders as a capsule
pill.

Colors come from the theme via the semantic `variant` prop — `destructive` (the default), `primary`, or `accent`.
Prefer `variant` for badge colors: it keeps the label legible on both platforms and in both light and dark themes.

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
- `a11y-hint` - Accessibility hint (optional)

<aside>

`<native:badge />` is a self-closing element. It does not accept children. For a badge attached to a navigation
icon, see the `badge` and `news` props on [`<native:bottom-nav-item>`](bottom-nav).

</aside>

## Examples

### Count badge

@verbatim
```blade
<native:row class="gap-2 items-center">
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

Use a [`<native:stack>`](stack) to layer the badge over its target. Size the stack a little larger than the
icon so the badge has a corner to sit in, and position it with positive `top-*` / `right-*` insets — the
renderer anchors an absolute child to whichever edges you set (negative insets are not supported):

@verbatim
```blade
<native:stack :width="40" :height="40">
    <native:icon name="cart" :size="32" />
    <native:column class="absolute top-px right-px">
        <native:badge :count="$cartItems" />
    </native:column>
</native:stack>
```
@endverbatim

### Custom background (iOS only)

On iOS you can override the capsule fill with a `bg-*` class and the radius with a `rounded-*` class:

@verbatim
```blade static
<native:badge label="Beta" class="bg-amber-600 rounded-md" />
```
@endverbatim

<aside>

Per-instance overrides are currently iOS-only: Android ignores `bg-*` and `rounded-*` on badges and always paints
the variant's colors. The label also keeps the variant's on-color (white for the default `destructive`) even over a
custom background, so pick a dark fill like `bg-amber-600` to keep the text legible. Prefer `variant` when you need
colors that work on both platforms.

</aside>

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
- `a11yHint(string $value)` - Accessibility hint
