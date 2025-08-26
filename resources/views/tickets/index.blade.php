<x-app-layout>
    <x-slot name="header">
        <h2>Mis tickets</h2>
    </x-slot>

    <div class="py-6">
        <a href="{{ route('tickets.create') }}" class="btn">New ticket</a>
        <div class="grid gap-4 mt-4">
            @foreach ($tickets as $ticket)
                <div class="p-4 border rounded">
                    <div class="font-semibold">{{ $ticket->title }}</div>
                    <div>Status: {{ $ticket->status->value }}</div>
                    <a href="{{ route('tickets.show', $ticket) }}" class="underline">Abrir</a>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
