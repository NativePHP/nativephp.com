---
title: Interactions
order: 200
---

## Overview

Interactions drive a screen the same way a person would — pressing buttons, typing into fields, flipping toggles. Each
one dispatches a real event through the component's normal event path and then re-renders, so the assertions after it
see the updated frame.

Every interaction method returns the harness, so they chain fluently.

## Targeting elements

Most interactions take a `target`. There are two ways to point at an element.

### By visible text

`tap()` finds the nearest pressable element whose subtree contains the given text. This is the most natural way to
press a labelled button or card:

```php
Native::test(Dashboard::class)->tap('Refresh');
```

### By `ref`

For anything without unique visible text — an icon-only button, a specific input, a card among many — give the
element a `ref` and target it by name. A `ref` works in Blade on any element:

@verbatim
```blade
<native:pressable ref="vibrate-card" @press="vibrate">
    <native:text>Tap to vibrate</native:text>
</native:pressable>

<native:text-input ref="message-input" wire:model="message" />
```
@endverbatim

...and via `->ref()` on element builders:

```php
Pressable::make()->ref('vibrate-card')->onPress('vibrate');
```

Then target that name from the test:

```php
Native::test(HapticsDemo::class)
    ->tap('vibrate-card')
    ->assertSet('buzzes', 1);
```

Refs make a test read cleanly and survive copy changes — the label can move without breaking the test.

## Pressing

- `tap($target)` — press by `ref` or visible text (the friendliest default).
- `press($target)` — press an element bound to a method, expression, or `ref`.
- `longPress($target)` — fire a long-press.

```php
it('opens the menu on long press', function () {
    Native::test(Gallery::class)
        ->longPress('photo-1')
        ->assertSee('Delete');
});
```

## Inputs and form controls

Each control fires the same event its native counterpart emits:

- `input($target, $text)` — type into a text field.
- `submit($target, $text = '')` — submit a field (e.g. the return key).
- `toggle($target, $value)` — flip a toggle on or off.
- `check($target, $value = true)` — check or uncheck a checkbox.
- `slide($target, $value)` — move a slider to a float value.
- `select($target, $value)` — choose a value in a select.
- `selectRadio($target, $value)` — pick a radio option.
- `changeTab($target, $index)` — switch a tab row to an index.
- `dismissSheet($target)` — dismiss a bottom sheet.

Fields bound with `wire:model` sync their property as you'd expect:

```php
it('fills and submits the toast form', function () {
    Native::visit('/dialogs/toast')
        ->input('message-input', 'Hello from CI')
        ->assertSet('message', 'Hello from CI')
        ->press('show-toast')
        ->assertNativeCalled('Dialog.Toast', fn (array $p) => $p['message'] === 'Hello from CI');
});
```

## Setting properties and calling methods

Two lower-level tools reach the component directly.

`set($property, $value)` sets a public property through the same binding path a native input uses — so it fires any
`updatedFoo()` hook — then re-renders:

```php
Native::test(SecureStorageDemo::class)
    ->set('key', 'api-key')
    ->set('value', 's3cret')
    ->call('store');
```

`call($method, ...$args)` invokes a component method directly, then re-renders. Reach for it to trigger logic that
isn't wired to a visible control, or to set up state:

```php
Native::test(Home::class)
    ->call('doubleTapped')
    ->assertSet('gesture', 'Double-tapped!');
```

## The generic primitive: `fireEvent()`

All the input sugar above is built on `fireEvent($target, $type, $fields = [])`, which dispatches a wire event at the
callback registered for a target. You rarely need it directly, but it's there when you're driving a custom element
that emits an event the sugar doesn't cover:

```php
Native::test(CustomControl::class)
    ->fireEvent('custom-widget', TestableComponent::EVENT_SLIDER_CHANGE, ['value' => 0.5]);
```

## Polling

Screens that refresh with `#[Poll]` don't wait for a timer under test — you fire them on demand.

- `firePolls()` fires every `#[Poll]` method immediately and re-renders, as if all timers came due at once.
- `firePoll($method)` fires a single `#[Poll]` method by name.

```php
it('polls the native recording status on demand', function () {
    Native::fakeBridge()->respondTo('Microphone.GetStatus', ['status' => 'recording']);

    Native::test(MicrophoneDemo::class)
        ->call('startRecording')
        ->firePoll('pollStatus')
        ->assertSet('status', 'Native status: recording')
        ->assertSee('Native status: recording');
});
```

## Search

For screens that override `onSearchQuery()`, run a query through the same handler the runloop uses. `search($query)`
drives the handler and stages the results for the next frame; `searchResults()` returns those staged results:

```php
it('filters the contact list', function () {
    $screen = Native::test(ContactSearch::class)->search('ada');

    expect($screen->searchResults())->toHaveCount(1);
    $screen->assertSee('Ada Lovelace');
});
```

## Back button

`pressBack()` simulates the system back gesture or hardware back button:

```php
Native::test(EditPost::class)
    ->pressBack()
    ->assertWentBack();
```

See [Navigation & Flows](navigation) for asserting on where back takes you.
