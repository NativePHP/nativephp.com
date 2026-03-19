@props(['status'])

@php
$color = match($status) {
    'Active', 'Approved', 'Licensed' => 'green',
    'Expired', 'Pending', 'Pending Review' => 'yellow',
    'Needs Renewal' => 'blue',
    'Suspended', 'Rejected' => 'red',
    default => 'zinc',
};
@endphp

<flux:badge color="{{ $color }}">{{ $status }}</flux:badge>
