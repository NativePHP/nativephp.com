@props([
    'title' => null,
])
<div {{ $attributes->merge(['class' => 'snippet not-prose my-6']) }} x-data="{
    activeTab: null,
    tabs: [],
    init() {
        const container = this.$el.querySelector('.snippet-content');
        if (container) {
            this.tabs = Array.from(container.querySelectorAll('[data-snippet-tab]'));
            if (this.tabs.length > 0) {
                this.activeTab = this.tabs[0].dataset.snippetTab;
            }
        }
    },
    setActiveTab(tab) {
        this.activeTab = tab;
    },
    isActive(tab) {
        return this.activeTab === tab;
    }
}">
<div x-show="tabs.length > 1" x-cloak class="flex gap-1 rounded-t-xl bg-slate-800 px-4 pt-3">
<template x-for="tab in tabs" :key="tab.dataset.snippetTab">
<button type="button" @click="setActiveTab(tab.dataset.snippetTab)" :class="{'bg-slate-700 text-white': isActive(tab.dataset.snippetTab), 'text-slate-400 hover:text-slate-300 hover:bg-slate-700/50': !isActive(tab.dataset.snippetTab)}" class="rounded-t-lg px-4 py-2 text-sm font-medium transition-colors" x-text="tab.dataset.snippetTab"></button>
</template>
</div>
@if ($title)<div x-show="tabs.length <= 1" x-cloak class="rounded-t-xl bg-slate-800 px-4 py-2 text-sm font-medium text-slate-400">{{ $title }}</div>@endif
<div class="snippet-content overflow-hidden bg-slate-900 @if(!$title) rounded-t-xl @endif rounded-b-xl">
{{ $slot }}
</div>
</div>
