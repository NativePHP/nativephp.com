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

#[On('native:' . ButtonPressed::class)]
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

## Example Usage

```php
use Livewire\Component;
use Livewire\Attributes\On;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Events\Alert\ButtonPressed;

class ItemManager extends Component
{
    public array $items = [];
    public ?int $itemToDelete = null;

    public function deleteItem(int $itemId)
    {
        $this->itemToDelete = $itemId;
        
        Dialog::alert(
            'Delete Item',
            'This action cannot be undone. Are you sure?',
            ['Cancel', 'Delete']
        );
    }

    #[On('native:' . ButtonPressed::class)]
    public function handleDeleteConfirmation($index, $label)
    {
        if ($index === 1 && $this->itemToDelete) {
            // User confirmed deletion
            $this->performDelete($this->itemToDelete);
            Dialog::toast('Item deleted successfully');
            $this->itemToDelete = null;
        } else {
            // User cancelled
            Dialog::toast("You pressed '{$label}'");
            $this->itemToDelete = null;
        }
    }

    public function shareItem(array $item)
    {
        Dialog::share(
            'Share Item',
            "Check out this item: {$item['name']}",
            "https://myapp.com/items/{$item['id']}"
        );
    }

    public function showSuccess(string $message)
    {
        Dialog::toast($message);
    }

    private function performDelete(int $itemId)
    {
        $this->items = array_filter(
            $this->items,
            fn($item) => $item['id'] !== $itemId
        );
    }

    public function render()
    {
        return view('livewire.item-manager');
    }
}
```

## Alert Button Examples

### Simple Confirmation
```php
Dialog::alert(
    'Delete Account',
    'This will permanently delete your account and all data.',
    ['Cancel', 'Delete']
);
```

### Three Button Options
```php
Dialog::alert(
    'Save Changes',
    'Do you want to save your changes before closing?',
    ['Cancel', 'Don\'t Save', 'Save']
);
```

### Single Button Alert
```php
Dialog::alert(
    'Welcome!',
    'Thanks for downloading our app!',
    ['OK']
);
```


## Toast Guidelines

### Best Practices
- Keep messages short and clear
- Use for confirmations and status updates
- Don't rely on toasts for critical information
- Avoid showing multiple toasts in quick succession

```php
// Good toast messages
Dialog::toast('Saved!');
Dialog::toast('Photo uploaded');
Dialog::toast('Settings updated');

// Avoid long messages
Dialog::toast('Your photo has been successfully uploaded to the server and will be processed shortly');
```

## Sharing Content

### Supported Content Types
- Plain text
- URLs
- Images (when sharing files)
- Mixed content

```php
// Share just text
Dialog::share('', 'Check out this amazing app!', '');

// Share a URL
Dialog::share('', '', 'https://nativephp.com');

// Share text and URL together
Dialog::share(
    'NativePHP for Mobile',
    'Build mobile apps with PHP and Laravel!',
    'https://nativephp.com'
);
```

## Platform Differences

### iOS
- Alerts use UIAlertController
- Toasts use custom overlay views
- Sharing uses UIActivityViewController

### Android
- Alerts use AlertDialog
- Toasts use native Toast system
- Sharing uses Intent.ACTION_SEND

## Accessibility

- All dialogs automatically support screen readers
- Button text should be descriptive
- Toast messages are announced by accessibility services
- Consider users with motor disabilities when designing button layouts
