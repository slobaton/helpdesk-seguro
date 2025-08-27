<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2>Nuevo ticket</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Título</label>
                <input name="title" type="text" required minlength="3" maxlength="120"
                       class="w-full border rounded p-2" value="{{ old('title') }}">
                @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Tipo de servicio</label>
                <select name="type" class="w-full border rounded p-2" required>
                    <option value="service">Servicio</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                </select>
                @error('type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Descripción</label>
                <textarea name="description" rows="6" required maxlength="5000"
                          class="w-full border rounded p-2">{{ old('description') }}</textarea>
                @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Imagenes del problema (solo imagenes - hasta 5)</label>
                <input name="problem_attachments[]" type="file" multiple accept="image/*" class="w-full">
                @error('problem_attachments')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                @error('problem_attachments.*')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="pt-2">
                <button class="px-4 py-2 rounded bg-blue-600 text-white">Crear ticket</button>
            </div>
        </form>
    </div>
</x-app-layout>
