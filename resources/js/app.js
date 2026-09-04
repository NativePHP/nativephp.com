import './fonts'
import './bootstrap'
import 'number-flow'
import { gsap } from 'gsap'
import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm.js'
import codeBlock from './alpine/codeBlock.js'
import copyMarkdown from './alpine/copyMarkdown.js'
import courseVideo from './alpine/courseVideo.js'
import sidebarGroup from './alpine/sidebarGroup.js'
import docsearch from '@docsearch/js'
import Atropos from 'atropos'
import '@docsearch/css'
import 'atropos/css'

import.meta.glob(['../images/**', '../svg/**'])
import {
    animate,
    hover,
    inView,
    easeIn,
    easeOut,
    easeInOut,
    backIn,
    backOut,
    backInOut,
    circIn,
    circOut,
    circInOut,
    anticipate,
    spring,
    stagger,
    cubicBezier,
} from 'motion'

// Motion
window.motion = {
    animate: animate,
    hover: hover,
    inView: inView,
    easeIn: easeIn,
    easeOut: easeOut,
    easeInOut: easeInOut,
    backOut: backOut,
    backIn: backIn,
    backInOut: backInOut,
    circIn: circIn,
    circOut: circOut,
    circInOut: circInOut,
    anticipate: anticipate,
    spring: spring,
    stagger: stagger,
    cubicBezier: cubicBezier,
}

// Atropos
window.Atropos = Atropos

// GSAP
window.gsap = gsap

// Alpine
Alpine.data('codeBlock', codeBlock)
Alpine.data('copyMarkdown', copyMarkdown)
Alpine.data('courseVideo', courseVideo)
Alpine.data('sidebarGroup', sidebarGroup)
Alpine.magic('refAll', (el) => {
    return (refName) => {
        return Array.from(el.querySelectorAll(`[x-ref="${refName}"]`))
    }
})
Alpine.data('countdown', (iso) => ({
    flows: {},
    init() {
        // Parse target date from ISO string and ensure it's treated as a specific point in time
        this.targetDate = new Date(iso).getTime()

        // refs to the number-flow elements
        this.flows = {
            dd: this.$refs.dd, // days
            hh: this.$refs.hh, // hours
            mm: this.$refs.mm, // minutes
            ss: this.$refs.ss, // seconds
        }

        // limit the rolling wheels so 59 ➜ 00 animates smoothly
        this.flows.hh.digits = { 1: { max: 2 }, 0: { max: 9 } } // hours 0-23
        this.flows.mm.digits = { 1: { max: 5 }, 0: { max: 9 } } // minutes 0-59
        this.flows.ss.digits = { 1: { max: 5 }, 0: { max: 9 } } // seconds 0-59

        this.tick() // draw immediately
        this.timer = setInterval(() => this.tick(), 1_000)
    },
    tick() {
        const now = Date.now()
        const diff = Math.max(0, this.targetDate - now)

        if (diff === 0) clearInterval(this.timer) // stop at zero

        // Calculate days, hours, minutes, and seconds properly
        const days = Math.floor(diff / (24 * 3600 * 1000))
        const hours = Math.floor((diff % (24 * 3600 * 1000)) / (3600 * 1000))
        const minutes = Math.floor((diff % (3600 * 1000)) / (60 * 1000))
        const seconds = Math.floor((diff % (60 * 1000)) / 1000)

        this.flows.dd.update(days)
        this.flows.hh.update(hours)
        this.flows.mm.update(minutes)
        this.flows.ss.update(seconds)
    },
    destroy() {
        clearInterval(this.timer)
    }, // tidy up
}))

// Which platform path the homepage is showing: 'mobile' | 'desktop'.
// Shared across sections (hero, explainer) and remembered between visits.
// Registered on alpine:init because $persist only exists once Livewire has
// registered Alpine's plugins.
document.addEventListener('alpine:init', () => {
    Alpine.store('platform', {
        current: Alpine.$persist('mobile').as('nativephpPlatform'),
        is(name) {
            return this.current === name
        },
        select(name) {
            if (this.current === name) return

            this.current = name
            this.revealExplainer()
        },
        // Switching tracks rewrites the explainer, so bring it into view.
        // Two frames: one for Alpine to apply the change, one for the
        // browser to lay it out, so the target offset is the final one.
        revealExplainer() {
            requestAnimationFrame(() =>
                requestAnimationFrame(() => {
                    const target = document.getElementById('platform-explainer')
                    if (!target) return

                    const nav = document.querySelector('[data-site-nav]')
                    const offset = (nav?.offsetHeight ?? 0) + 16
                    const top =
                        target.getBoundingClientRect().top +
                        window.scrollY -
                        offset

                    window.scrollTo({
                        top: Math.max(0, top),
                        behavior: window.matchMedia(
                            '(prefers-reduced-motion: reduce)',
                        ).matches
                            ? 'auto'
                            : 'smooth',
                    })
                }),
            )
        },
    })
})

Livewire.start()

// Docsearch
const docsearchContainerSelector = '#docsearch-desktop'
const docsPathMatch = window.location.pathname.match(
    /^\/docs\/(desktop|mobile)\/(\d+)/,
)

function scopedTransformItems(items) {
    const prefix = `/docs/${docsPathMatch[1]}/${docsPathMatch[2]}/`
    return items.filter((item) => {
        try {
            return new URL(item.url).pathname.startsWith(prefix)
        } catch {
            return item.url.includes(prefix)
        }
    })
}

function getDesktopSearchButton() {
    return document.querySelector(
        `${docsearchContainerSelector} .DocSearch-Button`,
    )
}

// Re-created on demand when "Search everywhere" is clicked, since docsearch()
// has no API to update an already-initialized instance's transformItems —
// only to destroy() and re-initialize it (see docsearch()'s DocSearchInstance
// return value in the @docsearch/js API reference).
function initDocsearch(broadenScope, initialQuery) {
    return docsearch({
        appId: 'ZNII9QZ8WI',
        apiKey: '9be495a1aaf367b47c873d30a8e7ccf5',
        indexName: 'nativephp',
        insights: true,
        debug: false,
        container: docsearchContainerSelector,
        initialQuery,
        ...(docsPathMatch && ! broadenScope && { transformItems: scopedTransformItems }),
    })
}

let docsearchInstance = initDocsearch(false)

const broadenButton = document.getElementById('docsearch-broaden')
if (broadenButton && docsPathMatch) {
    broadenButton.addEventListener('click', () => {
        const query =
            document.querySelector(
                `${docsearchContainerSelector} .DocSearch-Input`,
            )?.value ?? ''

        docsearchInstance.destroy()
        docsearchInstance = initDocsearch(true, query)
        docsearchInstance.open()

        document.getElementById('docsearch-scope-label')?.remove()
    })
}

// Mirror the desktop DocSearch button into the mobile container so that
// pressing Cmd+K only registers one handler (avoiding duplicate modals).
// Looks the button up fresh on click rather than caching a reference, since
// initDocsearch() re-renders it whenever the search scope is broadened.
const mobileContainer = document.getElementById('docsearch-mobile')
if (mobileContainer) {
    const desktopButton = getDesktopSearchButton()
    if (desktopButton) {
        const mobileButton = desktopButton.cloneNode(true)
        mobileContainer.appendChild(mobileButton)
        mobileButton.addEventListener('click', () =>
            getDesktopSearchButton()?.click(),
        )
    }
}
