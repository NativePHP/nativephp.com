---
title: Introduction
order: 1
---

## What is EDGE?

EDGE (Element Definition and Generation Engine) is NativePHP for Mobile's component system that transforms Blade
template syntax into platform-native UI elements that look beautiful whichever device your users are using.

Instead of rendering in the web view, EDGE components are compiled into truly native elements and live apart from the
web view's lifecycle. This means they are persistent and offer truly native performance.

There's no custom rendering engine and complex ahead-of-time compilation process, just a lightweight transformation
step that happens at runtime. You end up with pure, fast and flexible native components — all configured by PHP!

## How It Works

@verbatim
```blade static
<native:bottom-nav>
    <native:bottom-nav-item id="home" icon="home" label="Home" url="/home" />
</native:bottom-nav>
```
@endverbatim

You simply define your components in Blade and EDGE processes these during each request, passing instructions to the
native side. The native UI rendering pipeline takes over to generate your defined components and builds the interface
just the way your users would expect, enabling your app to use the latest and greatest parts of each platform,
such as Liquid Glass on iOS.

Under the hood, the Blade components are compiled down to a simple JSON configuration which we pass to the native side.
The native code already contains the generic components compiled-in. These are then rendered as needed based on the
JSON configuration.

<aside>

#### Sounds like _Server-Driven UI_...

That's right! This approach takes more than one leaf out of the server-driven UI book. The difference is that it can be
greatly simplified as there is no network involved — all the generation and rendering is happening on-device, with PHP
speaking directly to the UI state manager.

It doesn't even rely on the web view!

</aside>

## The `native:` prefix is optional

`<native:column>` and `<column>` compile to exactly the same thing — the prefix is optional. We use it throughout
these docs for two reasons:

- **Clarity** — `<native:button>` is unmistakably a native component, never confused with an HTML `<button>`.
- **IDE support** — editors resolve the prefixed form to its component class and offer attribute autocomplete on
  props; a bare `<column>` reads as plain HTML, so you lose that typeahead.

Drop the prefix for terser templates whenever you prefer — both forms are fully supported.

## Why Blade?

Blade is an expressive and straightforward templating language that is very familiar to most Laravel users, and also
super accessible to anyone who's used to writing HTML. All of our components are Blade components, which allows us to
use Blade's battle-tested processing engine to rapidly compile the necessary transformation just in time.

## Where to define your native components

They can be defined in any Blade file, but for them to be processed, that Blade file will need to be rendered. We
recommend putting your components in a Blade component that is likely to be rendered on every request, such as your
main layout, e.g. `layouts/app.blade.php` or one of its child views/components.

## Props Validation

EDGE components enforce required props validation to prevent misconfiguration. If you're missing required props, you'll
see a clear error message that tells you exactly what's missing and how to fix it.

For example, if you forget the `label` prop on a bottom navigation item:

```
EDGE Component <native:bottom-nav-item> is missing required properties: 'label'.
Add these attributes to your component: label="..."
```

The error message will list all missing required props and show you exactly which attributes you need to add. This
validation happens at render time, making it easy to catch configuration issues during development.

Each component's documentation page indicates which props are required vs optional.
