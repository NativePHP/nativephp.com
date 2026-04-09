---
title: Toggle
order: 510
---

## Overview

A native on/off switch control. Renders as a `UISwitch` on iOS and a Material `Switch` on Android.

@verbatim
```blade
<native:toggle :value="$darkMode" @change="toggleDarkMode" />
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `value` - Current toggle state (optional, boolean, default: `false`)
- `disabled` - Disable the toggle (optional, boolean, default: `false`)

## Events

- `@change` - Livewire method called when the toggle is flipped. Receives the new boolean value as a parameter

<aside>

`<native:toggle />` is a self-closing element. It does not accept children.

</aside>

## Two-way Binding

Use `@model` for automatic two-way binding with a Livewire property.

@verbatim
```blade
<native:toggle @model="notifications" />
```
@endverbatim

## Examples

### Settings list

@verbatim
```blade
<native:column class="w-full gap-0">
    <native:row class="w-full px-4 py-3" :justify-content="3" :align-items="1">
        <native:text class="text-base">Dark Mode</native:text>
        <native:toggle :value="$darkMode" @change="toggleDarkMode" />
    </native:row>
    <native:divider />
    <native:row class="w-full px-4 py-3" :justify-content="3" :align-items="1">
        <native:text class="text-base">Notifications</native:text>
        <native:toggle :value="$notifications" @change="toggleNotifications" />
    </native:row>
    <native:divider />
    <native:row class="w-full px-4 py-3" :justify-content="3" :align-items="1">
        <native:text class="text-base">Location</native:text>
        <native:toggle :value="$location" @change="toggleLocation" disabled />
    </native:row>
</native:column>
```
@endverbatim

### With icon and description

@verbatim
```blade
<native:row class="w-full px-4 py-3 gap-3" :align-items="1">
    <native:icon name="bell" :size="24" color="#7C3AED" />
    <native:column :flex-grow="1" :gap="2">
        <native:text class="text-base font-medium">Push Notifications</native:text>
        <native:text class="text-sm text-slate-400">Receive alerts for new messages</native:text>
    </native:column>
    <native:toggle :value="$pushEnabled" @change="togglePush" />
</native:row>
```
@endverbatim
