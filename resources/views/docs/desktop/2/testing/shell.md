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

    Shell::assertOpenedExternal('https://some-url.test');
}
```

## Available assertions

- `assertShowInFolder`
- `assertOpenedFile`
- `assertTrashedFile`
- `assertOpenedExternal`
