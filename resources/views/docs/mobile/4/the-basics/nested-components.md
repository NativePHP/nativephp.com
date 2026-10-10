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

Several methods can listen for one event. They all run, in the order they are declared in the class.

`#[On]` also takes a `when` filter <x-docs.version-badge since="TODO" />, which is matched against the **named**
arguments of the event. A positional argument has no name, so it never matches a filter:

```php
use Native\Mobile\Attributes\On;

// Emitted with names: $this->emit('task-toggled', title: $this->title, done: $this->done);
#[On('task-toggled', when: ['done' => true])]
public function onTaskDone(string $title): void
{
    // ...
}
```

See [Filtering with when](events#filtering-with-when) for the rules.

<aside>

String-form `#[On('...')]` (component events) and class-form `#[On(PhotoTaken::class)]` (native device events)
are different mechanisms sharing one attribute. Class-form listeners stay on the screen — see
[Events](events).

</aside>

## Dispatching events

`emit()` calls its listeners right away, and the event only travels up. `$this->dispatch('event-name', ...$params)`
queues the event instead. It is delivered after the current action returns, and before the screen renders.

```php
namespace App\NativeComponents;

use Native\Mobile\Edge\NativeComponent;

class TaskCard extends NativeComponent
{
    public int $taskId = 0;

    public function archive(): void
    {
        $this->dispatch('task-archived', id: $this->taskId);
    }
}
```

A dispatched event goes to, in this order:

1. the `#[On('task-archived')]` methods of the component that dispatched it,
2. the `@task-archived` binding on that component's mounting tag,
3. the `#[On('task-archived')]` methods of every ancestor, up to the screen.

So unlike `emit()`, `dispatch()` also reaches the component itself. A screen has no ancestors, and it can still
dispatch an event to its own `#[On]` methods.

Listen for a dispatched event as for an emitted one, with a string-form `#[On]`. Pass the parameters by name, as in
`id: $this->taskId`, and they are bound to the method's parameters by name.

Because the event waits until the action returns, you can narrow where it goes. `dispatch()` returns the event, and
`->self()` and `->to()` set its destination.

### Only the component itself

`->self()` keeps the event inside the component that dispatched it. Only its own `#[On]` methods run. No tag binding
and no ancestor hears it:

```php
$this->dispatch('draft-saved')->self();
```

### Components of one class

`->to()` takes a component class name. The event goes to every component on the screen that is an instance of that
class, wherever it sits: the screen itself, an ancestor, a sibling or a child. Nothing else hears it:

```php
use App\NativeComponents\TaskCounter;

$this->dispatch('task-archived', id: $this->taskId)->to(TaskCounter::class);
```

Pass a component instance instead of a class name to reach only that one component.

<aside>

The parameter names `self`, `to` and `component` are read as the destination:
`$this->dispatch('task-archived', to: TaskCounter::class)` does the same as `->to(TaskCounter::class)`. Give your own
parameters other names.

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

The harness records every event that was sent with `dispatch()`, so you can assert on those. An `emit()` is not
recorded:

```php
Native::test(TaskBoard::class)
    ->tap('archive-7')
    ->assertDispatched('task-archived', id: 7)
    ->assertNotDispatched('task-restored');
```

`assertDispatchedTo(TaskCounter::class, 'task-archived')` also checks the class that was given to `->to()`.

## Children vs. everything else

- **Repeated UI with behavior** (a card with its own tap handlers and state) → a nested component.
- **Shared markup with no behavior** → a plain Blade `@@include` / `$this->partial()` still works and is cheaper.
- **Chrome** (bars, fabs) → the [inline chrome elements](../edge-components/top-bar) or a
  [layout](layouts) — chrome hoists onto the native chrome root, which components don't.
