---
title: Routing
order: 152
---

## Overview

Native screens form a stack, exactly like a native iOS `UINavigationController` or an Android Activity stack. From
inside a `NativeComponent`, you push, pop, and replace screens using methods on `$this`.

## Registering routes

Native routes are registered similarly to Livewire routes, but instead of using `Route::livewire()` you use the
`Route::native()` macro:

```php
use App\NativeComponents\Home;
use App\NativeComponents\ItemDetail;

Route::native('/', Home::class);
Route::native('/item/{id}', ItemDetail::class);
```

You can put these routes anywhere that makes sense for your application, but you may find that creating a
`routes/mobile.php` to keep them all together is a clean option.

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
- `Transition::ParallaxPush` - incoming screen slides in from the right while the outgoing screen drifts partially left underneath
- `Transition::Morph` <x-docs.version-badge since="4.7" /> - cross-fade while elements that share a `ref` move between the two screens. See [Morphing elements between screens](#morphing-elements-between-screens)
- `Transition::None` - swap with no animation

## Navigating from Blade

The `@navigate` directive triggers navigation directly from your view, with no component method required. It works on
any node, so any element can become a navigation target.

The short form takes a route in single quotes:

@verbatim
```blade
<native:pressable @navigate='/item/42' class="w-full p-4">
    <native:text>View item</native:text>
</native:pressable>
```
@endverbatim

To pass data along with the navigation, use the args form — a route followed by an array:

@verbatim
```blade
<native:pressable @navigate="'/item/42', ['source' => 'home-feed']">
    <native:text>View item</native:text>
</native:pressable>
```
@endverbatim

The next screen reads the data with `$this->data('source')`, exactly as with `$this->navigate()`.

Double quotes hold a PHP expression rather than a plain route, so `@navigate="/item/42"` won't compile. For a
dynamic route, build the string in PHP: `@navigate="'/item/' . $item->id"`.

### Transition modifiers

Add a transition as a modifier to override the default push animation:

@verbatim
```blade
<native:pressable @navigate.slideFromBottom='/item/42'>
    <native:text>Present modally</native:text>
</native:pressable>
```
@endverbatim

Available transition modifiers:

- `fade`
- `slideFromRight`
- `slideFromLeft`
- `slideFromBottom`
- `fadeFromBottom`
- `scaleFromCenter`
- `parallaxPush`
- `morph` <x-docs.version-badge since="4.7" />
- `none`

### Action modifiers

Modifiers also select the navigation action. Use these in place of a route to `back`, or combine them with a route
to `replace` or `exitToWeb`:

@verbatim
```blade
<native:pressable @navigate.back>
    <native:text>Go back</native:text>
</native:pressable>

<native:pressable @navigate.replace='/login'>
    <native:text>Sign out</native:text>
</native:pressable>

<native:pressable @navigate.exitToWeb='/dashboard'>
    <native:text>Open dashboard</native:text>
</native:pressable>
```
@endverbatim

Transition and action modifiers can be combined — `@navigate.replace.fade='/login'`.

## Morphing elements between screens

<x-docs.version-badge since="4.7" />

The morph transition carries an element from where it sits on one screen to where it sits on the next. A thumbnail
grows into a header image, or an avatar slides into place on a profile page. If you've used the web's View
Transitions API, it's the same idea.

Give the element the same `ref` on both screens, then navigate with the `morph` modifier:

@verbatim
```blade
{{-- The album list --}}
@foreach ($albums as $album)
    <native:pressable @navigate.morph="'/album/' . $album->id">
        <native:image :ref="'album-' . $album->id" :src="$album->cover" class="w-16 h-16 rounded-xl" />
    </native:pressable>
@endforeach
```
@endverbatim

@verbatim
```blade
{{-- The album screen --}}
<native:column class="w-full">
    <native:image :ref="'album-' . $album->id" :src="$album->cover" class="w-full h-80" />

    <native:pressable @navigate.back.morph>
        <native:text>Back</native:text>
    </native:pressable>
</native:column>
```
@endverbatim

Tapping an album cross-fades the two screens while the cover moves and grows from the small tile into the header.
Going back with `@navigate.back.morph` plays it in reverse.

From a component, pass `Transition::Morph`:

```php
use Native\Mobile\Edge\Transition;

$this->navigate('/album/3')->transition(Transition::Morph);
```

### Shaping the morph

These attributes change how an element travels. They're read from the element on the screen you're navigating to,
so put them on both elements if you want the same motion in both directions.

- `morph` - what moves. `frame` (the default) moves and resizes the element. `position` moves it at its final size.
  `size` resizes it in place, at its final position. `none` stops the element morphing.
- `morph-duration` - length in milliseconds. The default is `350`, the same as the cross-fade behind it.
- `morph-easing` - the curve: `linear`, `ease-in`, `ease-out`, `ease-in-out` (the default) or `spring`.

@verbatim
```blade
<native:column ref="now-playing" morph="position" morph-duration="500" morph-easing="spring">
    ...
</native:column>
```
@endverbatim

<aside>

On Android, `position` and `size` behave like `frame`. Compose's shared element API always animates the whole frame,
so the element moves and resizes together.

</aside>

### Which elements morph

- An element morphs only when its `ref` is on both screens. Everything else cross-fades with its screen.
- Elements only morph under the `morph` transition. A `ref` that appears on both screens does nothing during a slide
  or a fade.
- Keep each `ref` unique within a screen. If two elements share one, you can't be sure which of them moves.
- `ref` is also how [tests target elements](../testing/interactions#by-ref). If you've reused a test `ref` on two
  screens and don't want those elements to morph, add `morph="none"` to either of them.

### Native chrome

A morph needs one whole screen to replace another. Inside native chrome that doesn't always happen. Pushing from one
screen with a native top bar to another, or switching between tabs of a bottom nav, is animated by the platform's
own navigation, and nothing morphs.

Morphs do run between screens that have no native chrome, and whenever the chrome changes, such as a tab screen
opening a detail screen that has a top bar instead. See [Layouts](layouts) for how screens get their chrome.

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
