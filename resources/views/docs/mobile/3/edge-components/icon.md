---
title: Icon
order: 330
---

## Overview

Displays a platform-native icon. On iOS, icons render as SF Symbols. On Android, icons render as Material Icons.
A smart mapping system translates common icon names across platforms automatically.

@verbatim
```blade
<native:icon name="home" :size="24" color="#1E293B" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `name` - Icon name (required, string). See the [Icons](icons) reference for available names
- `size` - Icon size in dp (optional, float, default: `24`)
- `color` - Icon color as hex string (optional, default: platform default)

<aside>

`<native:icon />` is a self-closing element. It does not accept children. For a complete list of available icon names
and platform-specific usage, see the [Icons](icons) reference page.

</aside>

## Examples

### Basic icons

@verbatim
```blade
<native:row :gap="16" :align-items="1">
    <native:icon name="home" :size="24" />
    <native:icon name="search" :size="24" />
    <native:icon name="settings" :size="24" />
    <native:icon name="person" :size="24" />
</native:row>
```
@endverbatim

### Colored icon with label

@verbatim
```blade
<native:row :gap="8" :align-items="1">
    <native:icon name="check" :size="20" color="#22C55E" />
    <native:text class="text-base" color="#22C55E">Verified</native:text>
</native:row>
```
@endverbatim

### Large icon

@verbatim
```blade
<native:column center :padding="32">
    <native:icon name="email" :size="64" color="#94A3B8" />
    <native:text class="text-lg text-slate-400">No messages</native:text>
</native:column>
```
@endverbatim

### Platform-specific icon

@verbatim
```blade
<native:icon
    name="{{ \Native\Mobile\Facades\System::isIos() ? 'car.side.fill' : 'directions_car' }}"
    :size="28"
/>
```
@endverbatim
