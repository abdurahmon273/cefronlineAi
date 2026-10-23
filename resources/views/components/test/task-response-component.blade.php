@props([
    'taskResponse' => [],
])

@php
    use Illuminate\Support\Str;

    $uid = 'task-response-' . Str::random(10);

    $labels = [
        'task_achievement' => 'Task Achievement',
        'lexical_resource' => 'Lexical Resource',
        'coherence_and_cohesion' => 'Coherence and Cohesion',
        'grammatical_range_and_accuracy' => 'Grammatical Range and Accuracy',
    ];

    $order = array_keys($labels);
@endphp

@if(!empty($taskResponse) && is_array($taskResponse))
    <div id="{{ $uid }}" class="mb-6">
        <div class="rounded-2xl bg-white border border-gray-200">
            <div class="p-4 md:p-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($order as $key)
                    @php
                        $title = $labels[$key] ?? Str::headline($key);
                        $body  = $taskResponse[$key] ?? null;
                        $body  = is_string($body) ? trim($body) : $body;
                    @endphp

                    @if(!empty($body))
                        <div class="rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-colors"
                             data-accordion-item
                             data-key="{{ $key }}">

                            <button type="button"
                                    class="w-full flex items-center justify-between gap-3 p-4"
                                    data-accordion-btn
                                    aria-expanded="false">
                                <p class="text-sm font-semibold text-gray-900">{{ $title }}</p>

                                <span class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                </span>
                            </button>

                            <div class="px-4 pb-4 hidden" data-accordion-panel>
                                <div class="pt-2 border-t border-gray-100">
                                    <div class="text-sm leading-7 text-gray-700 whitespace-pre-line">
                                        {!! nl2br(e($body)) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <script>
            (function () {
                const root = document.getElementById(@json($uid));
                if (!root) return;

                // ✅ Pair mapping (juftlar)
                const pairMap = {
                    task_achievement: 'lexical_resource',
                    lexical_resource: 'task_achievement',
                    coherence_and_cohesion: 'grammatical_range_and_accuracy',
                    grammatical_range_and_accuracy: 'coherence_and_cohesion',
                };

                function getItemByKey(key) {
                    return root.querySelector(`[data-accordion-item][data-key="${key}"]`);
                }

                function setOpen(item, open) {
                    if (!item) return;

                    const panel = item.querySelector('[data-accordion-panel]');
                    const btn   = item.querySelector('[data-accordion-btn]');
                    const icon  = item.querySelector('svg');

                    if (!panel || !btn) return;

                    if (open) {
                        panel.classList.remove('hidden');
                        icon && icon.classList.add('rotate-180');
                        btn.setAttribute('aria-expanded', 'true');
                    } else {
                        panel.classList.add('hidden');
                        icon && icon.classList.remove('rotate-180');
                        btn.setAttribute('aria-expanded', 'false');
                    }
                }

                function isOpen(item) {
                    const panel = item?.querySelector?.('[data-accordion-panel]');
                    return panel ? !panel.classList.contains('hidden') : false;
                }

                function toggleItem(item) {
                    const openNow = !isOpen(item);
                    setOpen(item, openNow);
                    return openNow; // true => ochildi, false => yopildi
                }

                root.addEventListener('click', function (e) {
                    const btn = e.target.closest('[data-accordion-btn]');
                    if (!btn || !root.contains(btn)) return;

                    const item = btn.closest('[data-accordion-item]');
                    if (!item) return;

                    const key = item.getAttribute('data-key');
                    const pairKey = pairMap[key];

                    // 1) Bosilgan item toggle bo‘ladi
                    const openedNow = toggleItem(item);

                    // 2) ✅ Jufti ham xuddi shunday holatga o‘tsin (open bo‘lsa open, close bo‘lsa close)
                    if (pairKey) {
                        const pairItem = getItemByKey(pairKey);

                        // Infinite loop bo‘lmaydi, chunki bu yerda click trigger qilmayapmiz
                        setOpen(pairItem, openedNow);
                    }
                });
            })();
        </script>
    </div>
@endif
