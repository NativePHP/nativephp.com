---
title: Refreshable
order: 315
---

## Overview

A standalone scrolling container with native pull-to-refresh. Wrap any content in it, point `@refresh` at a
component method, and the platform handles the gesture, spinner, and physics for you. On iOS it uses SwiftUI
`ScrollView { ... }.refreshable { }`; on Android, Compose `PullToRefreshBox` wrapping a `LazyColumn`. Both show
their native pull-to-refresh spinner, with system haptics on iOS.

@verbatim
```blade
<native:refreshable @refresh="loadLatest">
    @foreach($items as $item)
        <native:row class="p-4 gap-3 items-center">
            <native:text class="text-base">{{ $item->title }}</native:text>
        </native:row>
    @endforeach
</native:refreshable>
```
@endverbatim

## Events

- `@refresh` - Component method called when the user pulls down past the release threshold (optional, string)

## Children

Children are the scrollable content — the refreshable element **is** the scrolling container, so don't nest a
[`<native:scroll-view>`](scroll-view) (or another refreshable) inside, or you'll get nested scrolling.

<aside>

The refresh spinner stays visible for a short minimum window (around 800ms) after `@refresh` fires, so a fast PHP
handler doesn't make the pull feel skipped. Your handler runs during that window and the updated tree paints just as
the spinner hides — there's no separate "done" call to make.

</aside>

## Refreshable vs. List

[`<native:list>`](list) has pull-to-refresh built in via its `on-refresh` prop, alongside swipe actions,
end-reached, and Material3 rows. Reach for `<native:refreshable>` when you want pull-to-refresh around arbitrary
content — cards, a [`<native:column>`](column), a dashboard — rather than a list of rows.

## Examples

### Refreshable feed

@verbatim
```blade
<native:refreshable @refresh="refreshFeed">
    <native:column class="w-full gap-3 p-4">
        @foreach($posts as $post)
            <native:column class="w-full p-4 gap-2 rounded-lg bg-theme-surface">
                <native:text class="text-lg font-semibold">{{ $post->title }}</native:text>
                <native:text class="text-base text-theme-on-surface-variant">{{ $post->excerpt }}</native:text>
            </native:column>
        @endforeach
    </native:column>
</native:refreshable>
```
@endverbatim

The handler updates state and returns — the spinner dismisses on its own:

```php
public function refreshFeed(): void
{
    $this->posts = Post::latest()->get();
}
```

## Element

```php
use Native\Mobile\Edge\Elements\Refreshable;
use Native\Mobile\Edge\Elements\Row;
use Native\Mobile\Edge\Elements\Text;

Refreshable::make()
    ->onRefresh('loadLatest')
    ->addChild(Row::make(Text::make('First')))
    ->addChild(Row::make(Text::make('Second')));
```

- `make()` - Create a refreshable container
- `onRefresh(string $method)` - Component method called on pull-to-refresh
- `addChild(Element $child)` - Append a child to the scrollable content (inherited from the base `Element`)
