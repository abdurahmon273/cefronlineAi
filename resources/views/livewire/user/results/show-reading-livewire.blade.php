<div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
  @if($data)
    <x-test-result-header :type="'Reading'" :title="'Reading Test'" :mockType="$type"/>

    <div class="bg-white rounded-2xl p-6 mb-6">
        @if($createdText)
            <p class="text-md font-bold text-gray-500 mb-2 ml-2">Created date {{ $createdText }}</p>
        @endif

        <div class="bg-white rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-8 flex-wrap">
                <div class="flex-shrink-0">
                    <div class="relative w-[92px] h-[92px] shrink-0">
                        <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                            <!-- background ring -->
                            <circle cx="60" cy="60" r="46"
                                    class="fill-none stroke-gray-200"
                                    stroke-width="12" />

                            <!-- progress ring (static demo) -->
                            <circle cx="60" cy="60" r="46"
                                    class="fill-none stroke-orange-500"
                                    stroke-width="12"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{$percentageData['array']}}"
                                    stroke-dashoffset="{{$percentageData['offset']}}" />
                        </svg>

                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-xl font-extrabold text-gray-900" data-stat-percent>{{$percentageData['percent']}}%</div>
                        </div>
                    </div>
                </div>

                @if(!empty($percentageData['is_mock']))
                    <div class="text-center ml-0 sm:ml-6">
                        <p class="text-3xl font-semibold text-gray-900">{{ $percentageData['mock_ball'] ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Ball</p>
                    </div>

                    <div class="text-center ml-0 sm:ml-6">
                        <p class="text-3xl font-semibold text-gray-900">{{ $percentageData['degree'] ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Level</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="bg-[#fafafa] rounded-2xl p-6">
                <h3 class="text-base font-semibold text-gray-500 mb-4">Feedback and Suggestion</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <p><span class="font-semibold text-gray-900">Feedback:</span> {{ $feedback ?? '—' }}</p>
                    <p><span class="font-semibold text-gray-900">Suggestion:</span> {{ $suggestion ?? '—' }}</p>
                </div>
            </div>

            <div class="bg-[#fafafa] rounded-2xl p-6">
                <h3 class="text-base font-semibold text-gray-500 mb-4">Overall Answers</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Total questions:</span>
                        <span class="font-semibold text-gray-900">{{ $all_answers ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Correct answers:</span>
                        <span class="font-semibold text-gray-900">{{ $correct_answers ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Wrong Answers:</span>
                        <span class="font-semibold text-gray-900">{{ $wrong_answers ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{route('user.reading.result.detail',$id)}}"
           class="mt-4 px-12 py-2.5 bg-orange-50 text-[#ea580c] rounded-xl hover:bg-orange-100 transition-colors font-medium text-sm inline-flex items-center justify-center gap-2">
            Detail
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                 stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6l6 6m0 0l-6 6m6-6H3" />
            </svg>
        </a>
    </div>

    @if(!empty($parts_data))
        <div class="mb-4">
            <div class="flex flex-wrap gap-2">
                @foreach($parts_data as $key => $label)
                    <button type="button"
                            class="reading-part-tab-button px-4 py-2 rounded-xl text-sm font-semibold transition-all
                                                       {{ $key === $active ? 'bg-white text-orange-600 shadow-sm' : 'bg-transparent text-gray-500 hover:text-gray-700' }}"
                            data-part="{{ $key }}"
                            aria-controls="part-panel-{{ $key }}"
                            aria-selected="{{ $key === $active ? 'true' : 'false' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @foreach($parts_data as $partKey => $label)
            @php
                $part = $data[$partKey] ?? $$partKey ?? null;
                $rows = $part['question_data'] ?? [];
            @endphp

            <div id="part-panel-{{ $partKey }}"
                 class="reading-part-tab-content {{ $partKey === $active ? '' : 'hidden' }}"
                 data-part="{{ $partKey }}">
                <div class="bg-white rounded-2xl p-6">
                    <div class="p-2 bg-gray-50/50 rounded-2xl hover:bg-gray-100/50 transition-colors">
                        <div class="hidden sm:grid sm:grid-cols-12 gap-6 p-2 mb-2">
                            <div class="col-span-1 text-md font-medium text-gray-400">No</div>
                            <div class="col-span-4 text-md font-medium text-gray-400">Your answer</div>
                            <div class="col-span-4 text-md font-medium text-gray-400">Correct answer</div>
                            <div class="col-span-3 text-md font-medium text-gray-400 text-right">Result</div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        {{--                                            <div class="px-4 py-4 bg-white">--}}
                        {{--                                                <h4 class="text-base font-bold text-gray-900">{{ $label }}</h4>--}}
                        {{--                                                @if(!empty($part['advice']))--}}
                        {{--                                                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $part['advice'] }}</p>--}}
                        {{--                                                @endif--}}
                        {{--                                            </div>--}}

                        @forelse($rows as $i => $q)
                            <div class="px-4 py-5 bg-gray-50/50 rounded-xl hover:bg-gray-100/50 transition-colors">
                                <div class="grid sm:grid-cols-12 gap-6 items-center">
                                    <div class="col-span-12 sm:col-span-1">
                                        <span class="text-base font-medium text-gray-900">{{ $q['num'] ?? ($i + 1) }}</span>
                                    </div>

                                    <div class="col-span-12 sm:col-span-4">
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $q['user_answer'] ?? $q['user_ans'] ?? '—' }}</p>
                                    </div>

                                    <div class="col-span-12 sm:col-span-4">
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $q['correct_answer'] ?? $q['correct_ans'] ?? '—' }}</p>
                                    </div>

                                    <div class="col-span-12 sm:col-span-3 sm:text-right">
                                        @if(!empty($q['is_correct']))
                                            <svg class="w-6 h-6 text-green-500 inline-block" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-500 inline-block" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-gray-500">
                                No data for this part.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    @endif
</div>

@section('scripts')
    @parent
    <script>
        (() => {
            const buttons = Array.from(document.querySelectorAll('.reading-part-tab-button'));
            const panels  = Array.from(document.querySelectorAll('.reading-part-tab-content'));

            if (!buttons.length || !panels.length) return;

            const setActive = (part) => {
                buttons.forEach(btn => {
                    const isActive = btn.dataset.part === part;
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');

                    btn.classList.toggle('bg-white', isActive);
                    btn.classList.toggle('text-orange-600', isActive);
                    btn.classList.toggle('shadow-sm', isActive);

                    btn.classList.toggle('text-gray-500', !isActive);
                    btn.classList.toggle('hover:text-gray-700', !isActive);
                });

                panels.forEach(p => p.classList.toggle('hidden', p.dataset.part !== part));
            };

            const initial = buttons.find(b => b.getAttribute('aria-selected') === 'true')?.dataset.part
                || buttons[0].dataset.part;

            setActive(initial);

            buttons.forEach(btn => {
                btn.addEventListener('click', () => setActive(btn.dataset.part));
            });
        })();
    </script>
@endsection
