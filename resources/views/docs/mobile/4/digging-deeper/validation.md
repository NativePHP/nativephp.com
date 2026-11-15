---
title: Validation
order: 25
---

## Overview

<x-docs.version-badge since="4.7" />

The `ValidatesInput` trait lets a SuperNative screen validate its own state with Laravel's validator. Add the trait,
define your rules, and call `$this->validate()`. The validator checks the component's public properties. If any of
them fail, the messages are kept in an error bag and the screen re-renders with them, so the user sees what to fix
and no error overlay appears.

```php
use Native\Mobile\Edge\Concerns\ValidatesInput;
use Native\Mobile\Edge\NativeComponent;

class SignupScreen extends NativeComponent
{
    use ValidatesInput;

    public string $email = '';

    protected function rules(): array
    {
        return ['email' => 'required|email'];
    }

    public function save(): void
    {
        $this->validate();

        // Only reached when every rule passes.
        User::create(['email' => $this->email]);
    }
}
```

@verbatim
```blade static
<native:column>
    <native:outlined-text-input native:model.blur="email" label="Email" />

    @error('email')
        <native:text>{{ $message }}</native:text>
    @enderror

    <native:button label="Sign up" @press="save" />
</native:column>
```
@endverbatim

If you've validated a Livewire component, this will look familiar. The trait is opt-in, so screens that don't use it
are unchanged, and it can't clash with a `validate()` method you've already written yourself.

## Defining rules

Return your rules from a `rules()` method, keyed by public property name. Any rule Laravel supports works here,
including rule objects and array syntax:

```php
use Illuminate\Validation\Rule;

protected function rules(): array
{
    return [
        'name' => 'required|string|max:100',
        'email' => ['required', 'email', Rule::unique('users')],
        'age' => 'nullable|integer|min:18',
    ];
}
```

If your rules don't need any logic, you can use a `$rules` property instead:

```php
protected array $rules = [
    'email' => 'required|email',
];
```

The data being validated is every public property on the component, so rules such as `same:password` or
`required_if:plan,pro` can refer to other properties.

### Custom messages and attribute names

Define `messages()` to replace the default message for a field and rule, and `validationAttributes()` to change how
a field is named inside the default messages:

```php
protected function messages(): array
{
    return [
        'email.unique' => 'An account with this email already exists.',
    ];
}

protected function validationAttributes(): array
{
    return [
        'email' => 'email address', // "The email address field is required."
    ];
}
```

Both are optional. When you pass messages or attributes directly to `validate()` or `validateOnly()`, those take the
place of the methods for that call.

## validate() and validateOnly()

`validate()` runs every rule. When they all pass, it clears the errors for those fields and returns the validated
data. When any fail, it replaces the whole error bag with the new messages and throws a `ValidationException`, so
nothing after the call runs:

```php
public function save(): void
{
    $validated = $this->validate();

    Profile::update($validated);
    $this->navigate('/profile');
}
```

`validateOnly()` checks a single field and only updates that field's messages. Errors on other fields stay as they
are. This makes it a good fit for an [`updated{Property}()`](lifecycle-hooks#updatedproperty) hook, so the user gets
feedback as they leave a field instead of waiting until they submit:

```php
public function updatedEmail(): void
{
    $this->validateOnly('email');
}
```

Pair this with a `.blur` or `.debounce` [sync modifier](data-binding#sync-modifiers) so the field isn't validated on
every keystroke.

Both methods also accept their own rules, messages and attributes, which take the place of `rules()` and the other
methods for that call:

```php
$this->validate(
    ['code' => 'required|digits:6'],
    ['code.digits' => 'Enter the 6-digit code from the email.'],
);
```

<aside>

Since 4.9, `validateOnly()` also applies wildcard rules, so `validateOnly('items.0.name')` checks the field against
an `items.*.name` rule. As in Laravel, each `*` stands for one segment of the key. In 4.7 and 4.8, `validateOnly()`
finds a rule only by its exact key, so run `validate()` for wildcard rules there.

</aside>

### Managing errors yourself

The trait also includes the helpers you'd use to manage the bag yourself:

- `addError(string $field, string $message)` adds a message, for checks that aren't validation rules.
- `resetValidation()` clears every message. `resetValidation('email')` or `resetValidation(['email', 'name'])`
  clears only those fields.
- `getErrorBag()` returns the underlying `MessageBag`.

```php
public function redeem(): void
{
    $this->validate();

    if (! Coupon::isValid($this->code)) {
        $this->addError('code', 'This code has expired.');

        return;
    }

    // ...
}
```

You can also throw a `ValidationException` yourself. When it's thrown from a handler that's covered (see
[Which handlers are covered](#which-handlers-are-covered)), its messages go into the bag just as if `validate()`
had failed:

```php
use Illuminate\Validation\ValidationException;

throw ValidationException::withMessages([
    'email' => 'That email is already registered.',
]);
```

## Showing errors

Views of a screen that uses the trait get the error bag as `$errors`, as they do in a Laravel request. This means
Blade's `@@error` directive works, with `$message` set to the field's first message:

@verbatim
```blade static
@error('email')
    <native:text class="text-theme-destructive">{{ $message }}</native:text>
@enderror
```
@endverbatim

You can also use `$errors` to drive an input's own error state. For example, the
[text inputs](../edge-components/text-input) take `is-error` and `supporting`:

@verbatim
```blade static
<native:outlined-text-input
    native:model.blur="email"
    label="Email"
    :is-error="$errors->has('email')"
    :supporting="$errors->first('email')"
/>
```
@endverbatim

The bag is also passed to partials the screen renders, so `@@error` works inside them as well.

## Which handlers are covered

<x-docs.version-badge since="4.9" />

Validation errors are kept in the bag, and the screen re-renders, when they're thrown from one of the screen's
handlers:

- An event handler in the view, such as `@press`, `@tap` or `@change`.
- A [`native:model`](data-binding) sync, including the `updated{Property}()` hook it runs.
- An `#[On]` or `#[OnNative]` [event listener](../the-basics/events), or a closure registered with `->on()`.
- A handler that a child component's event runs, either through `#[On]` or through an `@event` attribute on the
  child's tag.
- A `#[Poll]` method.
- `onBackPressed()`.

A `ValidationException` thrown anywhere else is not caught, and it reaches the error overlay like any other
exception. That includes `mount()`, `onResume()` and the other [lifecycle hooks](lifecycle-hooks). In those places,
call `validate()` inside a `try`/`catch` block, or check the data yourself and use `addError()`.

In 4.7 and 4.8, only the first two kinds were covered. Event listeners, `#[Poll]` methods and `onBackPressed()`
reached the error overlay.

### Child components

Each handler is covered by the component it belongs to. When a child calls `emit()` and the parent's listener fails
validation, the messages go into the **parent's** bag, and the child's handler carries on after `emit()`. If the
parent doesn't use `ValidatesInput`, the error overlay appears, as it would for any other handler of that parent.

<aside>

In 4.7 and 4.8, the parent's listener ran as part of the child's interaction, so its messages ended up in the
child's bag when the child used `ValidatesInput`. There, validate in the component that owns the data, and emit only
once it passes.

</aside>

## Testing

[Interactions in tests](../testing/interactions) go through the same handlers as on a device, so a failed validation
in a test leaves its messages in the bag and re-renders the screen, just as it does in the app. That includes
`emitNative()`, `firePoll()` and `pressBack()`. You can
assert on the error text in the rendered screen, or read `getErrorBag()` from the component.
