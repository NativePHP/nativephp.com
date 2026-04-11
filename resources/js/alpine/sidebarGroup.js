
export default (key) => ({
    init() {
        const storageKey = `sidebar_${key}`
        const saved = localStorage.getItem(storageKey)
        const hasCurrent = !!this.$el.querySelector('[data-current]')
        let skipNext = false

        if ((hasCurrent || saved === 'open') && !this.$el.hasAttribute('data-open')) {
            if (hasCurrent) {
                skipNext = true
            }
            this.$el.querySelector('button')?.click()
        }

        this._observer = new MutationObserver(() => {
            if (skipNext) {
                skipNext = false
                return
            }
            localStorage.setItem(storageKey, this.$el.hasAttribute('data-open') ? 'open' : 'closed')
        })
        this._observer.observe(this.$el, { attributes: true, attributeFilter: ['data-open'] })
    },
    destroy() {
        this._observer?.disconnect()
    },
})
