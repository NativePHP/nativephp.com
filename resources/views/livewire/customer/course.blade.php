<div>
    <div class="mb-6">
        <flux:heading size="xl">Course</flux:heading>
        <flux:text>The NativePHP Masterclass</flux:text>
    </div>

    @if ($this->course)
        {{-- Progress --}}
        @if ($this->totalLessons > 0)
            <div class="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-center justify-between mb-2">
                    <flux:text class="font-medium">Your Progress</flux:text>
                    <flux:text class="text-sm">{{ $this->completedCount }} / {{ $this->totalLessons }} lessons</flux:text>
                </div>
                <div class="h-2 w-full rounded-full bg-zinc-200 dark:bg-white/10">
                    <div
                        class="h-2 rounded-full bg-emerald-500 transition-all"
                        style="width: {{ $this->totalLessons > 0 ? round(($this->completedCount / $this->totalLessons) * 100) : 0 }}%"
                    ></div>
                </div>
            </div>
        @endif

        {{-- Pro Access Status --}}
        @if ($this->hasPurchased)
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>You have Pro access. All modules and lessons are unlocked.</flux:callout.text>
            </flux:callout>
        @else
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-6 dark:border-emerald-700/50 dark:bg-emerald-900/20">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 text-emerald-600 dark:text-emerald-400">
                        <x-heroicon-s-lock-closed class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium text-emerald-900 dark:text-emerald-100">
                            Unlock all modules with Pro
                        </h3>
                        <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300">
                            Free modules are available now. Upgrade to Pro for full access to all current and future content.
                        </p>
                        <form action="{{ route('course.checkout') }}" method="POST" class="mt-3">
                            @csrf
                            <flux:button type="submit" variant="primary" size="sm">Get Pro Access</flux:button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modules --}}
        <div class="space-y-4">
            @foreach ($this->course->modules as $module)
                <div wire:key="module-{{ $module->id }}" class="rounded-lg border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-4 p-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $module->is_free ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-500' }}">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <flux:heading size="sm">{{ $module->title }}</flux:heading>
                                @if ($module->is_free)
                                    <flux:badge variant="pill" color="emerald" size="sm">Free</flux:badge>
                                @else
                                    <flux:badge variant="pill" color="violet" size="sm">Pro</flux:badge>
                                @endif
                            </div>
                            @if ($module->description)
                                <flux:text class="text-sm mt-1">{{ $module->description }}</flux:text>
                            @endif
                        </div>
                    </div>

                    @if ($module->lessons->isNotEmpty())
                        <div class="border-t border-zinc-200 dark:border-white/10">
                            @foreach ($module->lessons as $lesson)
                                @php
                                    $isCompleted = in_array($lesson->id, $this->completedLessonIds);
                                    $canAccess = $lesson->is_free || $this->hasPurchased;
                                @endphp
                                <div wire:key="lesson-{{ $lesson->id }}" class="flex items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-white/5' : '' }}">
                                    <div class="flex size-6 shrink-0 items-center justify-center rounded-full border {{ $isCompleted ? 'bg-emerald-100 border-emerald-300 dark:bg-emerald-900/50 dark:border-emerald-600' : 'border-zinc-300 dark:border-white/10' }}">
                                        @if ($isCompleted)
                                            <svg class="size-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        @if ($canAccess)
                                            <a href="{{ route('customer.course.lesson', $lesson) }}" wire:navigate class="text-sm font-medium hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                                {{ $lesson->title }}
                                            </a>
                                        @else
                                            <span class="text-sm text-zinc-400 dark:text-zinc-500">
                                                <x-heroicon-m-lock-closed class="inline size-3.5 mr-1" />
                                                {{ $lesson->title }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($lesson->duration_in_seconds)
                                        <flux:text class="text-xs shrink-0">{{ gmdate('i:s', $lesson->duration_in_seconds) }}</flux:text>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <flux:callout variant="secondary" icon="information-circle">
            <flux:callout.heading>Course coming soon</flux:callout.heading>
            <flux:callout.text>We're putting the finishing touches on the course content. You'll be notified when modules and lessons are ready.</flux:callout.text>
        </flux:callout>
    @endif
</div>
