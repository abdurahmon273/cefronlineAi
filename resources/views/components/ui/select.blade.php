@props([
    'label' => null,
    'placeholder' => 'Select option',
    'options' => ['Option', 'Option', 'Option', 'Option'],
    'value' => null,
    'error' => false,
    'open' => false,
    'name' => null, // optional for real forms
])

@php
    $hasLabel = filled($label);
    $selected = $value;
    $display = $selected ?: $placeholder;

    $base = 'relative w-full rounded-[18px] bg-white shadow-sm outline-none';
    $ring = $error ? 'ring-2 ring-orange-500' : 'ring-1 ring-black/0';
    $pad = $hasLabel ? 'px-5 pt-6 pb-2' : 'px-5 py-3';
    $text = $selected ? 'text-slate-800' : 'text-slate-400';
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }}
     data-select
     data-open="{{ $open ? 'true' : 'false' }}"
     @if ($selected) data-value="{{ $selected }}" @endif>
    @if ($hasLabel)
        <span class="pointer-events-none absolute top-2 left-5 text-[11px] leading-none {{ $error ? 'text-orange-500' : 'text-slate-500' }}">
            {{ $label }}
        </span>
    @endif

    <button type="button"
            class="{{ $base }} {{ $ring }} {{ $pad }} flex w-full items-center justify-between gap-3 text-left text-[15px] {{ $text }} focus:outline-none focus:ring-4 focus:ring-white/10"
            data-select-trigger
            aria-haspopup="listbox"
            aria-expanded="{{ $open ? 'true' : 'false' }}">
        <span data-select-display>{{ $display }}</span>
        <span class="text-slate-400">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </span>
    </button>

    @if ($name)
        <input type="hidden" name="{{ $name }}" value="{{ $selected }}">
    @endif

    <div class="mt-2 rounded-[18px] bg-white p-2 shadow-sm ring-1 ring-black/5"
         data-select-menu
         role="listbox"
         @if (! $open) hidden @endif>
        @foreach ($options as $opt)
            @php $isActive = $selected === $opt; @endphp
            <button type="button"
                    class="block w-full rounded-[14px] px-4 py-3 text-left text-sm text-slate-700 hover:bg-slate-50 {{ $isActive ? 'bg-slate-100' : '' }}"
                    data-select-option
                    data-value="{{ $opt }}"
                    role="option"
                    aria-selected="{{ $isActive ? 'true' : 'false' }}">
                {{ $opt }}
            </button>
        @endforeach
    </div>
</div>


