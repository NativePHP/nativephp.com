---
title: Accordion
order: 90
---

## Overview

An expandable disclosure section — a tappable header row that opens and closes a collapsible content area.
Renders as a SwiftUI `DisclosureGroup` on iOS and a Compose column with an `AnimatedVisibility` content area
on Android.

An accordion wraps two child tags: a [`<native:accordion-header>`](#props) holding the always-visible row, and a
[`<native:accordion-content>`](#props) holding the collapsible body. Both accept any child elements.

@verbatim
```blade
@php $showSpecs = false; @endphp

<native:accordion :expanded="$showSpecs" @change="toggleSpecs">
    <native:accordion-header>
        <native:text class="text-base font-semibold">Specifications</native:text>
    </native:accordion-header>
    <native:accordion-content>
        <native:text class="text-sm text-theme-on-surface-variant">Weight — 1.24 kg</native:text>
        <native:text class="text-sm text-theme-on-surface-variant">Battery — up to 18 hours</native:text>
    </native:accordion-content>
</native:accordion>
```
@endverbatim

`showSpecs` is a public bool property on your component — the `@php` line stands in for
`public bool $showSpecs = false;` — and `toggleSpecs()` is called with the new state whenever the user
taps the header.

Both platforms draw their own trailing chevron — the `DisclosureGroup` indicator on iOS, a rotating Material
arrow on Android — so don't add one to your header content. Tapping anywhere on the header row toggles the
section on both platforms.

## Props

- `expanded` - Whether the content area is open (optional, boolean, default: `false`). This is a live binding,
  not just a mount-time hint — assigning the bound property from PHP animates the group open or closed at any
  point, so patterns like "expand all" work
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

`<native:accordion-header>` and `<native:accordion-content>` take no props of their own beyond the same
`a11y-label` / `a11y-hint` pair — they exist to mark which children belong to which slot.

## Events

- `@change` - Component method called when the user expands or collapses the section. Receives the new
  expanded state as a boolean parameter

<aside>

The direct children of `<native:accordion>` must be one `<native:accordion-header>` and one
`<native:accordion-content>`. The renderers pick each slot by child type — any other direct child is
silently ignored.

</aside>

## Two-way Binding

The accordion's state prop is `expanded`, not `value` — and the `native:model` directive expands to a
`:value` binding, which the accordion doesn't read. So unlike [`<native:toggle>`](toggle), `native:model`
alone won't drive an accordion. Write the pair explicitly: bind `:expanded` and point `@change` at
`__syncProperty`, the same sync handler `native:model` generates.

@verbatim
```blade
@php $showDetails = false; @endphp

<native:accordion :expanded="$showDetails" @change="__syncProperty('showDetails')">
    <native:accordion-header>
        <native:text class="text-base font-semibold">Order details</native:text>
    </native:accordion-header>
    <native:accordion-content>
        <native:text class="text-sm text-theme-on-surface-variant">3 items · Arrives Thursday</native:text>
    </native:accordion-content>
</native:accordion>

<native:text class="text-sm text-theme-on-surface-variant">{{ $showDetails ? 'Expanded' : 'Collapsed' }}</native:text>
```
@endverbatim

Tapping the header syncs `showDetails` back automatically — the echo below updates on every toggle. The sync
runs both ways: set `$showDetails` from any component method and the group animates to match. Both renderers
echo-prevent, so adopting a programmatic value never fires `@change` back at your component.

`sync-mode` and `debounce-ms` are not accepted — a header tap is a single discrete event, so there is nothing
to defer or debounce.

## Examples

### FAQ list

@verbatim
```blade
@php $faqs = [
    ['q' => 'How do I reset my password?', 'a' => 'Use the link on the sign-in screen.'],
    ['q' => 'Can I change my plan later?', 'a' => 'Yes — upgrades apply immediately, downgrades at renewal.'],
]; @endphp

<native:column class="w-full gap-0 px-4">
    @foreach ($faqs as $faq)
        <native:accordion>
            <native:accordion-header>
                <native:text class="text-base font-medium">{{ $faq['q'] }}</native:text>
            </native:accordion-header>
            <native:accordion-content>
                <native:text class="text-sm text-theme-on-surface-variant pb-3">{{ $faq['a'] }}</native:text>
            </native:accordion-content>
        </native:accordion>
        <native:divider />
    @endforeach
</native:column>
```
@endverbatim

Accordions without `:expanded` start collapsed, and without `@change` they still toggle natively — wire the
event only when your component needs to know.

## Element

```php
use Native\Mobile\UI\Elements\Accordion;
use Native\Mobile\UI\Elements\AccordionContent;
use Native\Mobile\UI\Elements\AccordionHeader;
use Native\Mobile\UI\Elements\Text;

Accordion::make(
    AccordionHeader::make(Text::make('Specifications')),
    AccordionContent::make(Text::make('Weight — 1.24 kg')),
)
    ->expanded(true)
    ->onChange('toggleSpecs');
```

- `make(Element ...$children)` - Create an accordion from its header and content children
- `expanded(bool $expanded)` - Open or close the content area
- `onChange(string $method)` - Component method invoked when the user toggles the section
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint

`AccordionHeader::make(...)` and `AccordionContent::make(...)` take their child elements the same way and
expose only the two a11y setters.
