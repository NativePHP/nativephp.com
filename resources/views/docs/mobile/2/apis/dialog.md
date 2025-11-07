---
title: Dialog
order: 300
---

## Overview

The Dialog API provides access to native UI elements like alerts, toasts, and sharing interfaces.

```php
use Native\Mobile\Facades\Dialog;
```

## Methods

### `alert()`

Displays a native alert dialog with customizable buttons.

**Parameters:**
- `string $title` - The alert title
- `string $message` - The alert message
- `array $buttons` - Array of button labels (max 3 buttons)

**Button Positioning:**
- **1 button** - Positive (OK/Confirm)
- **2 buttons** - Negative (Cancel) + Positive (OK/Confirm) 
- **3 buttons** - Negative (Cancel) + Neutral (Maybe) + Positive (OK/Confirm)

```php
Dialog::alert(
    'Confirm Action',
    'Are you sure you want to delete this item?',
    ['Cancel', 'Delete']
);
```

### `toast()`

Displays a brief toast notification message.


**Parameters:**
- `string $message` - The message to display

```php
Dialog::toast('Item saved successfully!');
```

#### Good toast messages

- Short and clear
- Great for confirmations and status updates
- Don't rely on them for critical information
- Avoid showing multiple toasts in quick succession

### `share()`

Opens the native sharing interface.

**Parameters:**
- `string $title` - The share dialog title
- `string $text` - Text content to share
- `string $url` - URL to share

```php
Dialog::share(
    'Check this out!',
    'I found this amazing Laravel package for mobile development',
    'https://nativephp.com'
);
```

## Events

### `ButtonPressed`

Fired when a button is pressed in an alert dialog.

**Payload:** 
- `int $index` - Index of the pressed button (0-based)
- `string $label` - Label/text of the pressed button

```php
use Livewire\Attributes\On;
use Native\Mobile\Events\Alert\ButtonPressed;

#[On('native:'.ButtonPressed::class)]
public function handleAlertButton($index, $label)
{
    switch ($index) {
        case 0:
            // First button (usually Cancel)
            Dialog::toast("You pressed '{$label}'");
            break;
        case 1:
            // Second button (usually OK/Confirm)
            $this->performAction();
            Dialog::toast("You pressed '{$label}'");
            break;
    }
}
```
