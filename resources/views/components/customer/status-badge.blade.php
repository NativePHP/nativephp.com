@props(['status'])

@php
$color = match($status) {
    'Active', 'Approved', 'Licensed' => 'green',
    'Expired', 'Pending', 'Pending Review', 'Open' => 'yellow',
    'Needs Renewal', 'In Progress' => 'blue',
    'Suspended', 'Rejected', 'Closed' => 'red',
    'Responded' => 'green',
    'On Hold' => 'zinc',
    default => 'zinc',
};
@endphp

<flux:badge color="{{ $color }}">{{ $status }}</flux:badge>
