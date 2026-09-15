---
title: Pager
order: 312
---

## Overview

A full-screen snap pager: the TikTok / Reels / Shorts feed. Each page is sized to the pager's own frame, and a swipe
settles on exactly one page. Paging is vertical by default; pass `horizontal` for a stories-style strip.

`<native:pager>` is a paired tag and its children are the pages. For a fixed set of pages that is all you need:

@verbatim
```blade
<native:pager class="w-full h-full">
    @foreach ($slides as $slide)
        <native:column :native:key="'slide-'.$slide->id" class="w-full h-full items-center justify-center bg-black">
            <native:text class="text-2xl font-bold text-white">{{ $slide->title }}</native:text>
        </native:column>
    @endforeach
</native:pager>
```
@endverbatim

A feed is different: it has no total and you do not want every page in the tree. Ship a window of pages and tell the
pager where that window sits. See [Windowed feeds](#windowed-feeds).

## Props

- `count` - Items loaded so far, which is how many logical pages native lays out (optional, int, default: the
  number of inline children). This is not a total; a feed has none. Grow `count` as batches arrive and the pager
  grows in place.
- `page` - Page the pager should show (optional, int, default: `0`). See [Pager position](#pager-position).
- `from` - Absolute index of the first page emitted as a child (optional, int, default: `0`).
- `to` - Absolute index of the last page emitted, inclusive (optional, int). Native lays the children out from `from`
  upwards, so `from` is the value that has to agree with your loop; `to` travels with it to keep the two readable
  together.
- `has-more` - Append a loading page after the last loaded item (optional, boolean, default: `false`). The user can
  pull into it while the next batch is fetched, instead of hitting a wall at the end of the window. Settling on it
  reports `index === count`.
- `placeholders` - One image URL per loaded page index, `''` for no image (optional, array). Shown for loaded pages
  this render has not shipped. See [Placeholders](#placeholders).
- `horizontal` - Page sideways instead of vertically (optional, boolean, default: `false`).
- `on-page-change` - Component method called with `(int $index)` as the current page changes.
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional) [Android]

## Every page needs a `native:key`

The root element of every page must carry `native:key`, set to an id that does not depend on the page's slot in the
shipped window:

@verbatim
```blade
<native:stack :native:key="'pager-'.$index" class="w-full h-full bg-black">
```
@endverbatim

The window slides by one on every swipe. Without a key, each page picks up a new positional node id on every render,
so native rebuilds the page's whole subtree as soon as the window shifts. A video on that page then restarts from
zero, one round trip after the swipe settled. See [Subtree Reuse](../architecture/subtree-reuse#helping-the-diff-keys)
for how keys give a subtree its identity.

<aside>

A bare `key` attribute is not the same thing and is silently ignored. It has to be `native:key` (or
`:native:key` for a bound expression).

</aside>

## Pager position

Native owns the scroll position and keeps it across re-renders, so a render that lands mid-swipe never drags the
pager back. The `page` prop only moves the pager when PHP sends an index native never reported, which is how you jump
programmatically; an echo of native's own report is ignored.

`on-page-change` fires as soon as a page becomes the nearest one during a swipe, not when the scroll comes to rest,
so PHP's round trip overlaps the swipe animation. The handler takes a single int, the absolute page index:

```php
public function onPagerPage(int $index): void
{
    $this->setPagerPage($index);
}
```

## Placeholders

`placeholders` is an image URL per loaded page index, covering every loaded item and not just the shipped window. A
page PHP has not shipped yet draws its image instead of nothing, so scrolling faster than the round trip lands on a
still rather than a blank. Twenty URLs is a trivial payload, and the images are fetched and cached natively.

Pages past `count` (the `has-more` tail) show a loading indicator instead of a placeholder. A loaded page with no
placeholder URL is transparent, so the pager's own `bg-*` shows through.

## Windowed feeds

The `HasPagerWindow` trait holds the windowing state and leaves you the fetch. Native reports each page change, your
handler records it, and the next render emits the pages around it.

```php
use App\Services\Feed;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\UI\Concerns\HasPagerWindow;

class ReelFeed extends NativeComponent
{
    use HasPagerWindow;

    /** Clips loaded so far, in feed order. */
    public array $items = [];

    /** Opaque cursor from your API. */
    public ?string $cursor = null;

    public function mount(): void
    {
        $this->loadMore();
    }

    public function onPagerPage(int $index): void
    {
        $this->setPagerPage($index);

        if ($this->pagerNeedsMore()) {
            $this->loadMore();
        }
    }

    private function loadMore(): void
    {
        $batch = Feed::clips(cursor: $this->cursor);

        $this->items = [...$this->items, ...$batch->clips];
        $this->cursor = $batch->nextCursor;
        $this->extendPager(count($batch->clips), hasMore: $batch->nextCursor !== null);
    }

    public function render(): View
    {
        return view('native.pager-feed', [
            'items' => $this->items,
            'placeholders' => array_map(fn ($clip) => $clip['poster'] ?? '', $this->items),
            'loaded' => $this->pagerLoaded,
            'hasMore' => $this->pagerHasMore,
            'page' => $this->pagerPage,
            'from' => $this->pagerWindowFrom(),
            'to' => $this->pagerWindowTo(),
        ]);
    }
}
```

The view emits only the window, so the loop runs over indexes rather than the collection:

@verbatim
```blade
<native:pager
    class="w-full h-full"
    :count="$loaded"
    :page="$page"
    :from="$from"
    :to="$to"
    :has-more="$hasMore"
    :placeholders="$placeholders"
    on-page-change="onPagerPage"
    a11y-label="Video feed"
>
    @for ($index = $from; $index <= $to; $index++)
        @include('native.pager-feed-page', ['index' => $index])
    @endfor
</native:pager>
```
@endverbatim

And the page itself, keyed by its absolute index:

@verbatim
```blade static
{{-- resources/views/native/pager-feed-page.blade.php --}}
@php $clip = $items[$index]; @endphp

<native:stack :native:key="'pager-'.$index" class="w-full h-full bg-black">
    <native:video-player
        src="{{ $clip['src'] }}"
        poster="{{ $clip['poster'] ?? '' }}"
        class="w-full h-full object-cover"
        :controls="false"
        :autoplay="true"
        :loop="true"
    />

    <native:column class="absolute bottom-[40] left-4 right-20 gap-1">
        <native:text class="text-[15] font-bold text-white">{{ $clip['handle'] }}</native:text>
        <native:text class="text-[14] text-white">{{ $clip['caption'] }}</native:text>
    </native:column>
</native:stack>
```
@endverbatim

### `HasPagerWindow` state

- `$pagerPage` - Absolute index of the page currently on screen (int, default: `0`)
- `$pagerWindow` - Pages shipped either side of the current one (int, default: `2`). Two keeps a fast second swipe on
  device: the page after next is already there while the round trip for the settle is still in flight. Each shipped
  page is rendered Blade plus a buffering video, so keep it small.
- `$pagerLoaded` - Items fetched so far, which is the pager's `count` (int, default: `0`)
- `$pagerHasMore` - Whether another batch can be fetched, which drives the trailing loading page (bool, default:
  `true`)

### `HasPagerWindow` methods

- `setPagerPage(int $index)` - Record the page native reported
- `extendPager(int $added, bool $hasMore = true)` - Record a fetched batch: grows the pager and updates the tail state
- `pagerNeedsMore(int $threshold = 3)` - True when the current page is within `$threshold` items of the end of what is
  loaded, or on the loading page itself, and more can be fetched. Three is the usual feed default: the PHP round trip
  plus the API call has to land before the user swipes there.
- `pagerWindowFrom()` - First page index to emit
- `pagerWindowTo()` - Last page index to emit, inclusive, clamped to what is loaded

## Pairing with video

A [`<native:video-player>`](../plugins/core/media-player) on a pager page needs no coordination with the pager. With
`autoplay` the surface plays while it is at least 45% on screen and pauses below that, so the page you are watching
plays and its shipped neighbours sit loaded and silent. Nothing in the pager renderer knows about playback.

Give each page `:controls="false"` for a bare surface you can overlay your own EDGE elements on, and a `poster` so
the page shows a still the instant it exists rather than black. The playing surface is the one the `MediaPlayer`
facade drives, so `MediaPlayer::pause()` from a tap handler pauses the page on screen without passing a page id.

## Element

```php
use Native\Mobile\UI\Elements\Pager;

Pager::make($page1, $page2, $page3)
    ->count($loaded)
    ->page($current)
    ->hasMore()
    ->placeholders($posters)
    ->onPageChange('onPagerPage');
```

- `make(Element ...$children)` - Create a pager whose children are the pages
- `count(int $count)` - Items loaded so far
- `page(int $index)` - Page the pager should show
- `horizontal(bool $value = true)` - Page sideways
- `hasMore(bool $value = true)` - Append the trailing loading page
- `placeholders(array $urls)` - One image URL per loaded page index
- `onPageChange(string $method)` - Page-change handler
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint

<aside>

One pager per screen for now. The page index rides the tab-change transport, so a single screen's feed is capped at
32,767 pages.

</aside>
