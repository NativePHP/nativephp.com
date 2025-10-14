---
title: Native Functions
order: 100
---

Our custom PHP extension enables tight integration with each platform, providing a consistent and performant abstraction
that lets you focus on building your app. Build for both platforms while you develop on one.

Native device functions are called directly from your PHP code, giving you access to platform-specific features while
maintaining the productivity and familiarity of Laravel development.

These functions are called from your PHP code using an ever-growing list of classes. These classes are also wrapped in
Laravel Facades for ease of access and testing:

- `Native\Mobile\Facades\Biometrics`
- `Native\Mobile\Facades\Camera`
- `Native\Mobile\Facades\Dialog`
- `Native\Mobile\Facades\Geolocation`
- `Native\Mobile\Facades\Haptics`
- `Native\Mobile\Facades\PushNotifications`
- `Native\Mobile\Facades\SecureStorage`
- `Native\Mobile\Facades\System`

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Found a bug?

Community support is available to all at no cost via [Discord]({{ $discordLink }}). Higher priority support directly from the
NativePHP team is available to **Max** and **Ultra** license holders.

</aside>
