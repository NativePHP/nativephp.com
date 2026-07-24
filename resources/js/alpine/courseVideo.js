const VIMEO_SDK_URL = 'https://player.vimeo.com/api/player.js'

let sdkPromise = null

function loadVimeoSdk() {
    if (window.Vimeo?.Player) {
        return Promise.resolve()
    }

    if (!sdkPromise) {
        sdkPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script')
            script.src = VIMEO_SDK_URL
            script.async = true
            script.onload = resolve
            script.onerror = () =>
                reject(new Error('Unable to load the Vimeo player SDK'))
            document.head.appendChild(script)
        })
    }

    return sdkPromise
}

export default (options = {}) => ({
    videoId: options.videoId,
    skip: options.skip ?? false,
    introSeconds: options.introSeconds ?? 9,
    outroSeconds: options.outroSeconds ?? 10,
    marked: false,
    cleanup: null,

    init() {
        loadVimeoSdk()
            .then(() => this.setupPlayer())
            .catch(() => {})
    },

    setupPlayer() {
        if (!window.Vimeo?.Player || !this.videoId) {
            return
        }

        // Keep the player in a plain closure variable, never a reactive Alpine
        // property: the SDK keys its ready-promise and event registry off the raw
        // instance via WeakMaps, and an Alpine proxy breaks every lookup
        // ("Unknown player. Probably unloaded.").
        const player = new window.Vimeo.Player(this.$el, {
            id: Number(this.videoId),
            autopause: false,
            badge: false,
        })

        player.on('play', () => this.rememberFirstPlay())

        if (this.skip) {
            player.on('loaded', () =>
                player.setCurrentTime(this.introSeconds).catch(() => {}),
            )
            player
                .getDuration()
                .then((duration) => {
                    const cutoff = duration - this.outroSeconds

                    if (cutoff <= this.introSeconds) {
                        return
                    }

                    player.on('timeupdate', ({ seconds }) => {
                        if (seconds >= cutoff) {
                            player.pause().catch(() => {})
                        }
                    })
                })
                .catch(() => {})
        }

        // Toggle play/pause with the space bar anywhere on the page. When the
        // Vimeo iframe itself is focused the keydown fires inside it (and Vimeo
        // handles space natively), so this never double-fires.
        const onKeydown = (e) => {
            if (e.code !== 'Space' || e.repeat) {
                return
            }

            const tag = e.target?.tagName
            if (
                e.target?.isContentEditable ||
                tag === 'INPUT' ||
                tag === 'TEXTAREA' ||
                tag === 'SELECT' ||
                tag === 'BUTTON'
            ) {
                return
            }

            e.preventDefault()
            player
                .getPaused()
                .then((paused) => (paused ? player.play() : player.pause()))
                .catch(() => {})
        }
        window.addEventListener('keydown', onKeydown)

        this.cleanup = () => {
            window.removeEventListener('keydown', onKeydown)
            player.destroy().catch(() => {})
        }
    },

    rememberFirstPlay() {
        if (this.skip || this.marked) {
            return
        }

        this.marked = true
        this.$wire.markVideoPlayed()
    },

    destroy() {
        this.cleanup?.()
    },
})
