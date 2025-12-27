---
title: Haptics
order: 800
---

## Overview

The Haptics API provides access to the device's vibration and haptic feedback system for tactile user interactions.

<x-snippet title="Import">

<x-snippet.tab name="PHP">

```php
use Native\Mobile\Facades\Haptics;
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
import { device } from '#nativephp';
```

</x-snippet.tab>
</x-snippet>

## Methods

### `vibrate()`

Triggers device vibration for tactile feedback.

**Returns:** `void`

<x-snippet title="Vibrate">

<x-snippet.tab name="PHP">

```php
Haptics::vibrate();
```

</x-snippet.tab>
<x-snippet.tab name="JS">

```js
await device.vibrate();
```

</x-snippet.tab>
</x-snippet>

**Use haptics for:** Button presses, form validation, important notifications, game events.

**Avoid haptics for:** Frequent events, background processes, minor updates.

