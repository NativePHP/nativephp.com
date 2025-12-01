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
