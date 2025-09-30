<form wire:submit="switch">

    <select wire:model.live="version" name="version">

        @foreach($versions as $number => $label)
            <option value="{{ $number }}">{{ $label }}</option>
        @endforeach

    </select>

</form>
