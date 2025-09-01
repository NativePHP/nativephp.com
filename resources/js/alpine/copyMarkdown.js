export default () => ({
    showMessage: false,

    async copyMarkdownToClipboard() {
        try {
            // Get the current page URL and convert it to .md URL
            const currentUrl = window.location.href
            const mdUrl = currentUrl.replace(
                /\/docs\/([^\/]+\/[^\/]+\/.*)$/,
                '/docs/$1.md',
            )

            // Fetch the raw markdown content
            const response = await fetch(mdUrl)
            if (!response.ok) {
                throw new Error('Failed to fetch markdown content')
            }

            const markdownContent = await response.text()

            // Copy to clipboard
            await navigator.clipboard.writeText(markdownContent)

            // Show success message
            this.showMessage = true
            setTimeout(() => {
                this.showMessage = false
            }, 2000)
        } catch (error) {
            console.error('Failed to copy markdown:', error)
            // Could show an error message here if needed
        }
    },
})
