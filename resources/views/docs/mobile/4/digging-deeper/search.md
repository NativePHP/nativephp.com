---
title: Search
order: 80
---

## Overview

A screen can present a native search bar in its navigation chrome and feed it from PHP. There are two modes:
a **static** corpus the platform filters for you, and a **dynamic** handler you implement for server- or
database-backed results.

The search bar itself is shown by the [layout](../the-basics/layouts) chrome — via `NavBar::searchBar()` on a stack, or
`Tab::search()` on a tab. This page covers the component side: producing the results.

## Static search

Override `searchItems()` to return a fixed list the native search bar filters on-device as the user types — no
round-trip per keystroke. Return `null` to omit search entirely.

```php
public function searchItems(): ?array
{
    return Contact::pluck('name')->all();
}
```

Best for small, in-memory corpuses (a contact list, a settings index) where the whole set is cheap to hand over
once.

## Dynamic search

For results that come from a query — anything too large to preload, or that hits the database or network —
override `onSearchQuery()`. It receives the current query and returns the matching results; the native bar
debounces input so you're not queried on every character.

```php
public function onSearchQuery(string $query): array
{
    return Product::where('name', 'like', "%{$query}%")
        ->limit(20)
        ->pluck('name')
        ->all();
}
```

## Showing the search bar

Add the bar in the layout chrome and point it at the screen. On a stack layout's `NavBar`:

```php
NavBar::make()
    ->title('Contacts')
    ->searchBar(placeholder: 'Search contacts', debounceMs: 300);
```

Or as a dedicated search tab:

```php
Tab::search('Search', icon: 'magnifyingglass', placeholder: 'Find anything');
```

See [Layouts](../the-basics/layouts) for where these builders live and how a layout wraps a screen.

<aside>

Drive search in a test with `->search('query')` and read what your handler produced with `->searchResults()`, no
device required. See [Interactions](../testing/interactions).

</aside>
