<div>
    @if ($this->hasPurchased)
        {{-- Purchased: Show course content --}}
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

            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>You have Pro access. All modules and lessons are unlocked.</flux:callout.text>
            </flux:callout>

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
                                    @endphp
                                    <div wire:key="lesson-{{ $lesson->id }}" class="flex items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-white/5' : '' }}">
                                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full border {{ $isCompleted ? 'bg-emerald-100 border-emerald-300 dark:bg-emerald-900/50 dark:border-emerald-600' : 'border-zinc-300 dark:border-white/10' }}">
                                            @if ($isCompleted)
                                                <svg class="size-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('customer.course.lesson', $lesson) }}" wire:navigate class="text-sm font-medium hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                                {{ $lesson->title }}
                                            </a>
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
    @else
        {{-- Not purchased: Show marketing/purchase page --}}
        <div class="mx-auto max-w-2xl">
            {{-- Hero --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-8 text-center dark:border-white/10 dark:bg-white/5 sm:p-12">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-sm font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
                    </span>
                    Early Bird Pricing
                </div>

                <h1 class="mt-6 text-3xl font-bold sm:text-4xl">
                    <span class="text-zinc-900 dark:text-white">The</span>
                    <span class="bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent">NativePHP</span>
                    <br />
                    <span class="text-zinc-900 dark:text-white">Masterclass</span>
                </h1>

                <p class="mx-auto mt-4 max-w-md text-zinc-600 dark:text-zinc-400">
                    Go from zero to published app. Learn to build native mobile and desktop applications using the PHP and Laravel skills you already have.
                </p>

                {{-- Price --}}
                <div class="mt-8 flex items-baseline justify-center gap-2">
                    <span class="text-5xl font-bold text-zinc-900 dark:text-white">$101</span>
                    <span class="text-xl text-zinc-400 line-through">$299</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">one-time</span>
                </div>

                {{-- CTA --}}
                <div class="mt-8">
                    <form action="{{ route('course.checkout') }}" method="POST">
                        @csrf
                        <flux:button type="submit" variant="primary" class="!bg-emerald-600 hover:!bg-emerald-700 !px-8 !py-3 !text-base">
                            Get Early Bird Access
                        </flux:button>
                    </form>
                </div>
            </div>

            {{-- What's Included --}}
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                        <x-heroicon-s-device-phone-mobile class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white">Mobile & Desktop</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Build iOS, Android, macOS, Windows, and Linux apps from one codebase.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                        <x-heroicon-s-code-bracket class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white">Use Your PHP Skills</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">No need to learn Swift, Kotlin, or Dart. Build native apps with Laravel.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                        <x-heroicon-s-rocket-launch class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white">Zero to Published</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">From setup to the App Store and Google Play. The complete journey.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                        <x-heroicon-s-arrow-path class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h3 class="mt-3 font-semibold text-zinc-900 dark:text-white">Lifetime Access</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">One-time payment. All current and future content included.</p>
                </div>
            </div>

            {{-- Link to full course page --}}
            <div class="mt-8 text-center">
                <a href="{{ route('course') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                    View full course details &rarr;
                </a>
            </div>
        </div>
    @endif
</div>
