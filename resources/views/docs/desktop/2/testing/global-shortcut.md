---
title: Global Shortcut
order: 100
---

# Fake Global Shortcuts

## Example test case

```php
use Native\Desktop\Facades\GlobalShortcut;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    GlobalShortcut::fake();

    $this->get('/whatever-action');

    GlobalShortcut::assertKey('CmdOrCtrl+,');
    GlobalShortcut::assertRegisteredCount(1);
    GlobalShortcut::assertEvent(OpenPreferencesEvent::class);
}
```

## Available assertions

- `assertKey`
- `assertRegisteredCount`
- `assertUnregisteredCount`
- `assertEvent`
