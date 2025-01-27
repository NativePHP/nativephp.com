---
title: Child Process
order: 100
---
# Fake Child Processes

## Example test case

```php
use Native\Laravel\Facades\ChildProcess;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    ChildProcess::fake();

    $this->get('/whatever-action');

    ChildProcess::assertGet('background-worker');
    ChildProcess::assertMessage(fn (string $message, string|null $alias) => $message === '{"some-payload":"for-the-worker"}' && $alias === null);
}
```

## Available assertions
- `assertGet`
- `assertStarted`
- `assertPhp`
- `assertArtisan`
- `assertStop`
- `assertRestart`
- `assertMessage`
