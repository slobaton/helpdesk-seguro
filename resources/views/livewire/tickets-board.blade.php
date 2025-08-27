<div class="grid grid-cols-1 md:grid-cols-3 gap-6"
     data-kanban
     data-col-open="kanban-open"
     data-col-in_progress="kanban-in-progress"
     data-col-closed="kanban-closed">
    {{-- ABIERTOS --}}
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow">
        <h3 class="font-semibold mb-3">ABIERTOS</h3>
        <div id="kanban-open" class="space-y-3 min-h-[200px] p-2 border rounded bg-gray-50 dark:bg-gray-800"
             data-status="open">
            @foreach ($open as $ticket)
                <div class="p-3 bg-white dark:bg-gray-700 rounded shadow-sm"
                     data-ticket-id="{{ $ticket['id'] }}">
                    <div class="text-sm font-semibold">{{ $ticket['title'] }}</div>
                    <div class="text-xs opacity-70">Tipo de problema: {{ $ticket['type'] }}</div>
                    <div class="text-xs opacity-70">Creado por: {{$ticket['creator']['name'] }}</div>
                    <a href="{{ route('tickets.show', $ticket['id']) }}" class="text-xs underline">Ver ticket</a>
                </div>
            @endforeach
        </div>
    </div>

    {{--  EN PROGRESO  --}}
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow">
        <h3 class="font-semibold mb-3">EN PROGRESO</h3>
        <div id="kanban-in-progress" class="space-y-3 min-h-[200px] p-2 border rounded bg-gray-50 dark:bg-gray-800"
             data-status="in_progress">
            @foreach ($in_progress as $ticket)
                <div class="p-3 bg-white dark:bg-gray-700 rounded shadow-sm"
                     data-ticket-id="{{ $ticket['id'] }}">
                    <div class="text-sm font-semibold">{{ $ticket['title'] }}</div>
                    <div class="text-xs opacity-70">Tipo de problema: {{ $ticket['type'] }}</div>
                    <div class="text-xs opacity-70">Asignado a: {{ $ticket['assignee']['name'] ?? '—' }}</div>
                    <a href="{{ route('tickets.show', $ticket['id']) }}" class="text-xs underline">Ver ticket</a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CERRADOS --}}
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow">
        <h3 class="font-semibold mb-3">CERRADOS</h3>
        <div id="kanban-closed" class="space-y-3 min-h-[200px] p-2 border rounded bg-gray-50 dark:bg-gray-800"
             data-status="closed">
            @foreach ($closed as $ticket)
                <div class="p-3 bg-white dark:bg-gray-700 rounded shadow-sm"
                     data-ticket-id="{{ $ticket['id'] }}">
                    <div class="text-sm font-semibold">{{ $ticket['title'] }}</div>
                    <div class="text-xs opacity-70">Resuelto en fecha: {{ $ticket['resolved_at'] ?? '—' }}</div>
                    <a href="{{ route('tickets.show', $ticket['id']) }}" class="text-xs underline">Ver ticket</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
