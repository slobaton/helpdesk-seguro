<x-app-layout>
    <x-slot name="header">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2>Mis tickets</h2>
                @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Crear Ticket
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4 flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <select name="status" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="open" {{ request('status')==='open' ? 'selected' : '' }}>Abiertos</option>
                        <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>En progreso</option>
                        <option value="closed" {{ request('status')==='closed' ? 'selected' : '' }}>Cerrados</option>
                    </select>
                </form>
            </div>

            @if($tickets->count())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($tickets as $ticket)
                        <x-ticket-card :ticket="$ticket" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tickets->withQueryString()->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-600">
                    No se encontraron tickets
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
