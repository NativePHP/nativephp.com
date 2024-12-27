---
title: Power Monitor
order: 100
---
# Fake Power Monitor

## Example test case

```php
use Native\Laravel\Facades\PowerMonitor;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    PowerMonitor::fake();

    $this->get('/whatever-action');

    PowerMonitor::assertGetSystemIdleState('...');
}
```

## Available assertions
- `assertGetSystemIdleState`
- `assertGetSystemIdleStateCount`
- `assertGetSystemIdleTimeCount`
- `assertGetCurrentThermalStateCount`
- `assertIsOnBatteryPowerCount`
