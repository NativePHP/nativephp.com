<form
    wire:submit.prevent
    class="
        inline-flex items-center
        opacity-60 hover:opacity-100 transition-opacity duration-200
        focus-within:outline-2 focus-within:rounded-xs focus-within:outline-offset-2
    "
>

    <x-icons.git-branch class="size-4" />

    <select
        name="version"
        wire:model.live="version"
        class="
            -ml-1 py-0 pr-8
            border-0
            text-sm whitespace-nowrap
            focus:ring-0
            dark:bg-black
        "
    >

        @foreach($versions as $number => $label)
            <option value="{{ $number }}">Version {{ $label }}</option>
        @endforeach

    </select>

</form>
