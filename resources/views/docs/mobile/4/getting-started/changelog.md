---
title: Changelog
order: 2
---

For changes prior to v4, see the [v3 documentation](/docs/mobile/3/getting-started/changelog).

@forelse (\App\Support\GitHub::mobileAir()->releasesFrom('4.0.0') as $release)
## {{ $release->name ?: $release->tag_name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
Release notes for v4 will appear here as releases are published. In the
meantime, start with the [SuperNative introduction](../architecture/super-native).
@endforelse
