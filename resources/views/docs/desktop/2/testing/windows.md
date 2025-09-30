---
title: Windows
order: 100
---
# Fake Windows

## Example test case

```php
use Native\Laravel\Facades\Window;
use Illuminate\Support\Facades\Http;

 #[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    Http::fake();
    Window::fake();

    $this->get('/whatever-action');

    Window::assertOpened(fn (string $windowId) => Str::startsWith($windowId, ['window-name']));
    Window::assertClosed('window-name');
    Window::assertHidden('window-name');
}
```

## Available assertions
- `assertOpened`
- `assertClosed`
- `assertHidden`

## Asserting against a window instance (advanced)

```php
use Illuminate\Support\Facades\Http;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\Window as WindowImplementation;
use Mockery;

#[\PHPUnit\Framework\Attributes\Test]
public function example(): void
{
    Http::fake();
    Window::fake();
    Window::alwaysReturnWindows([
        $mockWindow = Mockery::mock(WindowImplementation::class)->makePartial(),
    ]);

    $mockWindow->shouldReceive('route')->once()->with('action')->andReturnSelf();
    $mockWindow->shouldReceive('transparent')->once()->andReturnSelf();
    $mockWindow->shouldReceive('height')->once()->with(500)->andReturnSelf();
    $mockWindow->shouldReceive('width')->once()->with(775)->andReturnSelf();
    $mockWindow->shouldReceive('minHeight')->once()->with(500)->andReturnSelf();
    $mockWindow->shouldReceive('minWidth')->once()->with(775)->andReturnSelf();

    $this->get(route('action'));
}
```
