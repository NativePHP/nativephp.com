---
title: Haptics
order: 800
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

**Use haptics for:** Button presses, form validation, important notifications, game events.

**Avoid haptics for:** Frequent events, background processes, minor updates.

