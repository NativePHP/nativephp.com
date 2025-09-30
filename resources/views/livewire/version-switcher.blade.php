<form wire:submit.prevent>

    <div class="inline-flex items-center opacity-60 hover:opacity-100 transition-opacity duration-200 focus-within:outline-2 focus-within:rounded-xs focus-within:outline-offset-2">

        <x-icons.git-branch class="size-4" />

        <select
            name="version"
            wire:model.live="version"
            class="text-sm -ml-1 py-0 pr-8 bg-transparent border-0 whitespace-nowrap focus:ring-0"
        >

            @foreach($versions as $number => $label)
                <option value="{{ $number }}">Version {{ $label }}</option>
            @endforeach

        </select>
    </div>

</form>
