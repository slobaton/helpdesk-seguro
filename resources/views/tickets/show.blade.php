<x-app-layout>
    <x-slot name="header">
        <h2>Ticket #{{ $ticket->id }} — {{ $ticket->title }}</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-8">
        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <div><span class="font-semibold">Estado:</span> {{ $ticket->status->value }}</div>
                    <div><span class="font-semibold">Tipo:</span> {{ $ticket->type->value }}</div>
                    <div><span class="font-semibold">Creado por:</span> {{ $ticket->creator->name }}</div>
                    <div><span class="font-semibold">Asignado a:</span> {{ $ticket->assignee?->name ?? 'Sin asignar' }}</div>
                    <div><span class="font-semibold">Fecha de creación:</span> {{ $ticket->created_at }}</div>
                </div>
                <div>
                    <div class="font-semibold">Descripción</div>
                    <p class="whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <h3 class="font-semibold mb-2">Archivos adjuntos del problema</h3>
            <div class="flex flex-wrap gap-3">
                @foreach ($ticket->problemAttachments as $a)
                    <a href="{{ route('attachments.show', $a) }}" class="underline text-sm">
                        {{ $a->original_name }}
                    </a>
                @endforeach
                @if ($ticket->problemAttachments->isEmpty())
                    <div class="text-sm opacity-70">Sin adjuntos.</div>
                @endif
            </div>
        </div>

        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <h3 class="font-semibold mb-2">Resolución</h3>
            @if ($ticket->resolution_text)
                <p class="whitespace-pre-line">{{ $ticket->resolution_text }}</p>
            @else
                <div class="text-sm opacity-70">Pendiente</div>
            @endif

            <div class="mt-3">
                <h4 class="font-medium mb-1">Adjuntos de la resolución</h4>
                <div class="flex flex-wrap gap-3">
                    @foreach ($ticket->resolutionAttachments as $a)
                        <a href="{{ route('attachments.show', $a) }}" class="underline text-sm">
                            {{ $a->original_name }}
                        </a>
                    @endforeach
                    @if ($ticket->resolutionAttachments->isEmpty())
                        <div class="text-sm opacity-70">Sin adjuntos</div>
                    @endif
                </div>
            </div>
        </div>

        @can('update', $ticket)
            <div class="p-4 bg-white dark:bg-gray-900 rounded shadow space-y-6">
                @can('assign', $ticket)
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-1">
                        <h3 class="text-base font-semibold text-gray-900">Asignar a:</h3>
                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="mt-3">
                            @csrf
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700">Asignar</label>
                            <select id="assigned_to" name="assigned_to" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                                <option value="">-- Seleccionar técnico --</option>
                                @foreach($assignees as $user)
                                    <option value="{{ $user->id }}" @selected(old('assigned_to', $ticket->assigned_to) == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="mt-3 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                Asignar ticket
                            </button>
                        </form>
                    </div>
                @endcan

                <div>
                    <h3 class="font-semibold mb-2">Change status</h3>
                    <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="flex gap-2">
                        @csrf @method('PATCH')
                        <select name="status" class="border rounded p-2">
                            <option value="open" @selected($ticket->status->value === 'open')>Open</option>
                            <option value="in_progress" @selected($ticket->status->value === 'in_progress')>In Progress</option>
                            <option value="closed" @selected($ticket->status->value === 'closed')>Closed</option>
                        </select>
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Update</button>
                    </form>
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Descripción de la solución</h3>
                    <form method="POST" action="{{ route('tickets.resolution.store', $ticket) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <textarea name="resolution_text" rows="5" class="w-full border rounded p-2" required>{{ old('resolution_text', $ticket->resolution_text) }}</textarea>
                        <div>
                            <label class="block text-sm font-medium">Adjuntos de la solución (hasta 10 imágenes)</label>
                            <input name="resolution_attachments[]" type="file" multiple accept="image/*">
                        </div>
                        <button class="px-3 py-2 bg-green-600 text-white rounded">Guardar solución</button>
                    </form>
                </div>
            </div>
        @endcan
    </div>
</x-app-layout>
