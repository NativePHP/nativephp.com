<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $imageUrl = $getImageUrl();
        $targetWidth = $getTargetWidth();
        $targetHeight = $getTargetHeight();

        $noteStyles = 'border-radius: 0.5rem; background: rgba(127, 127, 127, 0.08); padding: 1.5rem 1rem; text-align: center; font-size: 0.875rem; color: #6b7280;';
        $handleStyles = 'position: absolute; width: 0.875rem; height: 0.875rem; border-radius: 9999px; background: #fff; box-shadow: 0 0 0 1px #9ca3af, 0 1px 2px rgba(0, 0, 0, 0.3); touch-action: none;';
    @endphp

    @if ($getHasPendingImage())
        <div style="{{ $noteStyles }}">
            The hero image has changed. Save the article, then adjust this
            crop.
        </div>
    @elseif (! $imageUrl)
        <div style="{{ $noteStyles }}">
            Upload a hero image to select a crop.
        </div>
    @else
        <div
            x-data="{
                state: $wire.$entangle(@js($getStatePath())),
                ratio: @js($targetWidth) / @js($targetHeight),
                naturalWidth: 0,
                naturalHeight: 0,
                crop: null,
                drag: null,

                init() {
                    if (this.$refs.image.complete && this.$refs.image.naturalWidth) {
                        this.setup()
                    }
                },

                setup() {
                    this.naturalWidth = this.$refs.image.naturalWidth
                    this.naturalHeight = this.$refs.image.naturalHeight
                    this.crop = this.restoredCrop() ?? this.defaultCrop()
                    this.commit()
                },

                restoredCrop() {
                    const saved = this.state

                    if (! saved || typeof saved !== 'object' || ! (saved.width > 0) || ! (saved.height > 0)) {
                        return null
                    }

                    let width = Math.min(Math.max(saved.width, this.minWidth()), this.naturalWidth)
                    let height = width / this.ratio

                    if (height > this.naturalHeight) {
                        height = this.naturalHeight
                        width = height * this.ratio
                    }

                    return {
                        x: Math.min(Math.max(saved.x ?? 0, 0), this.naturalWidth - width),
                        y: Math.min(Math.max(saved.y ?? 0, 0), this.naturalHeight - height),
                        width,
                        height,
                    }
                },

                defaultCrop() {
                    const width = Math.min(this.naturalWidth, this.naturalHeight * this.ratio)
                    const height = width / this.ratio

                    return {
                        x: (this.naturalWidth - width) / 2,
                        y: (this.naturalHeight - height) / 2,
                        width,
                        height,
                    }
                },

                minWidth() {
                    return Math.min(this.naturalWidth, Math.max(this.naturalWidth * 0.1, 60))
                },

                commit() {
                    if (! this.crop) return

                    this.state = {
                        x: Math.round(this.crop.x),
                        y: Math.round(this.crop.y),
                        width: Math.round(this.crop.width),
                        height: Math.round(this.crop.height),
                    }
                },

                resetCrop() {
                    this.crop = this.defaultCrop()
                    this.commit()
                },

                displayScale() {
                    return this.naturalWidth / this.$refs.image.getBoundingClientRect().width
                },

                startDrag(event, mode) {
                    if (! this.crop) return

                    this.drag = {
                        mode,
                        pointerX: event.clientX,
                        pointerY: event.clientY,
                        start: { ...this.crop },
                        scale: this.displayScale(),
                    }
                },

                onDrag(event) {
                    if (! this.drag) return

                    event.preventDefault()

                    const dx = (event.clientX - this.drag.pointerX) * this.drag.scale
                    const dy = (event.clientY - this.drag.pointerY) * this.drag.scale

                    this.drag.mode === 'move' ? this.moveTo(dx, dy) : this.resizeTo(dx, dy)
                },

                moveTo(dx, dy) {
                    const start = this.drag.start

                    this.crop.x = Math.min(Math.max(start.x + dx, 0), this.naturalWidth - this.crop.width)
                    this.crop.y = Math.min(Math.max(start.y + dy, 0), this.naturalHeight - this.crop.height)
                },

                resizeTo(dx, dy) {
                    const handle = this.drag.mode
                    const start = this.drag.start

                    const anchorX = handle.includes('w') ? start.x + start.width : start.x
                    const anchorY = handle.includes('n') ? start.y + start.height : start.y

                    const widthFromX = start.width + (handle.includes('e') ? dx : -dx)
                    const widthFromY = (start.height + (handle.includes('s') ? dy : -dy)) * this.ratio

                    let width =
                        Math.abs(widthFromX - start.width) >= Math.abs(widthFromY - start.width)
                            ? widthFromX
                            : widthFromY

                    const maxWidth = Math.min(
                        handle.includes('e') ? this.naturalWidth - anchorX : anchorX,
                        (handle.includes('s') ? this.naturalHeight - anchorY : anchorY) * this.ratio,
                    )

                    width = Math.min(Math.max(width, this.minWidth()), maxWidth)
                    const height = width / this.ratio

                    this.crop = {
                        x: handle.includes('e') ? anchorX : anchorX - width,
                        y: handle.includes('s') ? anchorY : anchorY - height,
                        width,
                        height,
                    }
                },

                endDrag() {
                    if (! this.drag) return

                    this.drag = null
                    this.commit()
                },

                cropStyle() {
                    if (! this.crop || ! this.naturalWidth) {
                        return { display: 'none' }
                    }

                    return {
                        display: 'block',
                        left: `${(this.crop.x / this.naturalWidth) * 100}%`,
                        top: `${(this.crop.y / this.naturalHeight) * 100}%`,
                        width: `${(this.crop.width / this.naturalWidth) * 100}%`,
                        height: `${(this.crop.height / this.naturalHeight) * 100}%`,
                    }
                },
            }"
            x-on:pointermove.window="onDrag($event)"
            x-on:pointerup.window="endDrag()"
            x-on:pointercancel.window="endDrag()"
            {{ $getExtraAttributeBag() }}
        >
            <div
                style="position: relative; display: inline-block; max-width: 100%; overflow: hidden; border-radius: 0.5rem; vertical-align: top; line-height: 0;"
            >
                <img
                    x-ref="image"
                    src="{{ $imageUrl }}"
                    alt="Crop selection source"
                    style="display: block; max-height: 24rem; width: auto; max-width: 100%; user-select: none;"
                    draggable="false"
                    x-on:load="setup()"
                />

                <div
                    style="position: absolute; cursor: move; touch-action: none; border: 2px solid #fff; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.55);"
                    :style="cropStyle()"
                    x-on:pointerdown.prevent="startDrag($event, 'move')"
                >
                    <span
                        style="{{ $handleStyles }} top: -0.4375rem; left: -0.4375rem; cursor: nwse-resize;"
                        x-on:pointerdown.stop.prevent="startDrag($event, 'nw')"
                    ></span>
                    <span
                        style="{{ $handleStyles }} top: -0.4375rem; right: -0.4375rem; cursor: nesw-resize;"
                        x-on:pointerdown.stop.prevent="startDrag($event, 'ne')"
                    ></span>
                    <span
                        style="{{ $handleStyles }} bottom: -0.4375rem; left: -0.4375rem; cursor: nesw-resize;"
                        x-on:pointerdown.stop.prevent="startDrag($event, 'sw')"
                    ></span>
                    <span
                        style="{{ $handleStyles }} bottom: -0.4375rem; right: -0.4375rem; cursor: nwse-resize;"
                        x-on:pointerdown.stop.prevent="startDrag($event, 'se')"
                    ></span>
                </div>
            </div>

            <div
                style="margin-top: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; font-size: 0.75rem; color: #6b7280;"
            >
                <span>
                    Drag to reposition, drag a corner to resize. Output:
                    {{ $targetWidth }}&times;{{ $targetHeight }}px.
                </span>

                <button
                    type="button"
                    style="flex-shrink: 0; font-weight: 500; color: #8b5cf6; text-decoration: underline; cursor: pointer;"
                    x-on:click.prevent="resetCrop()"
                >
                    Reset crop
                </button>
            </div>
        </div>
    @endif
</x-dynamic-component>
