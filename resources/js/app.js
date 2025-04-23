import './fonts'
import './bootstrap'
import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm'
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
Alpine.data('codeBlock', codeBlock)
Alpine.magic('refAll', (el) => {
    return (refName) => {
        return Array.from(el.querySelectorAll(`[x-ref="${refName}"]`))
    }
})

Livewire.start()

// Docsearch
docsearch({
    appId: 'ZNII9QZ8WI',
    apiKey: '9be495a1aaf367b47c873d30a8e7ccf5',
    indexName: 'nativephp',
    insights: true,
    container: '#docsearch',
    debug: false,
})
