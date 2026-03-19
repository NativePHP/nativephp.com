---
title: Release Notes
order: 1100
---

## NativePHP/desktop
@forelse (\App\Support\GitHub::desktop()->releases()->take(10) as $release)
### {{ $release->name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
## We couldn't show you the latest release notes at this time.
Not to worry, you can head over to GitHub to see the [latest release notes](https://github.com/NativePHP/electron/releases).
@endforelse

## NativePHP/php-bin
@forelse (\App\Support\GitHub::phpBin()->releases()->take(10) as $release)
### {{ $release->name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
## We couldn't show you the latest release notes at this time.
Not to worry, you can head over to GitHub to see the [latest release notes](https://github.com/NativePHP/electron/releases).
@endforelse

## NativePHP/electron (v1)
@forelse (\App\Support\GitHub::electron()->releases()->take(10) as $release)
### {{ $release->name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
## We couldn't show you the latest release notes at this time.
Not to worry, you can head over to GitHub to see the [latest release notes](https://github.com/NativePHP/electron/releases).
@endforelse

## NativePHP/laravel (v1)
@forelse (\App\Support\GitHub::laravel()->releases()->take(10) as $release)
### {{ $release->name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
## We couldn't show you the latest release notes at this time.
Not to worry, you can head over to GitHub to see the [latest release notes](https://github.com/NativePHP/electron/releases).
@endforelse

