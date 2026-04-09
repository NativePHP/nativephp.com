---
title: Spacer
order: 240
---

## Overview

A flexible space element that expands to fill remaining space within a column or row. Spacers are the simplest way to
push elements apart without calculating explicit sizes.

@verbatim
```blade
<native:spacer />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. Most commonly used:

- `flex-grow` - How much space to consume relative to other spacers (optional, float, default: `1` implicitly)

<aside>

`<native:spacer />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Push content to bottom

@verbatim
```blade
<native:column fill :padding="16">
    <native:text class="text-2xl font-bold">Welcome</native:text>
    <native:text class="text-base text-slate-500">Get started with your app.</native:text>
    <native:spacer />
    <native:button label="Continue" @press="next" color="#7C3AED" label-color="#FFFFFF" />
</native:column>
```
@endverbatim

### Space between header and content

@verbatim
```blade
<native:row class="w-full px-4" :align-items="1">
    <native:text class="text-xl font-bold">Title</native:text>
    <native:spacer />
    <native:icon name="settings" :size="24" />
</native:row>
```
@endverbatim

### Fixed-height spacer

@verbatim
```blade
<native:column class="w-full">
    <native:text>Section One</native:text>
    <native:spacer class="h-8" />
    <native:text>Section Two</native:text>
</native:column>
```
@endverbatim
