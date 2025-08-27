@props(['ticket'])

@php
    $statusColors = [
        'open'        => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
        'closed'      => 'bg-green-100 text-green-800 border-green-200',
    ];

    $statusKey = $ticket->status instanceof \BackedEnum
        ? $ticket->status->value
        : (string) $ticket->status;


    $statusClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800 border-gray-200';

    $assignedName = optional($ticket->assignee)->name ?? 'No asignado';
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h3 class="text-base font-semibold text-gray-900">
                {{ $ticket->title }}
            </h3>
            <p class="mt-1 text-sm text-gray-600 line-clamp-3">
                {{ $ticket->description }}
            </p>
        </div>

        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium border {{ $statusClass }}">
            {{ \Illuminate\Support\Str::of($statusKey)->replace('_',' ')->title() }}
        </span>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-700">
        <span class="inline-flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 12c2.761 0 5-2.686 5-6S14.761 0 12 0 7 2.686 7 6s2.239 6 5 6Zm0 2c-4.418 0-8 2.239-8 5v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1c0-2.761-3.582-5-8-5Z" fill="currentColor"/>
            </svg>
            <strong>Asignado a:</strong> <span class="ml-1">{{ $assignedName }}</span>
        </span>

        <span class="inline-flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <strong>Tipo:</strong> <span class="ml-1 capitalize">{{ $ticket->type }}</span>
        </span>
    </div>

    <div class="mt-4">
        <a href="{{ route('tickets.show', $ticket) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-800 hover:bg-gray-50">
            Abrir
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 17L17 7M17 7H7M17 7v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </a>
    </div>
</div>
