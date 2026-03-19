---
title: System
order: 800
---

## The System

One of the main advantages of building a native application is having more direct access to system resources, such as
peripherals connected to the physical device and APIs that aren't typically accessible inside a browser's sandbox.

NativePHP makes it trivial to access these resources and APIs.

One of the main challenges - particularly when writing cross-platform apps - is that each operating system has
its own set of available APIs, along with their own idiosyncrasies.

NativePHP smooths over as much of this as possible, to offer a simple and consistent set of interfaces regardless of
the platform on which your app is running.

While some features are platform-specific, NativePHP gracefully handles this for you so that you don't have to think
about whether something is Linux-, Mac-, or Windows-only.

Most of the system-related features are available through the `System` facade.

```php
use Native\Laravel\Facades\System;
```

## Encryption / Decryption

Almost every non-trivial application will require some concept of secure data storage and retrieval. For example, if
you want to generate and store an API key to access a third-party service on behalf of your user.

You shouldn't ship these sorts of secrets _with_ your app, but rather generate them or ask your user for them at
runtime.

But when your app is running on a user's device, you have
[far less control and fewer guarantees](/docs/digging-deeper/security) over the safety of any secrets stored.

On a traditional server-rendered application, this is a relatively simple problem to solve using server-side encryption
with keys which are hidden from end users.

For this to work on the user's device, you need to be able to generate and store an encryption key securely.

NativePHP takes care of the key generation and storage for you, all that's left for you to do is encrypt, store and
decrypt the secrets that you need to store on behalf of your user.

NativePHP allows you to encrypt and decrypt data in your application easily:

```php
if (System::canEncrypt()) {
    $encrypted = System::encrypt('secret_key_a79hiunfw86...');

    // $encrypted => 'djEwJo+Huv+aeBgUoav5nIJWRQ=='
}
```

You can then safely store the encrypted string in a database or the filesystem.

When you need to get the original value, you can decrypt it:

```php
if (System::canEncrypt()) {
    $decrypted = System::decrypt('djEwJo+Huv+aeBgUoav5nIJWRQ==');

    // $decrypted = 'secret_key_a79hiunfw86...'
}
```

## TouchID

For Mac systems that support TouchID, you can use TouchID to protect and unlock various parts of your application.

```php
if (System::canPromptTouchID() && System::promptTouchID('access your Contacts')) {
    // Do your super secret activity here
}
```

You must pass a `string $reason` as the only argument to `System::promptTouchID`. This will show up in the dialog that
TouchID users are familiar with:

![TouchID Prompt Example on macOS](/img/docs/touchid.png)

Using this, you can gate certain parts of your app, or your *entire* application, allowing you to offer an extra layer
of protection for your user's data.

**Note: Despite the name, TouchID only gives you greater *confidence* that the person using your app is the same as the
person who has unlocked the device your app is installed on. It does not allow you to *identify* that user, nor does
it give you any special privileges to their system.**

## Printing

You can list all available printers:

```blade
@@use(Native\Laravel\Facades\System)

@@foreach(System::printers() as $printer)
    @{{ $printer->displayName }}
@@endforeach
```

Each item in the printers array is a `\Native\Laravel\DataObjects\Printer` which contains various device details and
default configuration.

You can send some HTML to be printed like this:

```php
System::print('<html>...', $printer);
```

If no `$printer` object is provided, the default printer and settings will be used.

You can also print directly to PDF:

```php
System::printToPDF('<html>...');
```

This returns the PDF data in a `base64_encoded` binary string. So be sure to `base64_decode` it before storing it to
disk:

```php
use Illuminate\Support\Facades\Storage;

$pdf = System::printToPDF('<html>...');

Storage::disk('desktop')->put('My Awesome File.pdf', base64_decode($pdf));
```

### Print Settings

You can change the configuration before sending something to be printed, for example if you want multiple copies:

```php
$printer->options['copies'] = 5;

System::print('<html>...', $printer);
```

Additionally, both the `print()` and `printToPDF()` methods accept an optional `$settings` parameter that allows you to customize the print behavior:

```php
System::print('<html>...', $printer, $settings);
```

#### Print Settings Examples

You can customize print behavior using the settings array. Here are some common examples:

```php
// Print with custom page size and orientation
$settings = [
    'pageSize' => 'A4',
    'landscape' => true,
];

System::print('<html>...', $printer, $settings);
```

```php
// Print multiple copies with duplex
$settings = [
    'copies' => 3,
    'duplexMode' => 'longEdge', // 'simplex', 'shortEdge', 'longEdge'
    'color' => false, // true for color, false for monochrome
];

System::print('<html>...', $printer, $settings);
```

For a complete list of available print settings, refer to the [Electron webContents.print()](https://www.electronjs.org/docs/latest/api/web-contents#contentsprintoptions-callback) and [webContents.printToPDF()](https://www.electronjs.org/docs/latest/api/web-contents#contentsprinttopdfoptions) documentation.

## Time Zones

PHP and your Laravel application will generally be configured to work with a specific time zone. This could be UTC, for
example.

But users of your application will think about time differently. Normally, the user's perspective of time is reflected
in their operating system's time zone setting.

NativePHP includes a mechanism to translate cross-platform time zone identifiers to consistent identifiers that PHP
expects to use.

You can use this to show dates and times in the appropriate time zone without having to ask your users to manually
select their current time zone.

**Note: In some cases, this mechanism may not select the _exact_ time zone that the user is in. It uses an approximation
to simplify things, as there are many overlapping time zones and methods of naming them.**

Using this approach, your app will be responsive to changes in the system's time zone settings, e.g. in case the
user moves between time zones.

Get the current system time zone:

```php
$timezone = System::timezone();

// $timezone => 'Europe/London'
```

## Theme

NativePHP allows you to detect the current theme of the user's operating system. This is useful for applications that
want to adapt their UI to match the user's preferences.
You can use the `System::theme()` method to get the current theme of the user's operating system.

```php
$theme = System::theme();
// $theme => SystemThemesEnum::LIGHT, SystemThemesEnum::DARK or SystemThemesEnum::SYSTEM
```

You can also set the theme of your application using the `System::theme()` method. This will change the theme of your
application to the specified value. The available options are `SystemThemesEnum::LIGHT`, `SystemThemesEnum::DARK` and
`SystemThemesEnum::SYSTEM`.

```php
System::theme(SystemThemesEnum::DARK);
```

Setting the theme to `SystemThemesEnum::SYSTEM` will remove the override and everything will be reset to the OS default.
By default themeSource is `SystemThemesEnum::SYSTEM`.
