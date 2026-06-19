<div class="not-prose my-6 flex items-start gap-3 rounded-2xl border-l-4 border-snow-flurry-300 bg-gradient-to-tl from-transparent to-snow-flurry-50/70 px-5 py-4 text-mirage ring-1 ring-black/5 dark:border-snow-flurry-200 dark:from-slate-900/30 dark:to-snow-flurry-300/10 dark:text-snow-flurry-50" role="note">
<x-icons.super-native class="mt-0.5 size-5 shrink-0 text-snow-flurry-300 dark:text-snow-flurry-200" />
<div class="text-sm leading-relaxed">
<p class="font-semibold">This is a Super Native feature &mdash; currently in beta</p>
<p class="mt-1 text-mirage/80 dark:text-snow-flurry-50/80">{{ $slot->isEmpty() ? 'Super Native is still in beta, so its APIs and behaviour may change before a stable release.' : $slot }}</p>
</div>
</div>
