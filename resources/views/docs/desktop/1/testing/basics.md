---
title: Basics
order: 99
---
# Understanding fake test doubles
When working with a NativePHP application, you may encounter an elevated level of difficulty when writing tests for your code.
This is because NativePHP relies on an Electron application to be open at all times, listening to HTTP requests. Obviously,
emulating this in a test environment can be cumbersome. You will often hit an HTTP error, and this is normal. This is where
NativePHP's fake test doubles come in.

```php
use Native\Laravel\Facades\Window;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    Window::fake();

    $this->get('/whatever-action');

    Window::assertOpened('window-name');
}
```

## Where have I seen this before?

If you've ever written tests for a Laravel application, you may have seen the `*::fake()` method available on
all sorts of facades. Under the hood, these methods are swapping the real implementation and behavior – in NativePHP's case,
an HTTP call that forces us to keep the server up and running, in turn degrading the ability to write expressive tests – with a fake one 
that follows the same API. This means you do not have to change any of your code to write great tests.
