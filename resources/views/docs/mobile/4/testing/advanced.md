---
title: Advanced
order: 500
---

## Overview

Beyond driving and asserting, the suite gives you tools for the finer points: rendering a screen as a specific
platform, guarding against wasteful re-renders, locking a screen's tree with snapshots, inspecting the raw wire tree,
and folding everything into Pest's `expect()` syntax.

## Platform variants

`ios:` and `android:` Tailwind variants let a screen adapt per platform. Pass `platform` to render as one or the
other, activating the matching variants for that frame:

```php
it('applies android-only translucency on the home quick-nav', function () {
    Native::visit('/', platform: 'android')
        ->assertElement('stack', fn (array $n) => ($n['style']['bg_color'] ?? null) === '#33FFFFFF');
});

it('omits the android translucency on ios', function () {
    Native::visit('/', platform: 'ios')
        ->assertMissingElement('stack', fn (array $n) => ($n['style']['bg_color'] ?? null) === '#33FFFFFF');
});
```

`assertElement($type, $matcher)` finds an element of a wire type, optionally narrowed by a closure receiving the wire
node; `assertMissingElement()` is its inverse.

To audit a tree for screen-reader accessibility rather than a specific element, see
[Accessibility](accessibility).

## Render-count guards

Every interaction re-renders. When performance matters, assert on exactly how many frames a screen produced.

- `renderCount()` — frames rendered so far (the initial mount counts as 1).
- `assertRenderCount($count)` — an exact frame count.
- `assertRerendered()` — the last interaction produced a new frame.
- `assertNotRerendered()` — it produced none.

```php
it('re-renders exactly once per interaction', function () {
    Native::test(HapticsDemo::class)
        ->assertRenderCount(1)
        ->tap('vibrate-card')
        ->assertRerendered()
        ->assertRenderCount(2);
});
```

An interaction that navigates away publishes its final state rather than a fresh frame, so it doesn't re-render:

```php
it('skips the re-render when navigating away', function () {
    Native::visit('/media')
        ->tap('Scanner')
        ->assertNotRerendered();
});
```

## Wire snapshots

`assertMatchesSnapshot()` captures the current wire tree and compares it against a committed snapshot on later runs.
Volatile fields — node ids and content hashes — are stripped, and callback ids are replaced with the expression they
point at, so snapshots stay stable across runs and read cleanly in review (a press handler shows as `@increment`
rather than an opaque number).

```php
it('matches the haptics screen wire snapshot', function () {
    Native::test(HapticsDemo::class)
        ->assertMatchesSnapshot()
        ->tap('vibrate-card')
        ->assertMatchesSnapshot('after-one-buzz');
});
```

The first run writes the snapshot to `tests/__snapshots__/<test-file>/<name>.json` and passes. Commit that file — it's
the baseline. When you intentionally change a screen, rewrite the snapshots by running with `UPDATE_SNAPSHOTS=1`:

```bash
UPDATE_SNAPSHOTS=1 php artisan test
```

Pass a name to `assertMatchesSnapshot('after-one-buzz')` when a test takes more than one snapshot; unnamed snapshots
are numbered in order.

## Inspecting the tree

Sometimes an assertion helper isn't enough and you want the raw data. These return values rather than the harness:

- `tree()` — the most recently published wire tree, as a nested array.
- `instance()` — the live component instance.
- `get($property)` — read a public or `#[Computed]` property.
- `bridge()` — the `FakeBridge` for this test.
- `navigationIntent()` — the pending navigation intent, if any.
- `dumpTree()` — dump the current tree while debugging (returns the harness, so it chains).

```php
it('accumulates continuous scans', function () {
    $screen = Native::test(ScannerDemo::class)
        ->call('scanContinuously')
        ->emitNative(CodeScanned::class, ['data' => 'SKU-1', 'format' => 'ean13'])
        ->emitNative(CodeScanned::class, ['data' => 'SKU-2', 'format' => 'ean13']);

    expect($screen->get('scans'))->toHaveCount(2)
        ->and($screen->get('scans')[1]['data'])->toBe('SKU-2');
});
```

## Bridge helpers on the harness

The `FakeBridge`'s own helpers are callable straight off the harness — the harness forwards any method it doesn't
recognise to this test's bridge. For the simple cases that means you skip the `bridge()` hop entirely and assert on
native traffic right in the chain:

```php
it('copies the share link', function () {
    Native::test(ShareSheet::class)
        ->tap('copy')
        ->assertCalled('Clipboard.WriteText', fn (array $p) => $p['text'] === 'https://nativephp.com');
});
```

`assertCalled`, `assertCalledTimes`, `assertNotCalled`, `assertCallOrder`, and `assertNothingCalled` all read this
way, as do the scripting and inspection helpers `respondTo`, `callsTo`, and `lastPublish`. When a bridge helper
answers fluently — returning the bridge — the harness returns itself instead, so the chain stays on the component and
the next interaction just follows:

```php
Native::test(ScannerDemo::class)
    ->call('scanContinuously')
    ->emitNative(CodeScanned::class, ['data' => 'SKU-1', 'format' => 'ean13'])
    ->assertCalledTimes('Barcode.Scan', 1)
    ->assertSee('SKU-1');
```

The `assertNative*` methods documented in [Native Events & the Bridge](native-events) remain the most explicit way to
read a test; these forwarded helpers are the shorter path when you're already thinking in bridge-method terms.

## Plugin test vocabulary

Plugins can teach the `FakeBridge` their own assertions, so app tests read in the plugin's domain terms instead of raw
bridge-method strings. A clipboard plugin might ship `assertCopied()`; once its package is installed, you call it
straight off the harness like any built-in helper:

```php
it('copies the link in domain terms', function () {
    Native::test(ShareSheet::class)
        ->tap('copy')
        ->assertCopied('https://nativephp.com');
});
```

Scripting vocabulary works the same way. A geolocation plugin might ship a `withLocation()` macro that stages a device
response, so you set the scene before mounting without naming the underlying bridge method:

```php
it('reads the scripted location', function () {
    Native::fakeBridge()->withLocation(48.85, 2.35);

    Native::test(GeolocationDemo::class)
        ->call('locate')
        ->assertSet('latitude', 48.85);
});
```

These read as first-class harness methods because the same forwarding backs them. If you write plugins yourself, see
[Validation & Testing](../plugins/validation-testing) for how to register this vocabulary.

## Pest expectation sugar

If you prefer Pest's `expect()` style end to end, register the expectation extensions once in `tests/Pest.php`:

```php
\Native\Mobile\Testing\PestExpectations::register();
```

That adds `toSee`, `toNotSee`, `toHaveSet`, `toHaveNavigatedTo`, `toHaveElement`, `toBeOnScreen`, and
`toBeAccessible`, so harness assertions compose into `expect()` chains:

```php
it('composes harness assertions into expect() chains', function () {
    expect(Native::visit('/dialogs/toast'))
        ->toSee('Toasts')
        ->toHaveSet('duration', 'long')
        ->toHaveElement('button')
        ->toBeAccessible()
        ->toNotSee('Nonexistent');

    expect(Native::visit('/media')->tap('Scanner'))
        ->toHaveNavigatedTo('/media/scanner');
});
```

## A whole-app smoke test

Because mounting a screen needs no device, you can render every routed screen in one data-driven test — a fast guard
that nothing throws on the way to its first frame:

```php
it('mounts and renders every routed screen without a device', function (string $uri) {
    expect(Native::visit($uri)->tree())->not->toBeEmpty();
})->with([
    '/',
    '/media',
    '/system',
    '/system/haptics',
    '/system/geolocation',
    '/dialogs/toast',
    // ...every route your app registers
]);
```

Rather than hand-maintain that list, pull it from the router so new screens are covered the moment they're
registered. `NativeRouter::registeredRoutes()` returns the `uri => ['class' => ..., 'layout' => ...]` table;
substitute a value for any `{param}` segments:

```php
use Native\Mobile\Edge\NativeRouter;

it('mounts every registered screen', function (string $uri) {
    $visit = preg_replace('/\{[^}]+\}/', '1', $uri); // /post/{id} -> /post/1
    expect(Native::visit($visit)->tree())->not->toBeEmpty();
})->with(array_keys(NativeRouter::registeredRoutes()));
```

Exclude screens that hit the bridge or network on mount (they need a `FakeBridge` expectation or an
`emitNative()` first) by filtering the list.
