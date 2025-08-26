<x-app-layout>
    <x-slot name="header">
        <h2>Ticket #{{ $ticket->id }} — {{ $ticket->title }}</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-8">
        {{-- Summary --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <div><span class="font-semibold">Status:</span> {{ $ticket->status->value }}</div>
                    <div><span class="font-semibold">Type:</span> {{ $ticket->type->value }}</div>
                    <div><span class="font-semibold">Created by:</span> {{ $ticket->creator->name }}</div>
                    <div><span class="font-semibold">Assigned to:</span> {{ $ticket->assignee?->name ?? '—' }}</div>
                    <div><span class="font-semibold">Created at:</span> {{ $ticket->created_at }}</div>
                </div>
                <div>
                    <div class="font-semibold">Description</div>
                    <p class="whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
            </div>
        </div>

        {{-- Problem attachments --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <h3 class="font-semibold mb-2">Problem attachments</h3>
            <div class="flex flex-wrap gap-3">
                @foreach ($ticket->problemAttachments as $a)
                    <a href="{{ route('attachments.show', $a) }}" class="underline text-sm">
                        {{ $a->original_name }}
                    </a>
                @endforeach
                @if ($ticket->problemAttachments->isEmpty())
                    <div class="text-sm opacity-70">No attachments.</div>
                @endif
            </div>
        </div>

        {{-- Resolution (visible summary for any role) --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded shadow">
            <h3 class="font-semibold mb-2">Resolution</h3>
            @if ($ticket->resolution_text)
                <p class="whitespace-pre-line">{{ $ticket->resolution_text }}</p>
            @else
                <div class="text-sm opacity-70">Pending.</div>
            @endif

            <div class="mt-3">
                <h4 class="font-medium mb-1">Resolution attachments</h4>
                <div class="flex flex-wrap gap-3">
                    @foreach ($ticket->resolutionAttachments as $a)
                        <a href="{{ route('attachments.show', $a) }}" class="underline text-sm">
                            {{ $a->original_name }}
                        </a>
                    @endforeach
                    @if ($ticket->resolutionAttachments->isEmpty())
                        <div class="text-sm opacity-70">No attachments.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Technician/Admin actions --}}
        @can('update', $ticket)
            <div class="p-4 bg-white dark:bg-gray-900 rounded shadow space-y-6">
                {{-- Assign --}}
                <div>
                    <h3 class="font-semibold mb-2">Assign</h3>
                    <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="flex gap-2">
                        @csrf
                        <input name="assigned_to" type="number" class="border rounded p-2" placeholder="User ID">
                        <button class="px-3 py-2 bg-blue-600 text-white rounded">Assign</button>
                    </form>
                    <p class="text-xs opacity-70 mt-1">Technicians can self-assign; Admin can assign anyone.</p>
                </div>

                {{-- Change status --}}
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

                {{-- Add/Update resolution with attachments --}}
                <div>
                    <h3 class="font-semibold mb-2">Resolution</h3>
                    <form method="POST" action="{{ route('tickets.resolution.store', $ticket) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <textarea name="resolution_text" rows="5" class="w-full border rounded p-2" required>{{ old('resolution_text', $ticket->resolution_text) }}</textarea>
                        <div>
                            <label class="block text-sm font-medium">Resolution attachments (images, up to 10)</label>
                            <input name="resolution_attachments[]" type="file" multiple accept="image/*">
                        </div>
                        <button class="px-3 py-2 bg-green-600 text-white rounded">Save resolution</button>
                    </form>
                </div>

                {{-- Upload additional problem/resolution files --}}
                <div>
                    <h3 class="font-semibold mb-2">Upload attachments</h3>
                    <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="flex flex-col gap-2">
                        @csrf
                        <select name="stage" class="border rounded p-2">
                            <option value="problem">Problem</option>
                            <option value="resolution">Resolution</option>
                        </select>
                        <input name="files[]" type="file" multiple accept="image/*">
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Upload</button>
                    </form>
                </div>
            </div>
        @endcan
    </div>
</x-app-layout>
