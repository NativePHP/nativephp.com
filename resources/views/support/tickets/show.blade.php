<x-layout-three-columns>
    <x-slot name="title">
        {{ $supportTicket->subject }}
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ $supportTicket->subject }}
        </h1>
    </x-slot>

    <section class="mt-6">
        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-medium">Ticket Details</h2>
                <p class="text-gray-700 dark:text-gray-300">
                    Ticket ID: <strong>#{{ $supportTicket->mask }}</strong><br>
                    Status: <strong>{{ $supportTicket->status }}</strong><br>
                    Created At: <strong>{{ $supportTicket->created_at->format('d M Y, H:i') }}</strong><br>
                    Updated At: <strong>{{ $supportTicket->updated_at->format('d M Y, H:i') }}</strong>
                </p>
            </div>
        </div>

        {{-- Ticket Messages --}}
        <div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-medium">Messages</h2>
                @foreach([] as $message)
                    <div class="mb-4 p-4 border rounded-lg {{ $message->is_from_user ? 'bg-blue-100' : 'bg-green-100' }}">
                        <p><strong>{{ $message->user->name }}:</strong></p>
                        <p>{{ $message->content }}</p>
                        <p class="text-sm text-gray-500">{{ $message->created_at->format('d M Y, H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Additional Support Information --}}
        <div class="mt-20 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-4 text-2xl font-medium">Need more help?</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Check out our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a> for comprehensive guides and tutorials to help you get the most out of NativePHP.
            </p>
        </div>
    </section>
</x-layout-three-columns>
