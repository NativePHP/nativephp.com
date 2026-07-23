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
    skip: options.skip ?? false,
    introSeconds: options.introSeconds ?? 9,
    outroSeconds: options.outroSeconds ?? 10,
    player: null,
    marked: false,

    init() {
        loadVimeoSdk()
            .then(() => this.setupPlayer())
            .catch(() => {})
    },

    setupPlayer() {
        if (!window.Vimeo?.Player) {
            return
        }

        this.player = new window.Vimeo.Player(this.$el)

        this.player.on('play', () => this.rememberFirstPlay())

        if (this.skip) {
            this.skipOutro()
        }
    },

    rememberFirstPlay() {
        if (this.skip || this.marked) {
            return
        }

        this.marked = true
        this.$wire.markVideoPlayed()
    },

    skipOutro() {
        this.player
            .getDuration()
            .then((duration) => {
                const cutoff = duration - this.outroSeconds

                if (cutoff <= this.introSeconds) {
                    return
                }

                this.player.on('timeupdate', ({ seconds }) => {
                    if (seconds >= cutoff) {
                        this.player.pause().catch(() => {})
                    }
                })
            })
            .catch(() => {})
    },

    destroy() {
        this.player?.unload().catch(() => {})
    },
})
