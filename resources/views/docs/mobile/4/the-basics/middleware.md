---
title: Middleware
order: 153
---

## Overview

`Route::native()` returns a real Laravel route, so middleware attaches exactly as it does anywhere else:

```php
use App\NativeComponents\Dashboard;
use App\NativeComponents\Settings;

Route::native('/dashboard', Dashboard::class)->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::native('/', Dashboard::class);
    Route::native('/settings', Settings::class);
});
```

Both forms work, and both run on **every** navigation to those screens — cold start, in-app `navigate()` and
`replace()`, deep links, and the stack restored after a hot reload.

## Why native middleware is different

A native app enters its runloop once. The screen the app launches into arrives as a genuine HTTP request, so the
HTTP kernel runs its middleware normally. Every screen after that is reached by in-app navigation, which resolves
through the native router and mounts the component directly — there is no second request, and no kernel to run a
pipeline against.

So middleware on a native route can't work quite the way it does on the web. Something has to stand in for the
request, and that is worth understanding before you rely on it.

## The synthesized request

Before a guarded screen mounts, NativePHP builds a `Request` for the target URI and runs the route's middleware
stack against it through Laravel's own `Pipeline`. It is a real `Illuminate\Http\Request`, but NativePHP created it
rather than receiving it from a client, and the difference is observable.

**Carried over from the request that launched the app:**

| | |
|---|---|
| Session | The live store — `session()` reads and writes the same data |
| Authenticated user | The launch request's user resolver, so `auth()->user()` is the real user |
| Cookies | Copied from the launch request |
| Server bag | Copied from the launch request |
| Route | Bound via `setRouteResolver()`, so `$request->route()` works |

**Not carried:**

- The method is always `GET` — navigation has no verb.
- There is no body, no query string, and no uploaded files. Route parameters live in the URI; screen data travels
  in `navigate()`'s data bag, not the request.
- Headers are defaults rather than what the device sent, so middleware reading `User-Agent`, `Accept`, or a custom
  header won't see the launch request's values.
- The client IP is a default, not the device's. Middleware that geolocates or throttles by IP won't behave as it
  does on the web.

Middleware that depends on any of the above should opt out — see below.

## Middleware that is skipped

Request-lifecycle middleware is excluded automatically. It already ran once for the real launch request, and
re-running it on every screen push would reopen the session, rotate the CSRF token, and re-emit cookies onto a
response that is never sent anywhere:

- `StartSession`
- `AuthenticateSession`
- `VerifyCsrfToken`
- `EncryptCookies`
- `AddQueuedCookiesToResponse`
- `ShareErrorsFromSession`

Everything else runs, including anything in your `web` group that isn't on that list.

## Opting your own middleware out

Middleware that should count once per app launch rather than once per screen — rate limiters, analytics, "last
seen" writes — can opt out from a service provider's `boot()`:

```php
use Native\Mobile\Edge\ScreenGuard;

ScreenGuard::skip([
    RecordVisit::class,
    ThrottleRequests::class,
]);
```

## What happens when middleware refuses

Middleware refuses a navigation the same way it refuses a request: by redirecting or aborting. NativePHP maps the
result onto native navigation.

| Middleware does | Native result |
|---|---|
| Passes the request through | Screen mounts |
| Redirects to a native route | `replace()` onto that screen |
| Redirects anywhere else | Exits to the web view with that URL |
| Throws `AuthenticationException` | Redirects to its `redirectTo()`, else the `login` route |
| Aborts (403) or returns a response | Navigation is refused; the user stays where they were |

Two guarantees are worth stating outright:

**A refused screen never mounts.** The check runs before `mount()`, so a guarded screen performs none of its data
loading — no queries, no API calls — before being turned away. It publishes no frame either, so none of its content
can appear.

**Guards fail closed.** If middleware throws — an unresolvable alias, a bug in your own guard — the navigation is
refused rather than allowed. Failing open would silently grant access.

A guard that redirects to the very screen it guards would bounce forever. That case is detected and refused instead
of hanging the runloop.

## Testing

`Native::visit()` runs middleware, so a redirect is directly assertable:

```php
it('keeps guests out of the dashboard', function () {
    Native::visit('/dashboard')->assertReplacedWith('/login');
});

it('lets a member in', function () {
    $this->actingAs(User::factory()->create());

    Native::visit('/dashboard')->assertSee('Welcome back');
});
```

Note the distinction: `Native::test(Dashboard::class)` mounts the component class directly and deliberately does
**not** run route middleware — it has no route. Reach a screen by URI with `Native::visit()` when the middleware is
part of what you're testing. A suite built only on `Native::test()` can't catch a middleware regression.
