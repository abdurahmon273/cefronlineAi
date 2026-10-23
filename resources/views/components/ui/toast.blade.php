@props([
    'type' => 'success', // success | warn | error
    'text' => 'Saved',
])

@php
    $map = [
        'success' => ['bg' => 'bg-emerald-50', 'ring' => 'ring-emerald-200', 'text' => 'text-emerald-600', 'iconBg' => 'bg-emerald-600'],
        'warn' => ['bg' => 'bg-amber-50', 'ring' => 'ring-amber-200', 'text' => 'text-amber-600', 'iconBg' => 'bg-amber-600'],
        'error' => ['bg' => 'bg-rose-50', 'ring' => 'ring-rose-200', 'text' => 'text-rose-600', 'iconBg' => 'bg-rose-600'],
    ];
    $c = $map[$type] ?? $map['success'];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center gap-3 rounded-2xl {$c['bg']} px-6 py-4 text-sm font-semibold {$c['text']} shadow-sm ring-1 ring-inset {$c['ring']}"]) }}>
    <span class="grid h-6 w-6 place-items-center rounded-full {{ $c['iconBg'] }} text-white">
        @if ($type === 'warn')
            <span class="text-xs font-black">?</span>
        @elseif ($type === 'error')
            <span class="text-xs font-black">i</span>
        @else
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        @endif
    </span>
    <span>{{ $text }}</span>
</div>


