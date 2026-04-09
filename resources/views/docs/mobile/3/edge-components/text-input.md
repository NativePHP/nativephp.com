---
title: Text Input
order: 500
---

## Overview

A native text input field for user input. Supports placeholders, labels, secure entry for passwords, multiline mode,
keyboard type selection, and validation states.

@verbatim
```blade
<native:text-input
    placeholder="Enter your name"
    :value="$name"
    @change="updateName"
/>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `value` - Current text value (optional, string)
- `placeholder` - Placeholder text shown when empty (optional, string)
- `label` - Floating label text (optional, string)
- `secure` - Mask input for passwords (optional, boolean, default: `false`)
- `multiline` - Allow multiple lines of text (optional, boolean, default: `false`)
- `disabled` - Disable the input (optional, boolean, default: `false`)
- `read-only` - Make the input read-only (optional, boolean, default: `false`)
- `is-error` - Show error styling (optional, boolean, default: `false`)
- `keyboard` - Keyboard type (optional, int): `0`=default, `1`=number, `2`=email, `3`=phone, `4`=URL
- `max-length` - Maximum character count (optional, int)
- `max-lines` - Maximum visible lines (optional, int)
- `min-lines` - Minimum visible lines (optional, int)
- `variant` - Input style (optional, int): `0`=outlined, `1`=filled
- `font-size` - Text size (optional, float)
- `font-weight` - Text weight 1-7 (optional, int)
- `color` - Accent/cursor color as hex (optional)
- `text-color` - Input text color as hex (optional)
- `container-color` - Background color as hex (optional)
- `label-color` - Label text color as hex (optional)

### Decorations

- `prefix` - Text displayed before the input (optional, string)
- `suffix` - Text displayed after the input (optional, string)
- `supporting` - Helper text displayed below the input (optional, string)
- `supporting-color` - Helper text color as hex (optional)
- `leading-icon` - Icon name displayed at the start (optional, string)
- `trailing-icon` - Icon name displayed at the end (optional, string)

## Events

- `@change` - Livewire method called when the text value changes. Receives the new text value as a parameter
- `@submit` - Livewire method called when the user submits (e.g. presses return). Receives the text value as a parameter

<aside>

`<native:text-input />` is a self-closing element. It does not accept children.

</aside>

## Two-way Binding

Use the `@model` directive for automatic two-way binding with a Livewire property. This is shorthand for setting
`:value` and `@change` together.

@verbatim
```blade
<native:text-input placeholder="Your name" @model="name" />
```
@endverbatim

This is equivalent to:

@verbatim
```blade
<native:text-input placeholder="Your name" :value="$name" @change="__syncProperty('name')" />
```
@endverbatim

## Examples

### Login form

@verbatim
```blade
<native:column class="w-full gap-4 p-4">
    <native:text-input
        label="Email"
        placeholder="you@example.com"
        :value="$email"
        @change="updateEmail"
        :keyboard="2"
        leading-icon="email"
    />
    <native:text-input
        label="Password"
        placeholder="Enter password"
        :value="$password"
        @change="updatePassword"
        secure
        leading-icon="lock"
    />
    <native:button label="Sign In" @press="login" color="#7C3AED" label-color="#FFFFFF" />
</native:column>
```
@endverbatim

### With validation error

@verbatim
```blade
<native:text-input
    label="Email"
    :value="$email"
    @change="updateEmail"
    is-error
    supporting="Please enter a valid email address"
    supporting-color="#EF4444"
/>
```
@endverbatim

### Multiline textarea

@verbatim
```blade
<native:text-input
    label="Message"
    placeholder="Type your message..."
    :value="$message"
    @change="updateMessage"
    multiline
    :min-lines="3"
    :max-lines="8"
/>
```
@endverbatim

### Search field

@verbatim
```blade
<native:text-input
    placeholder="Search..."
    :value="$query"
    @change="search"
    @submit="submitSearch"
    leading-icon="search"
    :variant="1"
/>
```
@endverbatim

### With prefix and suffix

@verbatim
```blade
<native:text-input
    label="Price"
    :value="$price"
    @change="updatePrice"
    prefix="$"
    suffix=".00"
    :keyboard="1"
/>
```
@endverbatim
