---
title: Activity Indicator
order: 350
---

## Overview

A native loading spinner. Use this to indicate background activity or loading states. Renders as a platform-native
progress indicator (spinning wheel on iOS, circular indicator on Android).

@verbatim
```blade
<native:activity-indicator />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `size` - Indicator size (optional, float): `0`=default, `1`=large, `2`=small
- `color` - Spinner color as hex string (optional, default: platform default)

<aside>

`<native:activity-indicator />` is a self-closing element. It does not accept children.

</aside>

## Examples

### Centered loading screen

@verbatim
```blade
<native:column fill center>
    <native:activity-indicator :size="1" color="#7C3AED" />
    <native:text class="text-base text-slate-400 mt-4">Loading...</native:text>
</native:column>
```
@endverbatim

### Inline loading

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:activity-indicator :size="2" />
    <native:text class="text-sm text-slate-500">Refreshing</native:text>
</native:row>
```
@endverbatim

### Conditional loading

@verbatim
```blade
@if($loading)
    <native:column fill center>
        <native:activity-indicator color="#3B82F6" />
    </native:column>
@else
    <native:column fill :padding="16">
        {{-- Content --}}
    </native:column>
@endif
```
@endverbatim
