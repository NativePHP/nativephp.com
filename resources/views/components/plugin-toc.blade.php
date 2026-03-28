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
    class="mb-6"
>
    <h3 class="flex items-center gap-1.5 text-sm opacity-60">
        <x-icons.stacked-lines class="size-[18px]" />
        <div>On this page</div>
    </h3>

    <div class="mt-4 flex flex-col space-y-2 overflow-y-auto overflow-x-hidden border-l text-xs dark:border-l-white/15">
        <template x-for="heading in headings" :key="heading.id">
            <a
                :href="'#' + heading.id"
                :class="heading.level === 2 ? 'pb-1 pl-3' : 'py-1 pl-6'"
                class="transition duration-300 ease-in-out will-change-transform hover:translate-x-0.5 hover:text-violet-400 hover:opacity-100 dark:text-white/80"
                x-text="heading.text"
            ></a>
        </template>
    </div>
</div>
