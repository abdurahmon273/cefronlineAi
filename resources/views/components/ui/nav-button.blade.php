@props([
    'active' => false,
    'label' => 'Home page',
])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white p-2 shadow-sm']) }}>
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium {{ $active ? 'bg-slate-100 text-slate-900 ring-2 ring-inset ring-violet-400/60' : 'text-slate-400 hover:bg-slate-50' }}">
        <span class="grid h-6 w-6 place-items-center rounded-full {{ $active ? 'bg-orange-600 text-white' : 'bg-slate-200 text-slate-500' }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-10.5z"/>
            </svg>
        </span>
        <span>{{ $label }}</span>
    </div>
</div>


