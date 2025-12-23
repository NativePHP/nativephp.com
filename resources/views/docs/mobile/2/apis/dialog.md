---
title: Dialog
order: 500
---

## Overview

The Dialog API provides access to native UI elements like alerts, toasts, and sharing interfaces.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Dialog;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { dialog, on, off, Events } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

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

<x-snippet title="Alert Dialog">

<x-snippet.tab name="PHP">

```php
Dialog::alert(
    'Confirm Action',
    'Are you sure you want to delete this item?',
    ['Cancel', 'Delete']
);
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
// Simple usage
await dialog.alert('Confirm Action', 'Are you sure you want to delete this item?', ['Cancel', 'Delete']);

// Fluent builder API
await dialog.alert()
    .title('Confirm Action')
    .message('Are you sure you want to delete this item?')
    .buttons(['Cancel', 'Delete']);

// Quick confirm dialog (OK/Cancel)
await dialog.alert()
    .confirm('Confirm Action', 'Are you sure?');

// Quick destructive confirm (Cancel/Delete)
await dialog.alert()
    .confirmDelete('Delete Item', 'This action cannot be undone.');
```

</x-snippet.tab>
</x-snippet>

### `toast()`

Displays a brief toast notification message.

**Parameters:**
- `string $message` - The message to display

<x-snippet title="Toast Notification">

<x-snippet.tab name="PHP">

```php
Dialog::toast('Item saved successfully!');
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await dialog.toast('Item saved successfully!');
```

</x-snippet.tab>
</x-snippet>

#### Good toast messages

- Short and clear
- Great for confirmations and status updates
- Don't rely on them for critical information
- Avoid showing multiple toasts in quick succession

### `share()`

<aside>

This API has been deprecated. Use the [`Share`](share) facade instead.

</aside>

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

<x-snippet title="ButtonPressed Event">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;

#[OnNative(ButtonPressed::class)]
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

</x-snippet.tab>
<x-snippet.tab name="Vue">

```js
import { dialog, on, off, Events } from '#nativephp';
import { ref, onMounted, onUnmounted } from 'vue';

const buttonLabel = ref('');

const handleButtonPressed = (payload) => {
    const { index, label } = payload;
    buttonLabel.value = label;

    if (index === 0) {
        dialog.toast(`You pressed '${label}'`);
    } else if (index === 1) {
        performAction();
        dialog.toast(`You pressed '${label}'`);
    }
};

onMounted(() => {
    on(Events.Alert.ButtonPressed, handleButtonPressed);
});

onUnmounted(() => {
    off(Events.Alert.ButtonPressed, handleButtonPressed);
});
```

</x-snippet.tab>
<x-snippet.tab name="React">

```jsx
import { dialog, on, off, Events } from '#nativephp';
import { useState, useEffect } from 'react';

const [buttonLabel, setButtonLabel] = useState('');

const handleButtonPressed = (payload) => {
    const { index, label } = payload;
    setButtonLabel(label);

    if (index === 0) {
        dialog.toast(`You pressed '${label}'`);
    } else if (index === 1) {
        performAction();
        dialog.toast(`You pressed '${label}'`);
    }
};

useEffect(() => {
    on(Events.Alert.ButtonPressed, handleButtonPressed);

    return () => {
        off(Events.Alert.ButtonPressed, handleButtonPressed);
    };
}, []);
```

</x-snippet.tab>
</x-snippet>
