---
title: Accessibility
order: 450
---

## Overview

Because mounting a screen needs no device, you can audit it for screen-reader accessibility in the same in-process
test that drives its behavior. `assertAccessible()` walks the rendered wire tree with the same rules a screen
reader cares about and fails with a located list of every violation, so an unlabeled control can't ship unnoticed.

This checks that the tree carries the right labels — it does not replace a manual VoiceOver/TalkBack pass. See
[Accessibility](../digging-deeper/accessibility) for the labeling rules the audit enforces and the platform
behavior you get for free.

## assertAccessible()

Flags the same problems the audit rules define: icon-only buttons, chips, and tabs without an `a11y-label`,
clickable icons and images without labels or `alt` text, pressables with neither visible text nor a label, form
controls with no label of any kind, and list items whose trailing icon button is unlabeled.

```php
it('renders an accessible screen', function () {
    Native::visit('/checkout')->assertAccessible();
});
```

When it fails, the message names each offending node by its ref, icon, or nearest text, so you can find it in the
Blade view without guessing.

## Sweeping every screen

Audit the whole app in one data-driven test. `NativeRouter::registeredRoutes()` returns the full route table, so a
newly added screen is covered the moment it's registered — nobody has to remember to add it here:

```php
use Native\Mobile\Edge\NativeRouter;

it('renders every registered screen accessibly', function (string $uri) {
    Native::visit($uri)->assertAccessible();
})->with(array_keys(NativeRouter::registeredRoutes()));
```

Substitute a value for any `{param}` segments, and filter out screens that hit the bridge or network on mount
(they need an `emitNative()` or a `FakeBridge` expectation first):

```php
$routes = collect(array_keys(NativeRouter::registeredRoutes()))
    ->reject(fn (string $uri) => in_array($uri, ['/vibe', '/geo-watch']))
    ->map(fn (string $uri) => preg_replace('/\{[^}]+\}/', '1', $uri)); // /post/{id} -> /post/1
```

## Conditional content

The audit only sees the current frame. Content behind an `@@if` — a photo grid that appears after images load, a
send button that replaces a mic button once you type — exists only once its state does, so drive the state in
first, then assert:

```php
it('stays accessible after a photo is captured', function () {
    Native::test(CameraDemo::class)
        ->call('takePhoto')
        ->emitNative(PhotoTaken::class, ['path' => '/tmp/photo-1.jpg'])
        ->assertAccessible();
});
```

## Inspecting violations directly

`accessibilityViolations()` returns the raw list of violation strings instead of asserting — use it to allow-list a
known exception, or to fail with your own message:

```php
it('has no unexpected accessibility violations', function () {
    $violations = Native::visit('/legacy-screen')->accessibilityViolations();

    // A single known gap tracked in TICKET-123; everything else must be clean.
    expect($violations)->toHaveCount(1);
});
```

## Pest expectation sugar

With `PestExpectations::register()` (see [Advanced](advanced#pest-expectation-sugar)), the audit composes into
`expect()` chains as `toBeAccessible()`:

```php
it('is accessible', function () {
    expect(Native::visit('/checkout'))
        ->toSee('Place order')
        ->toBeAccessible();
});
```
