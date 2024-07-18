---
title: System
order: 800
---
# The System

One of the main advantages of building a native application is having more direct access to system resources, such as
peripherals connected to the physical device and APIs that aren't typically accessible inside a browser's sandbox. 

NativePHP makes it trivial to access these resources and APIs.

One of the main challenges - particularly when writing cross-platform apps - is that each operating system has
its own set of available APIs, along with their own idiosyncrasies.

NativePHP smooths over as much of this as possible, to offer a simple and consistent set of interfaces regardless of
the platform on which your app is running.

While some features are platform-specific, NativePHP gracefully handles this for you so that you don't have to think
about whether something is Linux-, Mac-, or Windows-only.

## TouchID

For Mac systems that support TouchID, you can use TouchID to protect and unlock various parts of your application:

```php
use Native\Laravel\Facades\System;

if (System::canPromptTouchID() && System::promptTouchID()) {
    // Do you super secret activity here
}
```

## Printing

You can list all available printers:

```php
@use(Native\Laravel\Facades\System)

@@foreach(System::printers() as $printer)
    @{{ $printer->displayName }}
@@foreach
```

Each item in the printers array is a `\Native\Laravel\DataObjects\Printer` which contains various device details and
default configuration.

You can send some HTML to be printed like this:

```php
System::print('<html>...', $printer);
```

If no `$printer` object is provided, the default printer and settings will be used.

You can change the configuration before sending something to be printed, for example if you want multiple copies:

```php
$printer->options['copies'] = 5;

System::print('<html>...', $printer);
```

You can also print directly to PDF:

```php
System::printToPDF('<html>...');
```

This returns the PDF data in a `base64_encoded` binary string. So be sure to `base64_decode` it before storing it to disk.
You could save it directly to disk or prompt the user for a location to save it:

```php
use Illuminate\Support\Facades\Storage;

$pdf = System::printToPDF('<html>...');

Storage::disk('desktop')->put('My Awesome File.pdf', base64_decode($pdf));
```

## Time Zones

PHP and your Laravel application will generally be configured to work with a specific time zone. This could be UTC, for
example.

But users of your application will think about time differently. Normally, the user's perspective of time is reflected
in their operating system's time zone setting.

NativePHP includes a mechanism to translate cross-platform time zone identifiers to consistent identifiers that PHP
expects to use.

You can use this to show dates and times in the appropriate time zone without having to ask your users to manually
select their current time zone.

**Note: It some cases, this mechanism may not select the exact time zone of the user. It uses an approximation to
simplify things, as there are many overlapping time zones and methods of naming them.**

Using this approach, your app will be responsive to changes in the system's time zone settings, e.g. in case the
user moves between timezones.

Get the current system time zone:

```php
$timezone = System::timezone();

// $timezone => 'Europe/London'
```
