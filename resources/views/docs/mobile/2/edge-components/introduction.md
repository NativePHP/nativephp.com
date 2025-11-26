---
title: Introduction
order: 1
---

## What is EDGE?

EDGE (Element Definition and Generation Engine) is NativePHP for Mobile's component system that transforms Blade
template syntax into platform-native UI elements.

Instead of rendering in the web view, EDGE components are compiled into truly native elements and live apart from the
web view's lifecycle. This means they are persistent and performant.

These are native components, coming with all the performance benefits of the native UI rendering pipeline — no custom
rendering engine, no expensive transformation or compilation step. Just pure native components configured by PHP.

## Available Components

Our first set of components are focused on navigation, framing your application with beautiful, platform-dependent UI
components. These are familiar navigational elements that your users will be familiar with and make your app feel right
at home, like any other native app. All whilst giving you easy-to-use tools to help you rapidly build your app.

Dig into the docs for each component:

- **[Bottom Navigation](bottom-nav)** - Bottom navigation bar with up to 5 items
- **[Top Bar](top-bar)** - Top app bar with title and action buttons
- **[Side Navigation](side-nav)** - Slide-out navigation drawer

## How It Works

You simply define your components in Blade:

@verbatim
```blade
<native:bottom-nav>
    <native:bottom-nav-item id="home" icon="home" label="Home" url="/home" />
</native:bottom-nav>
```
@endverbatim

And EDGE processes these during each request, passing instructions to the native side. The native UI rendering pipeline
takes over to generate your defined components and builds the interface just the way your users would expect, enabling
your app to use the latest and greatest parts of each platform, such as Liquid Glass on iOS.

Under the hood, the Blade components are compiled down to a simple JSON configuration which we pass to the native side.
The native code already contains generic components compiled-in that are then rendered as needed based on the JSON.

<aside>

#### Sounds like _Server-Driven UI_...

That's right! This approach takes more than one leaf out of the server-driven UI book. The difference is that it can be
greatly simplified as there is no network involved — all the generation and rendering is happening on-device.

</aside>

## Why Blade?

Blade is an expressive and straightforward templating language that is very familiar to all Laravel users, but is also
super accessible to anyone who's used to writing HTML. All of our components are Blade components, allowing us to use
Blade's battle-tested processing engine to rapidly compile the necessary transformation just in time.
