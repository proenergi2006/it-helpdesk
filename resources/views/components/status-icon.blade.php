@props(['status', 'label'])

@php
    $icons = [
        'open'             => ['bg' => 'bg-yellow-500', 'path' => 'M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'in_progress'      => ['bg' => 'bg-blue-500',   'path' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99'],
        'resolved'         => ['bg' => 'bg-green-600',  'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'pending_approval' => ['bg' => 'bg-orange-400', 'path' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z'],
        'rejected'         => ['bg' => 'bg-red-700',    'path' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'cancel'           => ['bg' => 'bg-gray-500',   'path' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        'hold'             => ['bg' => 'bg-purple-600', 'path' => 'M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    $icon = $icons[$status] ?? $icons['hold'];
@endphp

<span class="group relative inline-flex" title="{{ $label }}">
    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-white {{ $icon['bg'] }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon['path'] }}" />
        </svg>
    </span>
    <span class="pointer-events-none absolute left-full top-1/2 z-30 ml-2 -translate-y-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-xs font-medium text-white opacity-0 shadow transition-opacity group-hover:opacity-100">{{ $label }}</span>
    <span class="sr-only">{{ $label }}</span>
</span>
