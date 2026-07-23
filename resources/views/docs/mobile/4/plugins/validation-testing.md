---
title: Validation & Testing
order: 800
---

## Validating Your Plugin

Before building, validate your plugin to catch common issues:

```shell
php artisan native:plugin:validate
```

This checks:
- Manifest syntax and required fields
- Bridge function declarations match native code
- Hook commands are registered and exist
- Declared assets are present

## Common Validation Errors

**"Bridge function not found in native code"**

Your manifest declares a function, but the Swift or Kotlin implementation is missing or named differently. Check
that class names and function names match exactly.

**"Invalid manifest JSON"**

Your `nativephp.json` has a syntax error. Check for trailing commas, missing quotes, or unclosed brackets.

**"Hook command not registered"**

The manifest references an Artisan command that isn't registered in your service provider. Make sure
`native:plugin:make-hook` has updated your service provider, or add it manually.

## Testing During Development

### Test PHP Code

Your PHP facades and event handling work like any Laravel code. Write standard PHPUnit tests:

```php
public function test_plugin_facade_is_accessible()
{
    $this->assertInstanceOf(MyPlugin::class, app(MyPlugin::class));
}
```

### Test Native Code

Native code can only be tested by running the app. Use this workflow:

1. Install your plugin locally via path repository
2. Run `php artisan native:run`
3. Trigger your plugin's functionality in the app
4. Check the console output for errors

<aside>

#### Use Logging

Add `$this->info()` or `Log::debug()` in your native code to trace execution. Check the device logs with
`php artisan native:tail`.

</aside>

## Shipping Test Vocabulary

App developers test your plugin's screens with the [component testing suite](../testing/introduction). You can ship a
vocabulary of first-class assertions so their tests read in your plugin's domain terms — `assertCopied()` rather than a
raw `Clipboard.WriteText` string.

The suite's `FakeBridge` is macroable, and the test harness forwards unknown methods to it. Register your macros where
they'll be loaded before tests run — your service provider's `boot()` method, or a helper file your `composer.json`
autoloads:

```php
use Native\Mobile\Testing\FakeBridge;

public function boot(): void
{
    FakeBridge::macro('assertCopied', function (?string $text = null) {
        return $this->assertCalled('Clipboard.WriteText',
            fn (array $p) => $text === null || $p['text'] === $text);
    });
}
```

The macro binds to the `FakeBridge` instance, so `$this` inside it is the bridge — every built-in helper
(`assertCalled`, `respondTo`, `callsTo`, and the rest) is available. App tests then call your macro straight off the
harness:

```php
Native::test(ShareSheet::class)
    ->tap('copy')
    ->assertCopied('https://nativephp.com');
```

### Assertion macros vs scripting macros

Two kinds of macro cover most plugins.

**Assertion macros** wrap `assertCalled()` and its siblings to confirm the component reached the bridge as expected:

```php
FakeBridge::macro('assertShared', function (string $url) {
    return $this->assertCalled('Share.Url', fn (array $p) => $p['url'] === $url);
});
```

**Scripting macros** wrap `respondTo()` to stage a device response in domain terms, so a test can set the scene before
the screen mounts:

```php
FakeBridge::macro('withLocation', function (float $lat, float $lng) {
    return $this->respondTo('Geolocation.GetCurrentPosition', [
        'latitude' => $lat,
        'longitude' => $lng,
    ]);
});
```

```php
Native::fakeBridge()->withLocation(48.85, 2.35);

Native::test(GeolocationDemo::class)
    ->call('locate')
    ->assertSet('latitude', 48.85);
```

### Returning `$this` for fluent chains

`assertCalled()`, `respondTo()`, and the other built-in helpers all return the bridge, so returning their result keeps
your macro fluent. When a macro returns the bridge, the harness hands back itself instead — the chain stays on the
component, and the next `->tap()` or `->assertSet()` follows naturally:

```php
Native::test(ShareSheet::class)
    ->assertCopied('https://nativephp.com')  // returns the harness
    ->assertSee('Copied!');
```

If your macro computes a value to return instead — say a helper that reads back recorded calls — the harness passes
that value straight through, exactly as calling it on the bridge would.

### How failures surface

A macro that delegates to `assertCalled()` fails with that helper's underlying PHPUnit message, naming the bridge
method and listing the calls that were actually made:

```
Expected native bridge call [Clipboard.WriteText] was not made. Calls made: (none)
```

Because the assertion carries the real method name, the app developer sees exactly which bridge call was missing —
your domain wrapper reads cleanly without hiding the underlying cause.

<aside>

Calling a name that was never registered raises a clear `BadMethodCallException` naming both the harness and the
missing macro, so a typo fails loudly rather than silently passing.

</aside>

## Debugging Tips

**Plugin not discovered?**
- Verify `composer.json` has `"type": "nativephp-plugin"`
- Run `composer dump-autoload`
- Check `php artisan native:plugin:list`

**Native function not found at runtime?**
- Rebuild the app after changing native code
- Check the manifest's function names match exactly
- Verify the Kotlin package name is correct

**Events not firing?**
- Confirm you're dispatching on the main thread
- Check the event class name matches the manifest
- Verify the `#[OnNative]` attribute uses the correct class

## Official Plugins & Dev Kit

Skip the debugging — browse ready-made plugins or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
