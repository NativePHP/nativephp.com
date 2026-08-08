---
title: Validation
order: 158
---

## Overview

Components validate their public properties with the same primitives you use in a controller: `$this->validate()`
and `$this->validateOnly()` run Laravel's validator against the component's props and throw Laravel's own
`ValidationException` on failure. The dispatch cycle catches it for you — the handler aborts at the `validate()`
line, component state is kept, and the next frame renders with `$errors` populated. The behavior is identical on
device and on the web preview, because both route event dispatch through the same guard.

Rules come from three sources, merged in order (later wins per key):

1. `#[Validate]` attributes on public props — these are **eager** and also re-run automatically on `native:model` sync
2. The component's `rules()` method — on-demand, only runs when you call `validate()`
3. An inline array passed to `validate([...])` — validates only those rules

## A complete example

A form screen with a bound input, a save handler, and inline error display:

```php
use Illuminate\View\View;
use Native\Mobile\Attributes\Validate;
use Native\Mobile\Edge\NativeComponent;

class ProfileScreen extends NativeComponent
{
    #[Validate('required|email')]
    public string $email = '';

    public string $bio = '';

    protected function rules(): array
    {
        return ['bio' => 'required|max:160'];
    }

    public function save(): void
    {
        $validated = $this->validate();

        auth()->user()->update($validated);
    }

    public function render(): View
    {
        return view('screens.profile');
    }
}
```

@verbatim
```blade static
<native:column class="w-full gap-4 p-4">
    <native:outlined-text-input
        label="Email"
        native:model.blur="email"
        keyboard="email"
        :error="$errors->has('email')"
        :supporting="$errors->first('email')"
    />

    <native:outlined-text-input
        label="Bio"
        native:model="bio"
        multiline
    />
    @error('bio')
        <native:text class="text-sm text-theme-destructive">{{ $message }}</native:text>
    @enderror

    <native:button label="Save" @press="save" />
</native:column>
```
@endverbatim

Tapping Save with an empty form calls `validate()`, which fails: `save()` stops before the `update()` line, the
typed values stay in place, and the screen re-renders with both messages showing. Fix the fields and tap again —
a passing `validate()` clears the whole error bag and returns the validated data.

The email field re-validates as you edit, because its rule lives in a `#[Validate]` attribute; the bio rule lives
in `rules()` and only runs when `save()` calls `validate()`.

## The #[Validate] attribute

Declare rules directly on a public prop with `Native\Mobile\Attributes\Validate`:

```php
use Native\Mobile\Attributes\Validate;

#[Validate('required|min:3')]
public string $title = '';

#[Validate(['required', 'min:8'])]
public string $password = '';
```

Attribute rules are **eager**: whenever the prop syncs through `native:model`, the framework re-validates just that
property — after your `updatedTitle()` hook runs, so a hook that normalizes the value validates the normalized form.
A failure records the error and aborts cleanly; the synced value itself is never rolled back. Once a later sync
passes, the field's error clears.

Your `native:model` modifier is the validation cadence: `.live` validates per keystroke, `.blur` on focus loss,
`.debounce` after the debounce window. There is no separate validation timing knob — pick the sync mode that matches
how often you want feedback.

Only attribute rules run on sync. Rules declared in `rules()` never fire per keystroke, even when they target the
same property — put expensive rules (`unique:`, `exists:`) there if you don't want them running while the user types.

The attribute is repeatable — stacked declarations merge their rules:

```php
#[Validate('required')]
#[Validate('min:3')]
public string $title = '';
```

When merging, string rules are exploded on `|`, so use the array form for rules that legitimately contain a pipe
(`regex:`) — the same caveat as Laravel's string rule parsing.

<aside>

PHP attribute arguments must be constant expressions, so rule objects (`Rule::in()`, `Password::min()`, closures)
can't go inside `#[Validate]`. Declare those in `rules()` or an inline `validate([...])` array instead.

</aside>

## rules() and messages()

For on-demand rules — everything that should only run when a handler explicitly validates — declare a `rules()`
method. Rule objects and closures work here:

```php
use Illuminate\Validation\Rule;

protected function rules(): array
{
    return [
        'email' => ['required', 'email', Rule::unique('users')],
        'plan' => Rule::in(['free', 'pro']),
        'tags.*' => 'required|string',
    ];
}
```

Customize messages and field names with `messages()` and `validationAttributes()`:

```php
protected function messages(): array
{
    return ['email.required' => 'We need your email address.'];
}

protected function validationAttributes(): array
{
    return ['email' => 'email address'];
}
```

When a key appears in both a `#[Validate]` attribute and `rules()`, the `rules()` entry wins for `validate()` calls —
but the eager sync path still uses only the attribute's rules.

### Validating a single property

`validateOnly('email')` runs just that property's rules and only ever touches that property's errors. It's
wildcard-aware: `validateOnly('tags.1')` matches a `tags.*` rule, and even though the wildcard rule sweeps the whole
array while running, only `tags.1`'s bag entries are replaced or cleared — untouched siblings keep their state.

## Inline rules

Pass rules straight to `validate()` to check only those, ignoring everything declared elsewhere:

```php
public function subscribe(): void
{
    $validated = $this->validate(
        ['email' => 'required|email'],
        ['email.required' => 'An email is required to subscribe.'],
    );

    // ...
}
```

The second and third arguments are messages and attribute names, exactly like `Validator::make()`. A failing inline
validation refreshes only the keys it ran — it won't clear an unrelated on-screen error it never re-checked.

## Sharing a FormRequest

`validate()` also accepts a `FormRequest` class-string, so a screen and an HTTP controller can share one rule
definition. The request's `rules()`, `messages()`, and `attributes()` are harvested:

```php
public function save(): void
{
    $validated = $this->validate(StorePostRequest::class);

    // ...
}
```

`rules()` is resolved through the container, so method-injected dependencies (`rules(PlanService $plans)`) work like
they do in a controller-bound request. Two caveats:

- The request is only read, never handled — `authorize()` is **not** called. Authorize in the handler if you need to.
- The data under validation is the component's public props, not an HTTP payload. A rule for a request-only field
  behaves as it would for an absent field (`required` fails, non-implicit rules pass) — share a FormRequest only when
  its fields map onto props.

## Displaying errors

Error **display is always explicit** — the same semantics as Livewire. A failed validation records messages in the
bag but never changes any element's appearance on its own: a bound text input does not turn red, no supporting text
appears. You decide where and how errors render.

`$errors` is a standard `ViewErrorBag` in every component view, so Laravel's `@@error` directive works exactly as it
does in a web Blade template:

@verbatim
```blade static
@error('email')
    <native:text class="text-sm text-theme-destructive">{{ $message }}</native:text>
@enderror
```
@endverbatim

The `@@nativeError` directive is the one-line shorthand — it renders the field's first message as a small
error-colored text element (nothing at all when the field has no error). An optional second argument overrides the
color, which defaults to `#FF0000`:

@verbatim
```blade static
@nativeError('email')
@nativeError('bio', '#B00020')
```
@endverbatim

To show the error inside the input itself, feed the bag into the input's `error` and `supporting` attributes —
today, [text inputs](../edge-components/text-input) are the elements that display them:

@verbatim
```blade static
<native:outlined-text-input
    label="Email"
    native:model.blur="email"
    :error="$errors->has('email')"
    :supporting="$errors->first('email')"
/>
```
@endverbatim

The `error` flag turns the field's border, indicator, and supporting text to the theme's destructive color;
`supporting` carries the message below the field.

<aside>

`@@nativeError` also accepts the older hand-rolled array form — a component that still declares
`public array $errors = ['field' => 'message']` keeps rendering. That pattern is deprecated: a public `$errors` prop
shadows the injected `ViewErrorBag`, so `$this->validate()` output can never reach that component's views, and a
deprecation notice fires. Rename the prop and use the validator instead.

</aside>

## Manual control

Manage the bag directly when validation rules aren't the right fit:

```php
// Record an error by hand
$this->addError('email', 'That address bounced last time.');

// Clear one field's errors, or everything
$this->resetValidation('email');
$this->resetValidation();
```

Throwing Laravel's `ValidationException::withMessages()` from any handler behaves like a failed `validate()`: the
handler aborts, the messages fold into the bag per key, and the frame renders with them:

```php
use Illuminate\Validation\ValidationException;

public function pay(): void
{
    if (! $this->gateway->charge($this->amount)) {
        throw ValidationException::withMessages([
            'amount' => 'The payment could not be processed.',
        ]);
    }
}
```

Two bag rules worth knowing:

- A **passing** full `validate()` clears the entire bag, including `addError()` entries. A **failing** validation
  refreshes only the keys it actually ran and leaves the rest alone — so two listeners failing on different fields
  during one event delivery both keep their messages.
- Every component owns its own bag. A [nested child component](nested-components)'s errors never appear in the
  parent's `$errors`, and a parent failure never leaks into a child — each screen region reports only its own state.
