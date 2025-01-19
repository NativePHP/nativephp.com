<a {{ $attributes->class(['group
inline-block
        text-sm bg-purple-500 border-purple-600
dark:border-purple-500 dark:bg-purple-700
hover:bg-purple-600 dark:hover:bg-purple-800
        px-4 py-1.5
        border rounded-md
        font-medium
        text-white
        ']) }} href="{{route('early-adopter')}}">
    <div class="group-hover:animate-none flex items-center gap-2"
         x-data="{earlyAnimated: $persist(false)}"
         x-init="setTimeout(() => {earlyAnimated = true}, 6000)"
         x-bind:class="!earlyAnimated ? 'animate-pulse' : ''"
         x-on:click="earlyAnimated = false"
    >

        <x-icons.party-popper class="size-4"/>
        {{--                <x-icons.sparkles class="size-4"/>--}}
        {{--                <x-icons.device-mobile-phone class="size-4"/>--}}
        <span>Soon on iOS!</span>
    </div>
</a>
