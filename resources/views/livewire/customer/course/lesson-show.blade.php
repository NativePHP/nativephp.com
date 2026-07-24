<div class="flex flex-col gap-8 lg:flex-row lg:items-start">
    {{-- Main column --}}
    <div class="min-w-0 flex-1">
        {{-- Video area --}}
        <div class="w-full rounded-lg overflow-hidden bg-black aspect-video flex items-center justify-center relative">
            @if ($lesson->vimeo_id)
                <div
                    wire:ignore
                    x-data="courseVideo({ videoId: '{{ $lesson->vimeo_id }}', skip: @js($skipIntroOutro), introSeconds: {{ $introSkipSeconds }}, outroSeconds: {{ $outroSkipSeconds }} })"
                    class="absolute inset-0 [&_iframe]:absolute [&_iframe]:inset-0 [&_iframe]:h-full [&_iframe]:w-full"
                ></div>
            @else
                <div class="text-center">
                    <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-white/5 border border-white/10 mb-4">
                        <svg class="size-8 text-zinc-400 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                    </div>
                    <p class="text-sm text-zinc-500">Video coming soon</p>
                </div>
            @endif
        </div>

        {{-- Lesson info --}}
        <div class="mt-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        @if (! $lesson->is_published)
                            <flux:badge variant="pill" color="amber" size="sm">Coming Soon</flux:badge>
                        @endif
                        <flux:text class="text-xs">{{ $lesson->module->title }}</flux:text>
                        @if (! $lesson->module->is_published)
                            <flux:badge variant="pill" color="amber" size="sm">Draft</flux:badge>
                        @endif
                    </div>
                    <flux:heading size="lg">{{ $lesson->title }}</flux:heading>
                    @if ($lesson->description)
                        <flux:text class="mt-2 max-w-2xl">{{ $lesson->description }}</flux:text>
                    @endif
                </div>

                <button
                    wire:click="toggleComplete"
                    class="shrink-0 flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-all {{ $this->isComplete ? 'bg-emerald-100 text-emerald-700 border border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-700 dark:hover:bg-emerald-900/50' : 'bg-zinc-100 text-zinc-600 border border-zinc-200 hover:bg-zinc-200 dark:bg-white/5 dark:text-zinc-400 dark:border-white/10 dark:hover:bg-white/10' }}"
                >
                    @if ($this->isComplete)
                        <svg class="size-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Completed
                    @else
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                        Mark Complete
                    @endif
                </button>
            </div>

            {{-- Prev / Next nav --}}
            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-zinc-200 dark:border-white/10">
                @if ($this->previousLesson)
                    <a href="{{ route('customer.course.lesson', $this->previousLesson) }}" wire:navigate class="flex items-center gap-2 rounded-lg bg-zinc-100 border border-zinc-200 px-4 py-2 text-sm text-zinc-600 hover:bg-zinc-200 transition-colors dark:bg-white/5 dark:border-white/10 dark:text-zinc-400 dark:hover:bg-white/10">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                        Previous
                    </a>
                @endif
                @if ($this->nextLesson)
                    <a href="{{ route('customer.course.lesson', $this->nextLesson) }}" wire:navigate class="ml-auto flex items-center gap-2 rounded-lg bg-zinc-100 border border-zinc-200 px-4 py-2 text-sm text-zinc-600 hover:bg-zinc-200 transition-colors dark:bg-white/5 dark:border-white/10 dark:text-zinc-400 dark:hover:bg-white/10">
                        Next
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif
            </div>

            {{-- Lesson notes --}}
            @if ($lesson->notes)
                <div class="prose prose-sm mt-8 max-w-2xl dark:prose-invert">
                    {!! App\Support\CommonMark\CommonMark::convertToHtml($lesson->notes) !!}
                </div>
            @endif
        </div>
    </div>

    {{-- Course outline --}}
    <aside class="w-full shrink-0 lg:sticky lg:top-6 lg:w-64">
        <div class="rounded-lg border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5">
            <div class="p-4 border-b border-zinc-200 dark:border-white/10">
                <flux:heading size="sm">Course outline</flux:heading>
            </div>
            <div class="max-h-[calc(100dvh-9rem)] overflow-y-auto py-2">
                @foreach ($this->course->modules as $outlineModule)
                    <div wire:key="outline-module-{{ $outlineModule->id }}" class="{{ ! $loop->first ? 'mt-1 border-t border-zinc-100 pt-1 dark:border-white/5' : '' }}">
                        <div class="flex items-center gap-2 px-4 pt-2 pb-1">
                            <flux:text class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $outlineModule->title }}</flux:text>
                            @if (! $outlineModule->is_published)
                                <flux:badge variant="pill" color="amber" size="sm">Draft</flux:badge>
                            @endif
                        </div>
                        @foreach ($outlineModule->lessons as $outlineLesson)
                            @php
                                $isCurrent = $outlineLesson->id === $lesson->id;
                                $isDraft = ! $outlineLesson->is_published;
                                $isLessonComplete = in_array($outlineLesson->id, $this->completedLessonIds);
                                $canAccess = $outlineLesson->is_free || $this->hasPurchased || $this->isAdmin;
                                $isClickable = $this->isAdmin || (! $isDraft && $canAccess);
                                $isLocked = ! $isClickable && ! $isDraft;
                            @endphp
                            @if ($isClickable)
                                <a
                                    href="{{ route('customer.course.lesson', $outlineLesson) }}"
                                    wire:navigate
                                    wire:key="outline-lesson-{{ $outlineLesson->id }}"
                                    @if ($isCurrent) aria-current="page" @endif
                                    class="flex items-center gap-3 px-4 py-2 text-sm transition-colors {{ $isCurrent ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-400 dark:hover:bg-white/5' }}"
                                >
                                    <span class="min-w-0 flex-1 truncate {{ $isCurrent ? 'font-medium' : '' }} {{ $isLessonComplete ? 'line-through' : '' }}" title="{{ $outlineLesson->title }}">{{ $outlineLesson->title }}</span>
                                    @if ($isDraft)
                                        <flux:badge variant="pill" color="amber" size="sm">Coming Soon</flux:badge>
                                    @endif
                                    @if ($outlineLesson->duration_in_seconds)
                                        <span class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ gmdate('i:s', $outlineLesson->duration_in_seconds) }}</span>
                                    @endif
                                </a>
                            @else
                                <div wire:key="outline-lesson-{{ $outlineLesson->id }}" class="flex items-center gap-3 px-4 py-2 text-sm text-zinc-400 dark:text-zinc-600">
                                    @if ($isLocked)
                                        <x-heroicon-m-lock-closed class="size-4 shrink-0" />
                                    @endif
                                    <span class="min-w-0 flex-1 truncate {{ $isLessonComplete ? 'line-through' : '' }}" title="{{ $outlineLesson->title }}">{{ $outlineLesson->title }}</span>
                                    @if ($isDraft)
                                        <flux:badge variant="pill" color="amber" size="sm">Coming Soon</flux:badge>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
</div>
