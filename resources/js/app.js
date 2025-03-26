import './fonts'
import './bootstrap'
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import persist from '@alpinejs/persist'
import codeBlock from './alpine/codeBlock.js'
import docsearch from '@docsearch/js'
import '@docsearch/css'
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

// Alpine
window.Alpine = Alpine

Alpine.data('codeBlock', codeBlock)
Alpine.magic('refAll', (el) => {
    return (refName) => {
        return Array.from(el.querySelectorAll(`[x-ref="${refName}"]`))
    }
})

Alpine.plugin(collapse)
Alpine.plugin(persist)
Alpine.start()

// Docsearch
docsearch({
    appId: 'ZNII9QZ8WI',
    apiKey: '9be495a1aaf367b47c873d30a8e7ccf5',
    indexName: 'nativephp',
    insights: true,
    container: '#docsearch',
    debug: false,
})
