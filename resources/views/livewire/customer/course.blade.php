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

            <div class="mb-6 rounded-lg border border-violet-200 bg-violet-50 p-6 dark:border-violet-700/50 dark:bg-violet-900/20">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 text-violet-600 dark:text-violet-400">
                        <x-heroicon-s-sparkles class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-medium text-violet-900 dark:text-violet-100">
                            You're in! Course content is coming soon.
                        </h3>
                        <p class="mt-1 text-sm text-violet-700 dark:text-violet-300">
                            We're recording the lessons now. You'll have full access to all modules and lessons as soon as they're published.
                        </p>
                    </div>
                </div>
            </div>

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
        {{-- Not purchased: Full-width marketing/purchase page --}}
        <div class="-mx-6 -mt-6 sm:-mx-8 sm:-mt-8">
            {{-- Hero --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-violet-50 to-white px-6 py-16 sm:px-12 sm:py-20 dark:from-zinc-900 dark:to-zinc-950">
                {{-- Background glow --}}
                <div class="pointer-events-none absolute -top-24 left-1/2 size-[500px] -translate-x-1/2 rounded-full bg-violet-500/5 blur-[120px] dark:bg-violet-500/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-32 -right-32 size-[400px] rounded-full bg-indigo-500/5 blur-[100px] dark:bg-indigo-500/10" aria-hidden="true"></div>

                <div class="relative z-10 mx-auto max-w-2xl text-center">
                    <span class="inline-flex items-center gap-2 rounded-md bg-violet-500/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-violet-600 ring-1 ring-violet-500/20 dark:text-violet-400">
                        New Course &mdash; Early Bird
                    </span>

                    <h1 class="mt-8 text-4xl font-black tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl dark:text-white">
                        Build native apps
                        <span class="bg-gradient-to-r from-violet-600 via-purple-500 to-indigo-600 bg-clip-text text-transparent dark:from-violet-400 dark:via-purple-300 dark:to-indigo-400">the Laravel way.</span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
                        This course takes you from zero to a published app on the App Store &mdash; using the PHP and Laravel skills you already have.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-sm text-zinc-500 dark:text-zinc-500">
                        <span class="flex items-center gap-2">
                            <svg class="size-4 text-emerald-500 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.06l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            Free modules included
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="size-4 text-emerald-500 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.06l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            HD video lessons
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="size-4 text-emerald-500 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.06l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            Lifetime access
                        </span>
                    </div>

                    {{-- Price --}}
                    <div class="mt-10 flex items-baseline justify-center gap-3">
                        <span class="text-5xl font-black text-zinc-900 dark:text-white">$101</span>
                        <span class="text-xl text-zinc-400 line-through dark:text-zinc-600">$299</span>
                        <span class="text-sm text-zinc-500">one-time</span>
                    </div>

                    {{-- CTA --}}
                    <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                        <form action="{{ route('course.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-b from-violet-500 to-violet-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 ring-1 ring-violet-400/20 transition hover:shadow-xl hover:shadow-violet-500/30">
                                Get Early Bird Access
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                            </button>
                        </form>
                        <a href="{{ route('course') }}" class="text-sm font-medium text-zinc-500 transition hover:text-zinc-900 dark:hover:text-white">
                            View full details &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- What's Included --}}
            <div class="mt-8 grid gap-4 px-1 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-500/15">
                        <x-heroicon-s-device-phone-mobile class="size-4 text-violet-600 dark:text-violet-400" />
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">Mobile & Desktop</h3>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">iOS, Android, macOS, Windows, and Linux from one codebase.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-500/15">
                        <x-heroicon-s-code-bracket class="size-4 text-violet-600 dark:text-violet-400" />
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">Use Your PHP Skills</h3>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">No Swift, Kotlin, or Dart. Build native apps with Laravel.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-500/15">
                        <x-heroicon-s-rocket-launch class="size-4 text-violet-600 dark:text-violet-400" />
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">Zero to Published</h3>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">From setup to the App Store and Google Play.</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-500/15">
                        <x-heroicon-s-arrow-path class="size-4 text-violet-600 dark:text-violet-400" />
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">Lifetime Access</h3>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">One-time payment. All current and future content.</p>
                </div>
            </div>
        </div>
    @endif
</div>
