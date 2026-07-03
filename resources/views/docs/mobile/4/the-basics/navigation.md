---
title: Navigation
order: 170
---

## Overview

Native screens form a stack, exactly like a native iOS `UINavigationController` or an Android Activity stack. From
inside a `NativeComponent`, you push, pop, and replace screens using methods on `$this`.

## Registering routes

Native routes are registered alongside your web routes using the `Route::native()` macro:

```php
use App\NativeComponents\Home;
use App\NativeComponents\ItemDetail;

Route::native('/', Home::class);
Route::native('/item/{id}', ItemDetail::class);
```

Route parameters work just like Laravel web routes — `{id}` matches a path segment and is exposed to the screen
through `$this->param('id')`.

See [Layouts](layouts) for how to attach shared chrome to a route or group of routes.

## Pushing a new screen

```php
$this->navigate('/item/42');
```

By default a push slides in from the right. Pass arbitrary data along with the navigation:

```php
$this->navigate('/item/42', ['source' => 'home-feed']);
```

The next screen reads the data with `$this->data('source')`.

## Going back

```php
$this->back();
```

Pops the current screen off the stack and returns to whatever was beneath it. The default transition is a slide-out
to the right.

## Replacing the current screen

```php
$this->replace('/login');
```

Pops the current screen and pushes a new one in its place — useful after sign-in / sign-out or for in-place tab
swaps. The default transition is a fade.

<aside>

`<native:bottom-nav-item>` taps automatically use `replace` semantics, so the back chevron pops the entire tabs
section in one step rather than stepping through tab history.

</aside>

## Exiting to the web view

```php
$this->exitToWeb('/dashboard');
```

Tear down the native UI stack and load the given URL in the web view. Use this for screens that aren't part of the
native experience.

## Custom transitions

Chain `transition()` after a navigation method to override the default animation:

```php
use Native\Mobile\Edge\Transition;

$this->navigate('/item/42')->transition(Transition::SlideFromBottom);
```

Available cases:

- `Transition::SlideFromRight` - default for `navigate()`
- `Transition::SlideFromLeft` - default for `back()`
- `Transition::SlideFromBottom` - modal-style presentation
- `Transition::Fade` - default for `replace()`
- `Transition::FadeFromBottom` - subtle vertical fade
- `Transition::ScaleFromCenter` - zoom-in effect
- `Transition::None` - swap with no animation

## Reading params and data

```php
class ItemDetail extends NativeComponent
{
    public function mount(): void
    {
        $id = $this->param('id');                  // from the route URI
        $source = $this->data('source', 'unknown'); // from navigate()'s second arg

        // ...
    }
}
```

Both accessors take an optional default that's returned when the key is missing.

## Resolving named routes

If you've named your routes you can use `route()` on the component to resolve them to URIs:

```php
$this->navigate($this->route('listing.show', ['id' => 5]));
```

This delegates to Laravel's URL generator with `absolute: false`.

## Customizing device-back behavior

By default, the device back button (Android) pops the navigation stack. Override `onBackPressed()` to add custom
behavior:

```php
class CheckoutForm extends NativeComponent
{
    public bool $dirty = false;

    public function onBackPressed(): void
    {
        if ($this->dirty) {
            $this->showDiscardConfirmation();

            return;
        }

        $this->back();
    }
}
```

If the back press should ultimately still pop, call `$this->back()` from your override.
