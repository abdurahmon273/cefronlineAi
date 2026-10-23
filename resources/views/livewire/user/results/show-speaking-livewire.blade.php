<div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
    <x-test-result-header :type="'Speaking'" :title="'Speaking Test'"  :mockType="$type"/>
    @if(!empty($data))
    {{-- Header --}}

    <div class="bg-white rounded-2xl p-6 mb-6 relative">

        <div class="absolute top-4 right-4 flex gap-2">
            @if(($speakingResult->status ?? 0) == 3)
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-green-600 bg-green-50">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-600"></span>
                    <span class="text-xs sm:text-sm font-semibold text-green-600">AI Checked</span>
                </div>
                <a href="{{ route('user.results.DownloadResultPdf', [$speakingResult->id, \App\Enums\CefrTypeEnum::SPEAKING->value]) }}" class="inline-flex items-center px-4 py-2 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm transition-colors">
                    Download PDF
                </a>
            @elseif(($speakingResult->with_ai ?? 0) == 1)
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-orange-600 bg-[#FFE9E0]">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-orange-600 animate-pulse"></span>
                    <span class="text-xs sm:text-sm font-semibold text-orange-600">AI checking</span>
                </div>
            @endif

        </div>
        <p class="text-sm text-gray-500 mb-1"> Created date: {{$createdText}} </p>
        <p class="text-sm text-gray-500 mb-6"> id: 000{{$speakingResult->id}} </p>

        <div class="flex items-center gap-8 mb-6">
            {{-- Score Circle --}}

            @if($speakingResult->status==3)

                {{-- Ball --}}
                <div class="text-center ml-6">
                    <p class="text-3xl font-semibold text-gray-900">{{$speakingResult->percentageData['ball']}}</p>
                    <p class="text-sm text-gray-500 mt-1">Overall</p>
                </div>
                {{-- Ball --}}
                <div class="text-center ml-6">
                    <p class="text-3xl font-semibold text-gray-900">{{\App\Enums\CefrTypeEnum::SPEAKING_MAX_SCORES[$speakingResult->category_type]}}</p>
                    <p class="text-sm text-gray-500 mt-1">Max Ball</p>
                </div>

                {{-- Level --}}
                @if($speakingResult->category_type==4)
                    @if($percentageData['degree'])
                        <div class="text-center ml-6">
                            <p class="text-3xl font-semibold text-gray-900">{{$percentageData['degree']}}</p>
                            <p class="text-sm text-gray-500 mt-1">Level</p>
                        </div>
                    @endif
                @endif
            @endif

        </div>

        {{-- Feedback Dropdowns --}}
    </div>

    <div class="mb-6">
        <nav class="flex space-x-1 bg-gray-100 rounded-lg p-1" aria-label="Tabs">
            @foreach($tabs as $key => $label)
                <button type="button"
                        class="speaking-subpart-tab-button whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200
                                                 {{ $key === $active ? 'bg-white text-orange-600 shadow-sm flex-1' : 'text-gray-500 hover:text-gray-700 flex-1' }}"
                        data-part="{{ $key }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="speaking-part-content {{ 'part_one' === $active ? '' : 'hidden' }}" data-part="part_one">
        <div class="bg-white rounded-2xl p-6">
            <div class="">
                <h2 class="text-base font-semibold text-gray-900 mb-6">Part 1.1 | Questions</h2>



                @if($part_one_first)

                    @if($part_one_first['resultData'])
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Score -->
                                <div class="flex items-baseline gap-3 mb-4">
                                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                        {{$part_one_first['resultData']['score']}}
                                    </p>
                                    <p class="text-lg font-semibold text-gray-500">
                                        / {{$part_one_first['resultData']['max_score']}}
                                    </p>
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                    {{$part_one_first['resultData']['description']}}
                                </p>

                            </div>

                        </div>

                    @endif

                    <div class="space-y-6">
                        @foreach ($part_one_first['questions'] as $question)
                            <x-test.speaking-question-component :question="$question['question']" :answerText="$question['answer_text']" :audioLink="$question['audio_link']" />
                        @endforeach
                    </div>
                @endif

                <h2 class="text-base font-semibold text-gray-900 mt-5 mb-6">Part 1.2 | Questions</h2>

                @if($part_one_second)

                    @if($part_one_second['resultData'])
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Score -->
                                <div class="flex items-baseline gap-3 mb-4">
                                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                        {{$part_one_second['resultData']['score']}}
                                    </p>
                                    <p class="text-lg font-semibold text-gray-500">
                                        / {{$part_one_second['resultData']['max_score']}}
                                    </p>
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                    {{$part_one_second['resultData']['description']}}
                                </p>

                            </div>

                        </div>

                    @endif


                    <div class="mb-2 ">
                        <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] flex gap-5 p-5">

                            @foreach($part_one_second['images'] as $image)
                                <div class="w-full">
                                    <img src="{{ asset($image['url']) }}" alt="">
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="space-y-6">
                        @foreach ($part_one_second['questions'] as $question)
                            <x-test.speaking-question-component :question="$question['question']" :answerText="$question['answer_text']"   :audioLink="$question['audio_link']" />
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="speaking-part-content {{ 'part_two' === $active ? '' : 'hidden' }}" data-part="part_two">
        <div class="bg-white rounded-2xl p-6">
            <div class="">
                <h2 class="text-base font-semibold text-gray-900 mb-6">Part 2</h2>

                @if(!empty($part_two))

                    @if($part_two['resultData'])
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Score -->
                                <div class="flex items-baseline gap-3 mb-4">
                                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                        {{$part_two['resultData']['score']}}
                                    </p>
                                    <p class="text-lg font-semibold text-gray-500">
                                        / {{$part_two['resultData']['max_score']}}
                                    </p>
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                    {{$part_two['resultData']['description']}}
                                </p>

                            </div>

                        </div>

                    @endif


                    {{-- single question --}}
                    @if(!empty($part_two['question']))
                        <div class="space-y-6">
                            <x-test.speaking-question-component
                                :question="$part_two['question']['question']"
                                :answerText="$part_two['question']['answer_text']"
                                :audioLink="$part_two['question']['audio_link'] ?? null"
                            />
                        </div>
                    @endif
                    {{-- images (agar bo‘lsa) --}}
                    @if(!empty($part_two['images']) && count($part_two['images']))
                        <div class="mt-4">
                            <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] flex gap-5 p-5">
                                @foreach($part_two['images'] as $image)
                                    <div class="w-full">
                                        <img src="{{ asset($image['url']) }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @endif

            </div>
        </div>
    </div>

    <div class="speaking-part-content {{ 'part_three' === $active ? '' : 'hidden' }}" data-part="part_three">
        <div class="bg-white rounded-2xl p-6">
            <div class="">
                <h2 class="text-base font-semibold text-gray-900 mb-2">Part 3</h2>
                @if(!empty($part_three))

                    @if($part_three['resultData'])
                        <div class="space-y-6 mb-3">

                            <div class="rounded-2xl sm:rounded-3xl bg-white border border-gray-200 p-6">

                                <!-- Score -->
                                <div class="flex items-baseline gap-3 mb-4">
                                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                                        {{$part_three['resultData']['score']}}
                                    </p>
                                    <p class="text-lg font-semibold text-gray-500">
                                        / {{$part_three['resultData']['max_score']}}
                                    </p>
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                    {{$part_three['resultData']['description']}}
                                </p>

                            </div>

                        </div>

                    @endif

                    {{-- FOR / AGAINST details --}}
                    @if(isset($part_three['forAgainstData']) && (!empty($part_three['forAgainstData']['forDetails']) || !empty($part_three['forAgainstData']['againtsDetails'])))
                        <div class="rounded-2xl bg-[#fafafa] p-4 sm:p-6 mb-6">
                            @if(!empty($part_three['theme']))
                                <p class="text-sm text-gray-500 mb-6">
                                    <span class="font-semibold text-gray-900">{{ $part_three['theme'] }}</span>
                                </p>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if(!empty($part_three['forAgainstData']['forDetails']))
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 mb-2">FOR</p>
                                        {!! $part_three['forAgainstData']['forDetails'] !!}
                                    </div>
                                @endif

                                @if(!empty($part_three['forAgainstData']['againtsDetails']))
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 mb-2">AGAINST</p>
                                        {!! $part_three['forAgainstData']['againtsDetails'] !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <x-test.speaking-question-component
                            :key="''"
                            :question="''"
                            :answerText="$part_three['answer_text']"
                            :audioLink="$part_three['audio_link'] ?? null"
                        />
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
        (() => {
            const tabButtons = document.querySelectorAll('.speaking-subpart-tab-button');
            const tabContents = document.querySelectorAll('.speaking-part-content');

            if (!tabButtons.length || !tabContents.length) return;

            const setActive = (activeKey) => {
                tabButtons.forEach(btn => {
                    const isActive = btn.getAttribute('data-part') === activeKey;

                    if (isActive) {
                        btn.classList.add('bg-white', 'text-orange-600', 'shadow-sm');
                        btn.classList.remove('text-gray-500');
                    } else {
                        btn.classList.remove('bg-white', 'text-orange-600', 'shadow-sm');
                        btn.classList.add('text-gray-500');
                    }
                });

                tabContents.forEach(content => {
                    const isActive = content.getAttribute('data-part') === activeKey;
                    content.classList.toggle('hidden', !isActive);
                });
            };

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const key = button.getAttribute('data-part');
                    setActive(key);
                });
            });

            // initial state (serverdan kelgan active bo‘yicha)
            const initiallyActive = document.querySelector('.speaking-subpart-tab-button.bg-white[data-part]')?.getAttribute('data-part')
                || tabButtons[0].getAttribute('data-part');

            setActive(initiallyActive);
        })();
    </script>
@endsection
