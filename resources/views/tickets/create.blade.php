<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2>Nuevo ticket</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Crear nuevo ticket</h3>

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="mt-3 space-y-4">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                    <input id="title" name="title" type="text" required minlength="3" maxlength="120"
                           class="mt-1 block w-full rounded-lg border-gray-300 text-sm p-2"
                           value="{{ old('title') }}">
                    @error('title')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipo de servicio</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-lg border-gray-300 text-sm p-2" required>
                        <option value="service" @selected(old('type') === 'service')>Servicio</option>
                        <option value="hardware" @selected(old('type') === 'hardware')>Hardware</option>
                        <option value="software" @selected(old('type') === 'software')>Software</option>
                    </select>
                    @error('type')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" rows="6" required maxlength="5000"
                              class="mt-1 block w-full rounded-lg border-gray-300 text-sm p-2">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="problem_attachments" class="block text-sm font-medium text-gray-700">
                        Imágenes del problema (hasta 5)
                    </label>
                    <input id="problem_attachments" name="problem_attachments[]" type="file" multiple accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-700">
                    @error('problem_attachments')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                    @error('problem_attachments.*')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Crear ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
