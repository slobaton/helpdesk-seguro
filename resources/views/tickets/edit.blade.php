<x-app-layout>
    <x-slot name="header">
        <h2>Edit Ticket #{{ $ticket->id }}</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium">Title</label>
                <input name="title" type="text" required minlength="3" maxlength="120"
                       class="w-full border rounded p-2" value="{{ old('title', $ticket->title) }}">
            </div>

            <div>
                <label class="block text-sm font-medium">Type</label>
                <select name="type" class="w-full border rounded p-2" required>
                    @foreach (['service','hardware','software'] as $t)
                        <option value="{{ $t }}" @selected($ticket->type->value === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea name="description" rows="6" required maxlength="5000"
                          class="w-full border rounded p-2">{{ old('description', $ticket->description) }}</textarea>
            </div>

            <div class="pt-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
