---
title: Queue Worker
order: 100
---
# Fake Queue Worker

## Example test case

```php
use Native\Laravel\Facades\QueueWorker;
use Native\Laravel\DTOs\QueueConfig;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    QueueWorker::fake();

    $this->get('/whatever-action');

    QueueWorker::assertUp(fn (QueueConfig $config) => $config->alias === 'custom');
}
```

## Available assertions
- `assertUp`
- `assertDown`
