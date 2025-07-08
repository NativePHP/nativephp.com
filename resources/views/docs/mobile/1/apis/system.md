---
title: System
order: 800
---

## Overview

The System API provides access to basic system functions like flashlight control.

```php
use Native\Mobile\Facades\System;
```

## Methods

### `flashlight()`

Toggles the device flashlight (camera flash LED) on and off.

**Returns:** `void`

```php
System::flashlight(); // Toggle flashlight state
```

## Example Usage

```php
use Livewire\Component;
use Native\Mobile\Facades\System;

class FlashlightController extends Component
{
    public bool $isFlashlightOn = false;

    public function toggleFlashlight()
    {
        System::flashlight();
        $this->isFlashlightOn = !$this->isFlashlightOn;
    }

    public function render()
    {
        return view('livewire.flashlight-controller');
    }
}
```

## Platform Support

### Flashlight
- **iOS:** Controls camera flash LED
- **Android:** Controls camera flash LED
- **Permissions:** None required
- **Limitations:** May not work if camera is currently in use