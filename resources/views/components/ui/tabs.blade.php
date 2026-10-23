@props([
    'items' => [
        ['label' => 'Comments', 'active' => true],
        ['label' => 'Files', 'active' => false],
    ],
])

<div {{ $attributes->merge(['class' => 'w-full rounded-2xl bg-[#F0F0F0] p-1 text-[16px]']) }} data-tabs>
    <div class="grid grid-cols-2 gap-1">
        @foreach ($items as $it)
            @php $active = $it['active'] ?? false; @endphp
            <button type="button"
                    data-tab
                    aria-selected="{{ $active ? 'true' : 'false' }}"
                    class="rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition {{ $active ? 'border-none bg-white text-orange-600' : 'border-none font-regular text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                {{ $it['label'] }}
            </button>
        @endforeach
    </div>
</div>


