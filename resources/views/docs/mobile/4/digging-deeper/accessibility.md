---
title: Accessibility
order: 90
---

## Overview

EDGE components render real native controls, so most accessibility behavior comes free from the platform:
VoiceOver (iOS) and TalkBack (Android) recognize buttons, switches, checkboxes, sliders, and tabs as what they
are, text scales with the user's system font size, and interactive controls maintain minimum touch-target sizes
(44pt on iOS, 48dp on Android) even when they render smaller visually.

What the platform can't know is *what your UI means*. That's where the accessibility props come in.

## Accessibility props

Every element accepts two attributes:

- `a11y-label` - What the screen reader announces for the element. Overrides the visible text.
- `a11y-hint` - Supplementary usage guidance, announced after the label (e.g. "Double-tap to open settings").

@verbatim
```blade static
<native:button icon="gear" a11y-label="Settings" @press="openSettings" />
<native:slider :value="$volume" a11y-label="Volume" a11y-hint="Adjusts playback volume" />
```
@endverbatim

The same props are available on the fluent PHP API — `a11yLabel()` and `a11yHint()` live on the base element, so
**every** element and plugin component has them:

@verbatim
```php
Button::make()
    ->icon('gear')
    ->a11yLabel('Settings')
    ->onPress('openSettings');
```
@endverbatim

Components with visible text labels (buttons with labels, checkboxes, toggles, radios, tabs) announce their
visible text automatically — you only need `a11y-label` when there is no visible text or when the visible text
isn't descriptive enough on its own.

## Icon-only controls

**Always set `a11y-label` on icon-only buttons, chips, and tabs.** An icon-only control without a label is a
mystery to screen reader users.

@verbatim
```blade static
{{-- Bad: VoiceOver has nothing meaningful to announce --}}
<native:button icon="trash" variant="destructive" @press="deleteItem" />

{{-- Good --}}
<native:button icon="trash" variant="destructive" a11y-label="Delete item" @press="deleteItem" />
```
@endverbatim

## Icons and images

Standalone `<native:icon />` elements are **decorative by default** — they are hidden from screen readers
unless you give them an `a11y-label`. If an icon conveys meaning on its own (or is tappable), label it:

@verbatim
```blade static
<native:icon name="wifi_off" a11y-label="No internet connection" />
```
@endverbatim

Images take an `alt` attribute, mirroring the web. With `alt`, the image is announced; without it, the image
is treated as decorative and skipped:

@verbatim
```blade static
<native:image src="{{ $product->photo }}" alt="{{ $product->name }}" :height="200" />
```
@endverbatim

## List items

The tappable trailing icon button on a [list item](../edge-components/list) takes its own label via `trailing-a11y-label`:

@verbatim
```blade static
<native:list-item
    headline="Backups"
    trailingIconButton="info"
    trailing-a11y-label="Backup details"
    on-trailing-press="showBackupInfo"
/>
```
@endverbatim

`on-trailing-press` needs the `nativephp/mobile-ui` release that includes
[NativePHP/mobile-ui#108](https://github.com/NativePHP/mobile-ui/pull/108). On 0.6.0, use the fluent
`->onTrailingPress()` instead. See [List › Events](../edge-components/list#events).

List rows group their content (headline, supporting text, leading and trailing decorations) into a single
screen-reader focus stop; interactive trailing controls remain individually focusable.

## Navigation chrome

Icon-only top-bar actions built with `NavAction` are unlabeled for screen readers unless you set a label. Give
every icon-only action an `a11yLabel()`:

@verbatim
```php
NavBarOptions::make()->actions([
    NavAction::make('search')->icon('magnifyingglass')->a11yLabel('Search')->press('openSearch'),
    NavAction::make('more')->icon('ellipsis')->a11yLabel('More options')->items([...]),
]);
```
@endverbatim

## Composing your own controls

When you draw a control yourself from `<native:pressable>`, `<native:column>`, `<native:row>`, `<native:stack>` or `<native:gesture-area>`, the screen reader can't tell what it is from how it looks. The props below let it describe the control the way it describes a native one. They apply to `pressable`, `column`, `row`, `stack`, `gesture-area`, `text`, `icon` and `image`; native controls such as `<native:button>` and `<native:toggle>` already announce their own role and state.

### Role

`a11y-role` says what the element is:

- `button`: the default for a `pressable` with `@press`.
- `checkbox` and `switch`: use with `a11y-checked`.
- `radio` and `tab`: use with `a11y-selected`. Put them in a `radiogroup` or `tablist` container with an `a11y-label`.
- `dropdown`: a control that opens a list of choices. Use with `a11y-value` for the current choice.
- `adjustable`: a slider or stepper. Use with a value and `@a11yIncrement` / `@a11yDecrement`.
- `progress`: use with `a11y-value`, and the numbers when you have them.
- `header`, `image`, and `none` (no role at all, for a pressable that shouldn't read as a button).

@verbatim
```blade static
<native:pressable @press="toggleTerms"
    a11y-role="checkbox" :a11y-checked="$accepted" a11y-label="Accept the terms">
    <native:icon :name="$accepted ? 'check_box' : 'check_box_outline_blank'" />
    <native:text>Accept the terms</native:text>
</native:pressable>
```
@endverbatim

### State

- `a11y-checked`: `true`, `false` or `mixed`, for checkboxes and switches.
- `a11y-selected`: for radios, tabs and other selectable elements.
- `a11y-expanded`: for disclosure rows and dropdowns. TalkBack offers expand or collapse, which taps the element. Pass `false` rather than `null` while it's collapsed.
- `a11y-state`: a state in your own words when none of the above fits, such as "Downloading" or "3 unread".
- `a11y-error`: the element's error, such as a validation message. TalkBack reads it with the element's state; VoiceOver reads it after the value and announces it when it appears. Put it on an element with a label or a role, so the screen reader has an element to read it on. On a `radiogroup` or `tablist`, the screen reader stops on the group itself while it has an error, before its first option. If you also show the message as text, mark that text `a11y-hidden` so it isn't read twice.
- `disabled` (on `pressable`): stops `@press`, `@longPress`, `@doubleTap` and the press-down and press-up handlers, and announces the element as unavailable. The content is drawn as you wrote it, so style the disabled look yourself. On iOS, controls inside the pressable are disabled too.

@verbatim
```blade static
<native:column ref="priority" a11y-role="radiogroup" a11y-label="Priority"
    :a11y-error="$errors->first('priority')">
    @foreach ($priorities as $value => $label)
        <native:pressable @press="choose('{{ $value }}')" a11y-role="radio"
            :a11y-selected="$priority === $value" :disabled="$value === 'urgent' && ! $canEscalate">
            <native:text>{{ $label }}</native:text>
        </native:pressable>
    @endforeach
</native:column>
```
@endverbatim

VoiceOver has no checkbox, expanded or mixed state of its own, so it speaks "Checked", "Unchecked", "Mixed", "On", "Off", "Expanded" and "Collapsed" as the element's value. Add those keys to your app's `Localizable.xcstrings` to translate them.

### Values and adjusting

- `a11y-value`: the spoken value, such as "40%" or "3 of 5".
- `a11y-value-now`, `a11y-value-min`, `a11y-value-max`: the numbers behind it. TalkBack uses them for its range; VoiceOver reads `a11y-value`, or the number when there's no text.
- `@a11yIncrement` and `@a11yDecrement`: called when a screen-reader user swipes up or down on an `adjustable` element. Your handler decides the step.

@verbatim
```blade static
<native:gesture-area :pan-x="$dx" @dragEnd="setVolume"
    a11y-role="adjustable" a11y-label="Volume" a11y-value="{{ $volume }}%"
    :a11y-value-now="$volume" a11y-value-min="0" a11y-value-max="100"
    @a11yIncrement="louder" @a11yDecrement="quieter">
    ...
</native:gesture-area>
```
@endverbatim

### Actions for gestures

A swipe or a drag has no screen-reader equivalent. `:a11y-actions` takes the same `NavAction` items as `:context-menu` and offers them in the screen reader's actions menu:

@verbatim
```blade static
<native:gesture-area :pan-x="$dx" :snap-x="[-96, 0]"
    :a11y-actions="[
        NavAction::make('archive')->label('Archive')->press('archive('.$mail->id.')'),
        NavAction::make('delete')->label('Delete')->press('delete('.$mail->id.')'),
    ]">
    ...
</native:gesture-area>
```
@endverbatim

### Hiding decoration

`a11y-hidden` hides an element and everything inside it from the screen reader, for decoration that would only add noise.

### Your own dialogs and sheets

A container with `a11y-modal` is an overlay: while it's on screen, the screen reader stays inside it. Give it an `a11y-label`, which TalkBack announces as its title, and an `@a11yDismiss` handler, which runs on VoiceOver's escape gesture (a two-finger scrub) and on Android's back gesture.

@verbatim
```blade static
@if ($confirming)
    <native:stack a11y-modal a11y-label="Discard draft?" @a11yDismiss="cancel" class="absolute inset-0">
        ...
    </native:stack>
@endif
```
@endverbatim

`a11y-pane-title` names a part of the screen that appears or changes as a whole without being modal, such as a step in a wizard. The screen reader announces the title when it appears or changes.

## Telling users about changes

When something changes on screen, describe it on the element that changed, in this order:

1. The element's own state, error or title: `a11y-checked`, `a11y-selected`, `a11y-state`, `a11y-error`, `a11y-pane-title`.
2. `a11y-live="polite"` (or `"assertive"`) on an element whose text changes and matters, such as a sync status. On a container, it reads the container's label, or its children's text when it has none. The screen reader reads the new text each time a render changes it, so keep it for updates people need to hear.
3. `Accessibility::focus($ref)` to move the screen reader to an element, such as the first error after a failed submit. It moves once your handler's changes are on screen, so the element is read with the error you just gave it. Call it last in the handler, since it waits only half a second for those changes. Use it for moves the person caused, not to pull focus elsewhere. The move interrupts whatever the screen reader is saying, including a live update from the same handler, so don't rely on both at once. It does nothing while no screen reader is running, and it leaves keyboard focus alone.
4. `Accessibility::announce($message)` only for a message that belongs to nothing on screen.

@verbatim
```php
use Native\Mobile\Facades\Accessibility;

public function submit(): void
{
    if (! $this->validate()) {
        Accessibility::focus('priority');

        return;
    }

    Accessibility::announce(__('Draft saved'));
}
```
@endverbatim

Android 16 deprecated announcements that aren't tied to an element, because accessibility services may ignore them ([Android's guidance](https://developer.android.com/reference/android/view/View#announceForAccessibility(java.lang.CharSequence))). `announce()` uses a live region on Android rather than the deprecated call, but TalkBack may still read it differently from an element's own state.

## What you get for free

- **Font scaling** — All component text respects the user's system font size (Dynamic Type on iOS, font scale
  on Android). Fixed-size text is not used anywhere in the component set.
- **Touch targets** — Small visual controls (checkboxes, radios, chips, icon buttons, nav actions) keep their
  compact look but expand their tappable area to the platform minimum.
- **State announcements** — Toggles, checkboxes, radios, chips, tabs, and sliders announce their current state
  ("Checked", "Selected", values) and stateful buttons announce loading.
- **Reduced motion** — On iOS, drawer and screen transitions respect the system Reduce Motion setting.
- **Contrast** — The default theme palette meets WCAG AA (4.5:1) for text on colored surfaces. If you customize
  the theme, keep your `on*` colors at 4.5:1 against their backgrounds.

## Testing accessibility

The [testing suite](../testing/accessibility) can audit a rendered screen for missing labels
without a device. Sweep every screen in one data-driven test so a new screen can't ship an unlabeled control:

@verbatim
```php
use Native\Mobile\Edge\NativeRouter;

it('renders every screen accessibly', function () {
    foreach (array_keys(NativeRouter::registeredRoutes()) as $uri) {
        Native::visit($uri)->assertAccessible();
    }
});
```
@endverbatim

<aside>

Manual pass, too: turn on VoiceOver (iOS) or TalkBack (Android) and swipe through each screen. Every focus stop
should announce something a person could act on. Anything announced as a raw icon name, an empty element, or
"button" with no label needs an `a11y-label`.

</aside>
