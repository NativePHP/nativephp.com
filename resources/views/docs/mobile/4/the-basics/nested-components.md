---
title: Nested Components
order: 156
---

## Overview

Any `NativeComponent` can mount other components as **children** — the unit of reuse for repeated UI: cards, rows,
chips, list items. A child is a real component, not an include: it receives **props** that stay live as the parent
re-renders, it keeps its **own state** between renders, its `@tap` and `native:model` bindings dispatch to *it*,
and it talks back to its ancestors with **events**. If you know Livewire's nested components, this is that — for
native views.

```php
namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class TaskCard extends NativeComponent
{
    // Props — assigned from the mounting tag's attributes.
    public string $title = '';

    // Own state — persists across parent re-renders.
    public bool $done = false;

    public function toggle(): void
    {
        $this->done = ! $this->done;

        $this->emit('task-toggled', $this->title, $this->done);
    }

    public function render(): View
    {
        return view('native.task-card');
    }
}
```

@verbatim
```blade static
{{-- resources/views/native/task-card.blade.php --}}
<native:column class="w-full rounded-2xl bg-theme-surface p-4 {{ $done ? 'opacity-60' : '' }}">
    <native:pressable @tap="toggle">
        <native:text class="text-base font-semibold text-theme-on-surface">{{ $title }}</native:text>
    </native:pressable>
</native:column>
```
@endverbatim

Mount it from any screen (or from another child — nesting is recursive):

@verbatim
```blade static
@foreach ($tasks as $task)
    <native:task-card
        :title="$task['title']"
        key="task-{{ $task['id'] }}"
        @task-toggled="onTaskToggled" />
@endforeach
```
@endverbatim

## Registering components

Classes under `app/NativeComponents` register **automatically** as tags by their kebab-cased class name —
`TaskCard` becomes `<native:task-card>`, `UserAvatarChip` becomes `<native:user-avatar-chip>`. That's the same
directory `php artisan native:make` scaffolds into, so screens and children live side by side; any of them can be
mounted as a child.

Classes living elsewhere register explicitly (a service provider's `boot()` is the natural home):

```php
use Native\Mobile\Edge\ComponentRegistry;

ComponentRegistry::components([
    'user-card' => \App\Support\Cards\UserCard::class,
]);
```

<aside>

Registered **element** names always win over component tags — you can't shadow `<native:column>` with a component
called `Column`.

</aside>

A Composer package can register components the same way from its own service provider's `boot()` — that's the
lightest way to distribute reusable UI. When you need a genuinely new element rather than an arrangement of existing
ones, see [UI Component Plugins](../plugins/ui-components).

## Props

Tag attributes assign to the child's matching public properties:

- Attribute names map kebab → camelCase: `:task-id="$task['id']"` assigns `$taskId`.
- `:prop="expression"` binds any Blade expression; plain attributes pass strings, coerced to the property's
  scalar type (`level="3"` assigns `int 3` to `public int $level`).
- Attributes with no matching public property are ignored.

Props are re-assigned on **every** parent render, so they stay live: when the parent's data changes, the child
re-renders with the fresh values. Everything *else* on the child is its own state — see below.

## Own state and keys

A child's non-prop public properties persist across parent re-renders — the `$done` flag above survives the
parent updating `$tasks`, adding rows, or reordering the list. What ties state to "the same" child is its
**identity**:

- With a `key` attribute, identity follows the key. `key="task-@{{ $task['id'] }}"` means the card for task 7 keeps
  its state wherever it moves in the list — through reorders, insertions, and removals.
- Without a `key`, identity falls back to the tag name plus its occurrence position in the parent. On reorder,
  state stays with the *position*, not the data — the classic list trap.

> [!IMPORTANT]
> Key list children by a **stable domain id** (`$task->id`), never the loop index. `$loop->index` *is* the
> position, so it pins state to slots instead of data and behaves exactly like having no key at all.

## Events up

A child calls `$this->emit('event-name', ...$args)` and the event **bubbles to every ancestor**, two ways:

**Tag bindings** — an `@event-name` attribute on the mounting tag maps the emit onto a parent method. Bound
arguments come first, emit arguments are appended:

@verbatim
```blade static
<native:task-card :title="$task['title']" @task-toggled="onTaskToggled('board')" />
```
@endverbatim

```php
// TaskCard emitted: $this->emit('task-toggled', $this->title, $this->done);
public function onTaskToggled(string $source, string $title, bool $done): void
{
    // $source = 'board' (bound), then the emit args
}
```

**`#[On]` listeners** — a string-form `#[On('event-name')]` method fires on *any* ancestor, however deep the
emitter is. A grandchild's emit reaches the screen without the intermediate child forwarding anything:

```php
use Native\Mobile\Attributes\On;

#[On('task-toggled')]
public function onAnyTaskToggled(string $title, bool $done): void
{
    // ...
}
```

<aside>

String-form `#[On('...')]` (component events) and class-form `#[On(PhotoTaken::class)]` (native device events)
are different mechanisms sharing one attribute. Class-form listeners stay on the screen — see
[Events](events).

</aside>

## Lifecycle

- `mount()` runs when a child's key first appears in the parent's tree.
- `unmount()` runs when the key disappears — a removed list row gets its hook before the instance is dropped.
- Children share the **screen's** run loop. A class-level `#[Poll]` on a child does not schedule timers; use
  `native:poll` on an element inside the child's Blade instead — that rolls up into the screen's frame timers.

## Inside a child

Bindings in a child's view resolve against **that child instance**, not the screen:

- `@tap="toggle"` calls the child's `toggle()`, with the child as `$this`.
- `native:model="note"` syncs the child's `$note`, and fires the child's `updatedNote()` hook.
- Navigation calls (`$this->navigate()`, `back()`, `replace()`) forward to the screen — a child can trigger
  navigation without knowing where it's mounted.

## No slot content

Content between a component's tags is not supported and throws a clear exception — pass data through props:

@verbatim
```blade static
{{-- Throws ComponentSlotNotSupportedException --}}
<native:task-card>
    <native:text>Nope</native:text>
</native:task-card>

{{-- Do this instead --}}
<native:task-card :title="$task['title']" />
```
@endverbatim

## Testing

The [component test harness](../testing/introduction) drives children through the screen exactly as the device
would: `tap('some-ref')` on a ref inside a child dispatches to the child's method, `input()` syncs the child's
model, and the child's state persists across `set()`-triggered parent re-renders — assert on what the screen
shows rather than reaching into child internals.

```php
Native::test(TaskBoard::class)
    ->tap('toggle-7')                 // ref rendered inside the task-7 child
    ->assertSee('1 done');            // tag binding updated the screen
```

## Children vs. everything else

- **Repeated UI with behavior** (a card with its own tap handlers and state) → a nested component.
- **Shared markup with no behavior** → a plain Blade `@@include` / `$this->partial()` still works and is cheaper.
- **Chrome** (bars, fabs) → the [inline chrome elements](../edge-components/top-bar) or a
  [layout](layouts) — chrome hoists onto the native chrome root, which components don't.
