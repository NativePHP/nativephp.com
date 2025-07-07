---
title: Haptics
order: 500
---

## Overview

The Haptics API provides access to the device's vibration and haptic feedback system for tactile user interactions.

```php
use Native\Mobile\Facades\Haptics;
```

## Methods

### `vibrate()`

Triggers device vibration for tactile feedback.

**Returns:** `void`

```php
Haptics::vibrate();
```

## Example Usage

### Basic Form Feedback
```php
use Livewire\Component;
use Native\Mobile\Facades\Haptics;

class FormComponent extends Component
{
    public function save()
    {
        if ($this->hasErrors()) {
            // Haptic feedback for errors
            Haptics::vibrate();
            return;
        }

        $this->saveData();
        
        // Success haptic feedback
        Haptics::vibrate();
    }

    public function deleteItem()
    {
        // Haptic feedback for important actions
        Haptics::vibrate();
        $this->performDelete();
    }
}
```

### Best Practices
```php
class HapticsExample extends Component
{
    // ✅ Good: Button presses, form errors, important actions
    public function onButtonPress()
    {
        Haptics::vibrate();
        $this->processAction();
    }

    // ❌ Avoid: Frequent events like scrolling
    public function onScroll()
    {
        // Don't vibrate on every scroll - too annoying!
        // Haptics::vibrate();
    }
}
```

**Use haptics for:** Button presses, form validation, important notifications, game events  
**Avoid haptics for:** Frequent events, background processes, minor updates

## Migration from System Facade

```php
// Old way (deprecated)
use Native\Mobile\Facades\System;
System::vibrate();

// New way (recommended)
use Native\Mobile\Facades\Haptics;
Haptics::vibrate();
```