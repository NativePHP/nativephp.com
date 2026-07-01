<div class="not-prose my-6 flex items-start gap-3 rounded-2xl bg-yellow-50 px-5 py-4 text-yellow-900 ring-1 ring-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-100 dark:ring-yellow-800/40" role="note">
<x-icons.super-native class="mt-0.5 size-5 shrink-0 text-yellow-600 dark:text-yellow-400" />
<div class="text-sm leading-relaxed">
<p class="font-semibold">This is a Super Native feature &mdash; currently in beta</p>
<p class="mt-1 text-yellow-900/80 dark:text-yellow-100/80">{{ $slot->isEmpty() ? 'Super Native is still in beta, so its APIs and behaviour may change before a stable release.' : $slot }}</p>
</div>
</div>
