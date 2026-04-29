---
title: Text Input
order: 500
---

## Overview

Native text input fields come in two variants:

- `<native:outlined-text-input>` — bordered field. Default, lower emphasis.
- `<native:filled-text-input>` — surface-fill background + bottom indicator line. Higher emphasis.

Both share the same prop set and event API. Choose based on emphasis, not behavior.

On iOS they render as SwiftUI `TextField` / `SecureField` with Material3-style chrome; on Android they map to
`OutlinedTextField` / `TextField` (filled). Per Model 3 there are no per-instance color, font, or border overrides
— all chrome resolves from the theme. For fully custom input visuals drop to [`<native:pressable>`](pressable)
wrapping your own drawing.

@verbatim
```blade
<native:outlined-text-input
    label="Email"
    placeholder="you@example.com"
    native:model="email"
    keyboard="email"
    leading-icon="email"
/>
```
@endverbatim

## Props

Both variants accept identical props.

### Content

- `value` - Current text value (optional, string)
- `placeholder` - Placeholder shown when empty (optional, string)
- `label` - Label rendered above the field (optional, string)
- `supporting` - Helper text rendered below the field (optional, string)

### State

- `disabled` - Disable the input (optional, boolean, default: `false`)
- `read-only` - Make the input read-only (optional, boolean, default: `false`)
- `is-error` - Show error styling (border / indicator + supporting text turn `theme.destructive`)
- `loading` - Show a spinner in the trailing position (optional, boolean, default: `false`)

### Behavior

- `keyboard` - Keyboard hint string: `text` (default), `number`, `email`, `phone`, `url`, `decimal`, `numberPassword`
- `secure` - Mask input for passwords (optional, boolean, default: `false`)
- `multiline` - Allow multiple lines (optional, boolean, default: `false`)
- `max-length` - Maximum character count (optional, int)
- `max-lines` - Maximum visible lines when `multiline` (optional, int)
- `min-lines` - Minimum visible lines when `multiline` (optional, int)

### Decorations

- `prefix` - Text rendered before the input (optional, string)
- `suffix` - Text rendered after the input (optional, string)
- `leading-icon` - Icon name rendered at the start (optional, string)
- `trailing-icon` - Icon name rendered at the end (optional, string)

### Sizing & accessibility

- `size` - `sm | md (default) | lg`
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Livewire method called when the text changes. Receives the new value
- `@submit` - Livewire method called when the user submits (e.g. presses return). Receives the current value

<aside>

Both variants are self-closing. They do not accept children.

</aside>

## Two-way Binding

Use the `native:model` directive for automatic two-way binding with a Livewire property. The directive expands to
`:value`, `@change="__syncProperty(...)"`, and a `sync-mode` prop driven by the modifier chain.

@verbatim
```blade
<native:outlined-text-input native:model="name" />
<native:outlined-text-input native:model.blur="email" />
<native:outlined-text-input native:model.debounce.500ms="search" />
```
@endverbatim

`sync-mode` semantics:

- `live` (default) — every keystroke fires `@change`
- `blur` — only fires on focus loss / submit
- `debounce` — fires after `debounce_ms` of inactivity, or immediately on blur / submit

## Examples

### Login form

@verbatim
```blade
<native:column class="w-full gap-4 p-4">
    <native:outlined-text-input
        label="Email"
        placeholder="you@example.com"
        native:model="email"
        keyboard="email"
        leading-icon="email"
    />
    <native:outlined-text-input
        label="Password"
        placeholder="Enter password"
        native:model="password"
        secure
        leading-icon="lock"
    />
    <native:button label="Sign In" @press="login" />
</native:column>
```
@endverbatim

### Filled variant with validation error

@verbatim
```blade
<native:filled-text-input
    label="Email"
    native:model="email"
    is-error
    supporting="Please enter a valid email address"
/>
```
@endverbatim

### Multiline textarea

@verbatim
```blade
<native:outlined-text-input
    label="Message"
    placeholder="Type your message..."
    native:model="message"
    multiline
    :min-lines="3"
    :max-lines="8"
/>
```
@endverbatim

### Search with submit

@verbatim
```blade
<native:filled-text-input
    placeholder="Search..."
    native:model.debounce.300ms="query"
    @submit="submitSearch"
    leading-icon="search"
/>
```
@endverbatim

### Prefix and suffix

@verbatim
```blade
<native:outlined-text-input
    label="Price"
    native:model="price"
    prefix="$"
    suffix=".00"
    keyboard="decimal"
/>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\OutlinedTextInput;
use Nativephp\NativeUi\Elements\FilledTextInput;

OutlinedTextInput::make()
    ->label('Email')
    ->placeholder('you@example.com')
    ->value($email)
    ->keyboard('email')
    ->leadingIcon('email')
    ->onChange('updateEmail');
```

Both elements share the same fluent API (defined on `BaseTextInput`):

- `value(string $text)`, `placeholder(string $text)`, `label(string $text)`, `supporting(string $text)`
- `disabled(bool $value = true)`, `readOnly(bool $value = true)`, `error(bool $value = true)`, `loading(bool $value = true)`
- `keyboard(string|int $type)`, `secure(bool $value = true)`, `maxLength(int $length)`
- `multiline(bool $value = true)`, `maxLines(int $lines)`, `minLines(int $lines)`
- `prefix(string $text)`, `suffix(string $text)`, `leadingIcon(string $name)`, `trailingIcon(string $name)`
- `size(string $value)` - `sm | md | lg`
- `a11yLabel(string $value)`, `a11yHint(string $value)`
- `syncMode(string $mode)`, `debounceMs(int $ms)`
- `onChange(string $method)`, `onSubmit(string $method)`
