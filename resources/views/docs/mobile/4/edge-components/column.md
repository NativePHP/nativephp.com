---
title: Column
order: 210
---

## Overview

A vertical flex container that stacks its children from top to bottom. This is the most commonly used layout element
and serves as the foundation for most screen layouts — think of it as the mobile equivalent of `<div>`.

@verbatim
```blade
<native:column class="p-4 gap-3 w-full h-full">
    <native:text>First item</native:text>
    <native:text>Second item</native:text>
    <native:text>Third item</native:text>
</native:column>
```
@endverbatim

## Children

Accepts any EDGE elements as children. Children are arranged vertically from top to bottom.

## Supported Tailwind classes

Column inherits the full class set documented at [Layout & Styling](layout#supported-tailwind-classes). The classes
that shape how a column behaves specifically:

| Class | Effect on a column |
|---|---|
| `gap-N` | **Vertical** spacing between children |
| `items-*` | **Horizontal** (cross-axis) alignment of children: `items-start`, `items-center`, `items-end`, `items-stretch` |
| `justify-*` | **Vertical** (main-axis) distribution: `justify-start`, `justify-center`, `justify-end`, `justify-between`, `justify-around`, `justify-evenly` |
| `flex-1` | Fills remaining space in the parent flex container |
| `safe-area`, `safe-area-top`, `safe-area-bottom` | Respect device safe-area insets (typical at page root) |

Everything else from the shared list applies the same as on any element (`w-*`, `h-*`, `p-*`, `m-*`, `bg-*`,
`rounded-*`, `shadow-*`, `dark:*`, `ios:*` / `android:*`, `glass:*`, alpha suffix `/N`, arbitrary `prefix-[value]`).

## Examples

### Full-screen layout with safe area

A column at the page root typically fills the screen and pushes actions to the bottom with a spacer:

@verbatim
```blade
<native:column class="w-full h-[220] p-4 bg-theme-background rounded-xl border border-theme-outline">
    <native:text class="text-2xl font-bold">My App</native:text>
    <native:spacer />
    <native:button label="Get Started" @press="start" />
</native:column>
```
@endverbatim

The full-screen version below adds `safe-area` at the page root so content clears the notch and home
indicator — run it in your app to see it edge-to-edge:

@verbatim
```blade static
<native:column class="w-full h-full safe-area bg-theme-background">
    <native:text class="text-2xl font-bold">My App</native:text>
    <native:spacer />
    <native:button label="Get Started" @press="start" />
</native:column>
```
@endverbatim

### Centered content

@verbatim
```blade
<native:column class="w-full h-full items-center justify-center gap-2">
    <native:activity-indicator />
    <native:text>Loading...</native:text>
</native:column>
```
@endverbatim

### Surface-styled layout

@verbatim
```blade
<native:column class="w-full p-4 gap-3 bg-theme-surface rounded-2xl border border-theme-outline">
    <native:text class="text-lg font-bold text-theme-on-surface">Section Title</native:text>
    <native:text class="text-base text-theme-on-surface-variant">Surface description goes here.</native:text>
    <native:row class="gap-2 justify-end">
        <native:button label="Cancel" @press="cancel" variant="ghost" />
        <native:button label="Confirm" @press="confirm" />
    </native:row>
</native:column>
```
@endverbatim

### Space-between distribution

`justify-between` spreads children across the column's height, placing the leftover space between them:

@verbatim
```blade
<native:column class="w-full h-[220] p-4 justify-between bg-theme-surface-variant rounded-xl">
    <native:text class="text-theme-on-surface">Top</native:text>
    <native:text class="text-theme-on-surface">Middle</native:text>
    <native:text class="text-theme-on-surface">Bottom</native:text>
</native:column>
```
@endverbatim

Distribution needs a bounded height to work with — on a real screen you would typically use `h-full` at the
page root; the fixed `h-[220]` here just gives the preview a bounded height to distribute.

## Element

```php
use Native\Mobile\Edge\Elements\Column;
use Native\Mobile\Edge\Elements\Text;

Column::make(
    Text::make('First'),
    Text::make('Second'),
)->fill()->padding(16)->gap(12);
```

- `make(Element ...$children)` - Create a column with children. Layout / style fluent methods are inherited from
  the base `Element` class — see [Layout & Styling](layout)
