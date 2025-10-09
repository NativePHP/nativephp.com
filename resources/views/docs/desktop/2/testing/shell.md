---
title: Shell
order: 100
---

# Fake Shell

## Example test case

```php
use Native\Desktop\Facades\Shell;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    Shell::fake();

    $this->get('/whatever-action');

    Shell::assertOpenExternal('https://some-url.test');
}
```

## Available assertions

- `assertShowInFolder`
- `assertOpenFile`
- `assertTrashFile`
- `assertOpenExternal`
