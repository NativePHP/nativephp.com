import './fonts'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import './bootstrap'
import Alpine from 'alpinejs'
import codeBlock from './alpine/codeBlock.js'
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
} from 'motion'

// GSAP
gsap.registerPlugin(ScrollTrigger)
window.ScrollTrigger = ScrollTrigger
window.gsap = gsap

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
}

// Alpine
window.Alpine = Alpine

Alpine.data('codeBlock', codeBlock)
Alpine.magic('refAll', (el) => {
    return (refName) => {
        return Array.from(document.querySelectorAll(`[x-ref="${refName}"]`))
    }
})

Alpine.start()
