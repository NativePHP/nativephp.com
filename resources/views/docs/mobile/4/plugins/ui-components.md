---
title: UI Component Plugins
order: 450
---

## Two Ways to Ship UI

A plugin can add UI to an app at two very different levels:

- **A composed component** — a `NativeComponent` subclass with a Blade view, assembled from elements that already
  exist (`column`, `text`, `button`). No native code. This is the [nested component](../the-basics/nested-components)
  model, packaged for distribution. Reach for it first.
- **A UI component plugin** — a genuinely **new element type**, backed by a SwiftUI view on iOS and a Compose
  composable on Android. This is what you need when the thing you want to render doesn't exist in EDGE: a map, a
  signature pad, a vendor SDK's drop-in payment sheet, a chart view.

This page covers the second. The first is [at the bottom](#shipping-a-composed-component-instead).

<aside>

EDGE's own component library is exactly this mechanism — `nativephp/mobile-ui` is a plugin that declares `text`,
`image`, `column` and the rest, each with a Kotlin and a Swift renderer. Nothing you can do in your own plugin is
privileged differently.

</aside>

## Scaffolding a UI Plugin

```shell
php artisan native:plugin:create
```

Choose **UI Component plugin (custom native UI elements for Blade templates)** when prompted for the plugin type.
You get a working element end-to-end — manifest, Element class, Blade component, Kotlin renderer, Swift renderer,
and tests:

```
my-widget/
├── composer.json               # type: "nativephp-plugin"
├── nativephp.json              # manifest — declares `components`
├── src/
│   ├── MyWidgetServiceProvider.php
│   ├── Elements/
│   │   └── MyWidget.php        # PHP element — props → wire node
│   └── Components/
│       └── MyWidget.php        # Blade component — tag → element
├── resources/
│   ├── android/
│   │   └── MyWidgetRenderer.kt # Compose renderer
│   └── ios/
│       └── MyWidgetRenderer.swift  # SwiftUI renderer
└── tests/
```

Everything else about the package — registration, local path repositories, `native:plugin:register` — works exactly
as it does for a [system plugin](./creating-plugins).

## Declaring Components

Add a `components` array to `nativephp.json`. Each entry wires one element type to its four implementations:

```json
{
    "namespace": "MyWidget",
    "components": [
        {
            "type": "my_widget",
            "element": "MyVendor\\MyWidget\\Elements\\MyWidget",
            "blade": "MyVendor\\MyWidget\\Components\\MyWidget",
            "android_renderer": "com.myvendor.plugins.mywidget.ui.MyWidgetRenderer",
            "ios_renderer": "MyWidgetRenderer",
            "self_closing": true
        }
    ]
}
```

| Field | Required | Description |
|-------|----------|-------------|
| `type` | Yes | The element type on the wire. Snake_case — this is what the Blade tag maps to |
| `element` | Yes | FQCN of the PHP `Element` subclass |
| `blade` | Yes | FQCN of the Blade component class |
| `android_renderer` | At least one | Fully qualified Kotlin object that renders the node |
| `ios_renderer` | At least one | Swift `View` struct name (no module prefix) |
| `self_closing` | No | `true` for leaf elements, `false` (default) for containers that take children |

The manifest is validated on load: a component missing `type`, `element`, `blade`, or **both** renderers throws.
Run `php artisan native:plugin:validate` to catch it before you build.

A component may declare only one renderer — a plugin wrapping an iOS-only SDK ships `ios_renderer` alone. The element
then renders nothing on the other platform rather than failing the build, so guard its use with
[`System::isIos()` / `System::isAndroid()`](../the-basics/system) if the difference is user-visible.

### Naming the tag

The `type` determines how app developers write the element, and there are two paths into the same node:

@verbatim
| Written in Blade | Resolves via | Type it produces |
|---|---|---|
| `<native:my-widget>` | The native-tag precompiler | `my_widget` |
| `<my-widget>` | Same (the prefix is optional) | `my_widget` |
| `<x-native-my-widget>` | The registered Blade component | Whatever `elementType()` returns |
@endverbatim

The first two forms convert kebab → snake and look the type up directly, so **the `type` in your manifest must be the
snake_case form of the tag you want people to write**. `type: "my_widget"` gives you `<native:my-widget>`.

@verbatim
Dotted types (`"type": "stripe.payment_sheet"`) are legal and namespace nicely, but they are only reachable through the
`<x-native-stripe-payment-sheet>` form — no `native:` tag maps to a dot. Prefer a flat, prefixed type like
`stripe_payment_sheet` unless you specifically want the component-class path.
@endverbatim

<aside>

Core element types always win. Registration skips any `type` that is already registered, so a plugin cannot shadow
`column`, `text`, or `pressable` — pick a name unlikely to collide, ideally vendor-prefixed.

</aside>

## The Element Class

The Element is the PHP-side definition of your node: it takes props, resolves callbacks, and hands a props array to
the serializer. Extend `Native\Mobile\Edge\Element` and set `$type` to match the manifest:

```php
namespace MyVendor\MyWidget\Elements;

use Native\Mobile\Edge\CallbackRegistry;
use Native\Mobile\Edge\Element;

class MyWidget extends Element
{
    protected string $type = 'my_widget';

    protected array $componentProps = [];

    public static function make(): static
    {
        return new static;
    }

    public function value(mixed $value): static
    {
        $this->componentProps['value'] = $value;

        return $this;
    }

    public function onChange(string $method): static
    {
        $this->componentProps['on_change'] = $method;

        return $this;
    }

    /**
     * Map Blade attributes onto props. Called before layout/style parsing.
     */
    public function applyAttributes(array $attrs): void
    {
        if (isset($attrs['value'])) {
            $this->value($attrs['value']);
        }

        if (isset($attrs['_change'])) {
            $this->onChange($attrs['_change']);
        }
    }

    /**
     * Final props for the wire node. Register any handler method names here
     * so the renderer receives an integer callback id, not a string.
     */
    protected function resolveProps(CallbackRegistry $registry): array
    {
        $props = $this->componentProps;

        if (isset($props['on_change'])) {
            $props['on_change'] = $registry->register($props['on_change']);
        }

        return $props;
    }
}
```

Three hooks are worth knowing:

- **`applyAttributes(array $attrs)`** — your one chance to turn Blade attributes into props. Built-in elements are
  handled by an `instanceof` chain in the collector; plugin elements are not, so an attribute you don't read here is
  silently dropped.
- **`resolveProps(CallbackRegistry $registry)`** — returns the props map. Anything that is a PHP method name must go
  through `$registry->register()`, which returns the integer id your renderer sends back with the event.
- **`defaults()`, `layoutDefaults()`, `styleDefaults()`** — optional; merged under whatever the app sets.

Layout and styling come for free. Tailwind classes on the tag are parsed by the same parser core elements use, so
`class="w-full rounded-2xl bg-theme-surface"` populates the node's layout and style maps without you writing anything —
your renderer just applies the `modifier` it's handed.

## The Blade Component

A thin class that names the element type and declares whether it takes children:

```php
namespace MyVendor\MyWidget\Components;

use Native\Mobile\Edge\Components\Native\NativeBladeComponent;

class MyWidget extends NativeBladeComponent
{
    protected bool $isSelfClosing = true;

    protected function elementType(): string
    {
        return 'my_widget';
    }
}
```

Keep `$isSelfClosing` in sync with the manifest's `self_closing`. A container that reports itself self-closing swallows
its children; a leaf that doesn't will wait for a closing tag that never comes.

## The Android Renderer

A Kotlin `object` with a `@Composable fun Render(node, modifier)`. Files go in `resources/android/` — the package
declaration at the top decides where the compiler places the file, so always namespace it to your vendor:

```kotlin
package com.myvendor.plugins.mywidget.ui

import androidx.compose.foundation.layout.Box
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import com.nativephp.mobile.ui.nativerender.NativeUIBridge
import com.nativephp.mobile.ui.nativerender.NativeUINode
import com.nativephp.mobile.ui.nativerender.NodeView

object MyWidgetRenderer {
    @Composable
    fun Render(node: NativeUINode, modifier: Modifier) {
        val p = node.props

        val label = p.getString("value", "")
        val onChangeCb = p.getCallbackId("on_change")

        Box(modifier = modifier) {
            Text(text = label)

            // Children, if this is a container:
            // node.children.forEach { child -> NodeView(node = child) }
        }

        // Send an event back to PHP — the callback id routes it to the
        // method you registered in resolveProps():
        // NativeUIBridge.sendTextChangeEvent(onChangeCb, node.id, newValue)
    }
}
```

Props are read through typed accessors on `node.props` — `getString`, `getInt`, `getFloat`, `getBool`, `getColor`,
`getStringList`, and `getCallbackId` — each with a default. Apply the `modifier` you're given rather than building your
own sizing: it carries the layout the app's Tailwind classes resolved to.

## The iOS Renderer

A SwiftUI `View` struct with a single `node` property, in `resources/ios/`:

```swift
import SwiftUI

struct MyWidgetRenderer: View {
    let node: NativeUINode

    var body: some View {
        let p = node.props
        let label = p.getString("value", default: "")
        let onChangeCb = p.getCallbackId("on_change")

        Text(label)

        // Children, if this is a container:
        // ForEach(node.children) { child in NodeView(node: child) }

        // Send an event back to PHP:
        // NativeElementBridge.sendTextChangeEvent(onChangeCb, nodeId: node.id, value: newValue)
    }
}
```

The struct name must match `ios_renderer` exactly — it's referenced by name in generated Swift, so a typo surfaces as a
compile error in the app build, not a plugin error.

## How Registration Happens

You never call a register function yourself. At two points, the framework wires your declarations up:

**At boot (PHP)** — every installed plugin's `components` are read from its manifest. Each `element` class is added to
the element registry under its `type`, and each `blade` class is registered as a Blade component named
`native-{kebab-type}`. Core elements are registered first and are never overwritten.

**At build (native)** — the platform compilers copy your Kotlin and Swift sources into the app project and generate a
registration file per platform:

```kotlin
// PluginRendererRegistration.kt — generated
NativeRendererRegistry.register("my_widget", NodeRenderer { node, modifier ->
    MyWidgetRenderer.Render(node, modifier)
})
```

```swift
// PluginRendererRegistration.swift — generated
SwiftUIRendererRegistry.shared.register("my_widget") { AnyView(MyWidgetRenderer(node: $0)) }
```

Because the native half is generated at build time, **a newly added component needs a rebuild**, not just a
`composer update`. Adding a component to the manifest and only restarting PHP gives you an element that serializes
fine and renders nothing.

## Using It in an App

Install and register the plugin the [usual way](./using-plugins), then the element is just an element:

@verbatim
```blade static
<native:column class="p-4 gap-3">
    <native:my-widget :value="$label" _change="updateLabel" class="w-full h-40 rounded-2xl" />
</native:column>
```
@endverbatim

```php
class WidgetScreen extends NativeComponent
{
    public string $label = 'Hello';

    public function updateLabel(string $value): void
    {
        $this->label = $value;
    }
}
```

## Testing

Plugin elements work with the [component testing suite](../testing/introduction) like any other element — they appear
in the rendered tree under their `type`, and `ref="..."` targets them:

```php
use Native\Mobile\Testing\Native;

it('renders the widget', function () {
    Native::test(WidgetScreen::class)
        ->assertSee('Hello')
        ->assertElement('my_widget', fn ($node) => $node['props']['value'] === 'Hello');
});
```

`assertElement()` matches on the wire type — the same string you put in the manifest — and the optional matcher
receives the published node, so you can assert the props your `resolveProps()` produced.

That covers the PHP half — props resolve, callbacks register, the node lands in the tree. The renderers themselves are
native code and need a device or simulator; see [Validation & Testing](./validation-testing) for what to check before
you publish.

## Shipping a Composed Component Instead

If your component is just an arrangement of elements that already exist, skip all of the above. Ship a
`NativeComponent` subclass and its Blade view in your package, and register the tag from your service provider's
`boot()`:

```php
use Native\Mobile\Edge\ComponentRegistry;

class MyPackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'my-package');

        ComponentRegistry::components([
            'rating-stars' => \MyVendor\MyPackage\Components\RatingStars::class,
        ]);
    }
}
```

App developers then write `<native:rating-stars :score="$score" />` with no build step, no Kotlin, and no Swift — it's
a plain Composer package, and doesn't even need to be a NativePHP plugin. The trade-off is that it can only render what
EDGE already renders. See [Nested Components](../the-basics/nested-components) for props, state, and events.
