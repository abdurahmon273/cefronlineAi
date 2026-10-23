<div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
   @if($data)
    {{-- Header --}}
    <x-test-result-header :type="'Writing'" :title="'Writing Test'"  :mockType="$type" />

    <div>


        {{-- Part 1 Content --}}
        <div class="part-tab-content" data-part="Part 1">
            {{-- Stats Card --}}
            <div class="bg-white rounded-2xl p-6 mb-6 relative">

                <div class="absolute top-4 right-4 flex gap-2">
                    @if(($writingResult->status ?? 0) == 3)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-green-600 bg-green-50">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-600"></span>
                            <span class="text-xs sm:text-sm font-semibold text-green-600">AI Checked</span>
                        </div>

                        <a href="{{ route('user.results.DownloadResultPdf', ['id'=>$writingResult->id, 'type'=>\App\Enums\CefrTypeEnum::WRITING->value]) }}" class="inline-flex items-center px-4 py-2 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm transition-colors">
                            Download PDF
                        </a>
                    @elseif(($writingResult->with_ai ?? 0) == 1)
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-orange-600 bg-[#FFE9E0]">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-orange-600 animate-pulse"></span>
                            <span class="text-xs sm:text-sm font-semibold text-orange-600">AI checking</span>
                        </div>
                    @endif

                </div>
                <p class="text-sm text-gray-500 mb-1"> Created date {{$createdText}} </p>
                <p class="text-sm text-gray-500 mb-6"> id : 000{{$writingResult->id}} </p>

                <div class="flex items-center gap-8 mb-6">
                    {{-- Score Circle --}}
                    @if(!$writingResult->category_type==3)
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
                    @endif
                    @if($writingResult->status==3)


                        {{-- overall --}}
                        <div class="text-center ml-6">
                            <p class="text-3xl font-semibold text-gray-900">{{$writingResult->percentageData['ball']}}</p>
                            <p class="text-sm text-gray-500 mt-1">Overall</p>
                        </div>
                        {{-- Ball --}}
                        <div class="text-center ml-6">
                            <p class="text-3xl font-semibold text-gray-900">{{\App\Enums\CefrTypeEnum::WRITING_MAX_SCORES[$writingResult->category_type]}}</p>
                            <p class="text-sm text-gray-500 mt-1">Max Ball</p>
                        </div>

                        {{-- Level --}}
                        @if($writingResult->category_type==3)
                            <div class="text-center ml-6">
                                <p class="text-3xl font-semibold text-gray-900">{{$percentageData['degree']}}</p>
                                <p class="text-sm text-gray-500 mt-1">Level</p>
                            </div>

                        @endif
                    @endif

                </div>

                {{-- Feedback Dropdowns --}}
            </div>

            @if(!empty($tabs))
                {{-- Part 1 Tabs --}}
                <div class="mb-6">
                    <nav class="flex space-x-1 bg-gray-100 rounded-lg p-1" aria-label="Tabs">

                        @foreach($tabs as $key => $label)

                            <button type="button"
                                    class="subpart-tab-button whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200
                                                       {{ $key === $active ? 'bg-white text-orange-600 shadow-sm flex-1' : 'text-gray-500 hover:text-gray-700 flex-1' }}"
                                    data-subpart="{{ $key }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>
                <div class="mb-4">
                    <div class="flex flex-wrap gap-2">

                    </div>
                </div>
            @endif

            {{-- Question Content --}}
            <div class="subpart-tab-content " data-subpart="task_one">
                @if($task_one)

                    @if(!empty($task_one['resultData']))
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Scores (Task 1.1 / Task 1.2) -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                                    <!-- Task 1.1 -->
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-baseline gap-2">
                                            <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                                {{ $task_one['resultData']['score_1'] ?? '-' }}
                                            </p>
                                            <p class="text-lg font-semibold text-gray-500">
                                                / {{ $task_one['resultData']['max_score_1'] ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-2xl  text-xs sm:text-sm font-semibold border border-gray-200">
                        Task 1.1
                    </span>
                                    </div>

                                    <!-- Divider -->
                                    <div class="hidden sm:block w-px h-10 bg-gray-200"></div>

                                    <!-- Task 1.2 -->
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-baseline gap-2">
                                            <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                                {{ $task_one['resultData']['score_2'] ?? '-' }}
                                            </p>
                                            <p class="text-lg font-semibold text-gray-500">
                                                / {{ $task_one['resultData']['max_score_2'] ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-2xl  text-xs sm:text-sm font-semibold border border-gray-200">
                        Task 1.2
                    </span>
                                    </div>

                                </div>

                                <!-- Descriptions -->
                                <div class="mt-5 space-y-3">
                                    @if(!empty($task_one['description_1']))
                                        <div>
                                            <p class="text-xs sm:text-sm font-semibold text-gray-900 mb-1">Task 1.1 description</p>
                                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                                {{ $task_one['description_1'] }}
                                            </p>
                                        </div>
                                    @endif

                                    @if(!empty($task_one['description_2']))
                                        <div>
                                            <p class="text-xs sm:text-sm font-semibold text-gray-900 mb-1">Task 1.2 description</p>
                                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                                {{ $task_one['description_2'] }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                            </div>

                        </div>
                    @endif

                    <x-test.task-response-component :taskResponse="$task_one['task_response']" />

                    <div class="bg-white rounded-2xl p-6 mb-4 questionDiv" data-qwrap>
                        <button type="button"
                                class="w-full flex items-center justify-between gap-3"
                                data-qtoggle
                                aria-expanded="false">
                            <h3 class="text-lg font-bold text-gray-900">Question</h3>

                            <span class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                                    </svg>
                                                </span>
                        </button>

                        <div class="mt-4 hidden" data-qpanel>
                            <div class="space-y-4 text-lg text-gray-700">
                                {!! nl2br(e($task_one['question'])) !!}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6">

                        <h3 class="text-lg font-bold text-gray-900 mt-4 mb-4">Answer Task 1.1</h3>

                        <div class="space-y-4 text-lg text-gray-700">
                            {!! $task_one['text_1'] !!}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mt-4 mb-4">Answer Task 1.2</h3>

                        <div class="space-y-4 text-lg text-gray-700">
                            {!! $task_one['text_2'] !!}
                        </div>
                    </div>
                @endif
            </div>

            <div class="subpart-tab-content hidden " data-subpart="task_two">
                @if($task_two)

                    @if($task_two['resultData'])
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Score -->
                                <div class="flex items-baseline gap-3 mb-4">
                                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                        {{$task_two['resultData']['score']}}
                                    </p>
                                    <p class="text-lg font-semibold text-gray-500">
                                        / {{$task_two['resultData']['max_score']}}
                                    </p>

                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                    {{$task_two['description']}}
                                </p>

                            </div>

                        </div>
                    @endif


                    <x-test.task-response-component :taskResponse="$task_two['task_response']" />

                    <div class="bg-white rounded-2xl p-6 mb-4 questionDiv" data-qwrap>
                        <button type="button"
                                class="w-full flex items-center justify-between gap-3"
                                data-qtoggle
                                aria-expanded="false">
                            <h3 class="text-lg font-bold text-gray-900">Question</h3>

                            <span class="shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white transition">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4 text-gray-500 transition-transform duration-200"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
            </svg>
        </span>
                        </button>

                        <div class="mt-4 hidden" data-qpanel>
                            <div class="space-y-4 text-lg text-gray-700">
                                {!! nl2br(e($task_two['question'])) !!}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6">

                        <h3 class="text-lg font-bold text-gray-900 mt-4 mb-4">Answer</h3>

                        <div class="space-y-4 text-lg text-gray-700">
                            {!! $task_two['text'] !!}
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>
    @endif
</div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ✅ Blade'dan keladigan default active subpart
            const DEFAULT_SUBPART = @json($active ?? 'task_one');

            // Main part tabs
            const partButtons = document.querySelectorAll('.part-tab-button');
            const partContents = document.querySelectorAll('.part-tab-content');

            function activatePart(partName) {
                partButtons.forEach(btn => {
                    const isActive = btn.getAttribute('data-part') === partName;
                    btn.classList.toggle('bg-white', isActive);
                    btn.classList.toggle('text-orange-600', isActive);
                    btn.classList.toggle('shadow-sm', isActive);
                    btn.classList.toggle('text-gray-500', !isActive);
                    btn.classList.toggle('hover:text-gray-700', !isActive);
                });

                partContents.forEach(content => {
                    const isActive = content.getAttribute('data-part') === partName;
                    content.classList.toggle('hidden', !isActive);
                });
            }

            partButtons.forEach(button => {
                button.addEventListener('click', () => {
                    activatePart(button.getAttribute('data-part'));
                });
            });

            // Subpart tabs
            const subpartButtons = document.querySelectorAll('.subpart-tab-button');
            const subpartContents = document.querySelectorAll('.subpart-tab-content');

            function activateSubpart(subpartName) {
                subpartButtons.forEach(btn => {
                    const isActive = btn.getAttribute('data-subpart') === subpartName;
                    btn.classList.toggle('bg-white', isActive);
                    btn.classList.toggle('text-orange-600', isActive);
                    btn.classList.toggle('shadow-sm', isActive);
                    btn.classList.toggle('text-gray-500', !isActive);
                    btn.classList.toggle('hover:text-gray-700', !isActive);
                });

                subpartContents.forEach(content => {
                    const isActive = content.getAttribute('data-subpart') === subpartName;
                    content.classList.toggle('hidden', !isActive);
                });
            }

            subpartButtons.forEach(button => {
                button.addEventListener('click', () => {
                    activateSubpart(button.getAttribute('data-subpart'));
                });
            });

            // ✅ Initial state sync (page load)
            // Agar blade’da active task_two bo‘lsa ham, load paytida ko‘rsatib qo‘yadi
            if (subpartContents.length) {
                activateSubpart(DEFAULT_SUBPART);
            }

            // ✅ Question accordion (default yopiq, faqat header ko‘rinadi)
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-qtoggle]');
                if (!btn) return;

                const wrap = btn.closest('[data-qwrap]');
                if (!wrap) return;

                const panel = wrap.querySelector('[data-qpanel]');
                const icon = btn.querySelector('svg');
                if (!panel) return;

                const willOpen = panel.classList.contains('hidden');

                if (willOpen) {
                    panel.classList.remove('hidden');
                    icon && icon.classList.add('rotate-180');
                    btn.setAttribute('aria-expanded', 'true');
                } else {
                    panel.classList.add('hidden');
                    icon && icon.classList.remove('rotate-180');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });

        });
    </script>
@endsection
