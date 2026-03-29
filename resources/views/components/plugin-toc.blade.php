<div
    x-data="{
        headings: [],
        init() {
            const article = document.querySelector('article')
            if (! article) return

            const elements = article.querySelectorAll('h2[id], h3[id]')
            this.headings = Array.from(elements).map(el => ({
                id: el.id,
                text: el.textContent.replace(/^#\s*/, '').trim(),
                level: parseInt(el.tagName.substring(1)),
            }))
        },
    }"
    x-show="headings.length > 0"
    x-cloak
>
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="filled" size="sm" class="!rounded-full">
            <x-icons.stacked-lines class="size-4" />
            On this page
        </flux:button>

        <flux:popover class="w-64">
            <nav class="flex max-h-80 flex-col gap-0.5 overflow-y-auto">
                <template x-for="heading in headings" :key="heading.id">
                    <a
                        :href="'#' + heading.id"
                        :class="heading.level === 2 ? 'pl-2' : 'pl-5'"
                        class="rounded-md px-2 py-1.5 text-xs transition hover:bg-zinc-100 dark:text-white/80 dark:hover:bg-zinc-700"
                        x-text="heading.text"
                    ></a>
                </template>
            </nav>
        </flux:popover>
    </flux:dropdown>
</div>
