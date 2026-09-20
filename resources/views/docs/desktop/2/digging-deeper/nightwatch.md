---
title: Nightwatch
order: 800
---

[Laravel Nightwatch](https://nightwatch.laravel.com) is a first-party observability product for Laravel applications
— requests, queries, queue jobs, exceptions, and more. NativePHP wires it up automatically for your packaged desktop
app when it detects Nightwatch is installed.

## Enabling it

Install Nightwatch in your app as you would for any Laravel project, and set your `NIGHTWATCH_TOKEN`:

```dotenv
NIGHTWATCH_TOKEN=your-token
```

When your app is packaged and launched, NativePHP checks for both `laravel/nightwatch` in your dependencies and a
`NIGHTWATCH_TOKEN`. If both are present, it starts a local Nightwatch ingest agent (`php artisan nightwatch:agent`)
alongside your bundled PHP process and wires it up automatically — there's nothing else to configure.

If a token is set but Nightwatch isn't installed, NativePHP skips starting the agent rather than failing.

<aside>

`NIGHTWATCH_TOKEN` is not stripped from your bundled app's `.env` file during packaging — unlike most credentials
(see [Security](security#your-code-ships-with-your-app)), it needs to be readable at runtime for the agent to start.

</aside>

## Internal routes are excluded

NativePHP's own internal bridge routes (`_native/api/*`, used for communication between your PHP app and the native
shell) are sampled at 0% when Nightwatch is installed, so this internal plumbing never shows up in your Nightwatch
dashboard alongside your actual application traffic.
