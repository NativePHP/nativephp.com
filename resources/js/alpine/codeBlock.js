
export default () => ({
    showMessage: false,
    copyToClipboard() {
        navigator.clipboard.writeText(
            // This requires torchlight.options.copyable to be "true" on the PHP side.
            this.$root.querySelector('.torchlight-copy-target').textContent.trim()
        ).then(() => this.showMessage = true)

        setTimeout(() => (this.showMessage = false), 2000)
    },
})
