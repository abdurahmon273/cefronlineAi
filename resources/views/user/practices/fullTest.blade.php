@extends('layouts.user')

@section('content')

    @php
        $asset = fn($p) => asset("assets/{$p}");
    @endphp

    {{-- Main --}}
    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">

                {{-- Practice Test Content --}}
                <div class="lg:col-span-9">
                    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">

                        {{-- Back Button and Breadcrumb --}}
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                            <a href="{{ route('user.practice-tests',$type) }}"
                               class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                                <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ $asset('arrow-left.svg') }}" alt="arrow left">
                            </a>

                            <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-slate-600 overflow-x-auto">
                                <a href="{{ route('user.practices') }}"
                                   class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">
                                    Practice
                                </a>

                                <span class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                                    <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                                </span>

                                <a href="{{ route('user.practice-tests',['type' => $type, 'part' => 0]) }}"
                                   class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">
                                    {{ \App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type] }}
                                </a>

                                <span class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                                    <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                                </span>

                                <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px] whitespace-nowrap">
                                    {{ $title ?? 'Full Mock 1' }}
                                </span>
                            </div>
                        </div>

                        {{-- Grid of 4 Cards --}}
                        <div class="grid gap-4 sm:gap-6 md:grid-cols-2">
                            @foreach($exerciseData as $data)
                                <div class="group bg-white rounded-2xl sm:rounded-[28px] p-4 sm:p-6 border border-slate-100 hover:border-orange-100 hover:shadow-sm transition-all relative overflow-hidden">

                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl bg-orange-50 flex items-center justify-center">
                                                <img class="h-10 w-10 sm:h-12 sm:w-12" src="{{ $asset($data['image']['asset']) }}" alt="{{ $data['image']['alt'] ?? $data['title'] }}">
                                            </div>
                                            <div>
                                                <h3 class="text-base sm:text-lg font-semibold text-slate-900">
                                                    {{ $data['title'] }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-[11px] font-semibold text-slate-500">Difficulty</div>
                                            <div class="text-sm font-medium text-slate-900 truncate">{{ $data['difficulty'] }}</div>
                                        </div>
                                        <div class="h-8 w-px bg-slate-100"></div>
                                        <div class="min-w-0 text-right">
                                            <div class="text-[11px] font-semibold text-slate-500">Time Limit</div>
                                            <div class="text-sm font-medium text-slate-900 truncate">{{ $data['time_limit'] }} minutes</div>
                                        </div>
                                    </div>

                                    @if($data['buttonStatus'])
                                        <div class="mt-5">
                                            <div
                                                class="inline-flex w-full justify-center items-center gap-2 rounded-[12px]
                                                       bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-500
                                                       border border-slate-200 cursor-not-allowed select-none">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 6L9 17l-5-5" />
                                                </svg>
                                                Finished
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-5">
                                            <a href="{{ $data['buttonLink'] }}"
                                               class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                                Start
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14M13 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endif

                                    <div class="absolute right-[-22px] bottom-[-22px] opacity-10 group-hover:opacity-15 transition-opacity pointer-events-none">
                                        <img class="w-32 h-auto" src="{{ $asset($data['image']['asset']) }}" alt="">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Reset Exercise (design only, no backend) --}}
                        @if($lastResult && (!$lastResult->status))
                        <div class="mt-4 sm:mt-6">
                            <div class="bg-white rounded-2xl sm:rounded-[28px] border border-slate-100 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                    <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-[#FD4D00]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9a7 7 0 00-12.6-3M5 15a7 7 0 0012.6 3" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm sm:text-base font-semibold text-slate-900">Reset Exam</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Barcha 4 ta bo'limni boshidan qayta ishlang</div>
                                    </div>
                                </div>

                                <a href="{{route('user.full.reset',['id'=>$id])}}" type="button"
                                        id="resetExerciseBtn"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors whitespace-nowrap flex-shrink-0">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9a7 7 0 00-12.6-3M5 15a7 7 0 0012.6 3" />
                                    </svg>
                                    Reset Exam
                                </a>
                            </div>
                        </div>
                        @endif
                        {{-- Last Result --}}
                        @if($lastResult)
                            <div class="mt-6">
                                <div class="bg-white rounded-2xl sm:rounded-[28px] border border-slate-100 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-orange-50 flex items-center justify-center">
                                            @if($lastResult->status)
                                                <svg class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6 text-[#FD4D00]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="text-sm sm:text-base font-semibold text-slate-900">Last Result</div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                {{ optional($lastResult->created_at)->locale('uz')->translatedFormat('j F H:i') }}
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('user.full.results.show', ['id' => $lastResult->id,'type'=>1]) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-[12px] bg-orange-50 px-5 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-orange-100 transition-colors">
                                        View Result
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M13 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>

                                {{-- Show more (only if has lastResults) --}}
                                @if(isset($lastResults) && $lastResults->count() > 0)
                                    <div class="mt-3">
                                        <button type="button"
                                                id="toggleMoreResultsBtn"
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-[14px] bg-white border border-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                            Show more
                                            <svg id="toggleMoreResultsIcon" class="h-5 w-5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M6 9l6 6 6-6" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="moreResultsWrap" class="mt-3 space-y-3 hidden">
                                        @foreach($lastResults as $r)
                                            <div class="more-result-item overflow-hidden max-h-0 opacity-0 translate-y-1 transition-all duration-300 ease-out">
                                                <div class="bg-white rounded-2xl sm:rounded-[28px] border border-slate-100 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                                    <div class="flex items-center gap-4">
                                                        <div class="h-12 w-12 rounded-2xl bg-orange-50 flex items-center justify-center">
                                                            @if($r->status)
                                                                <svg class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            @else
                                                                <svg class="h-6 w-6 text-[#FD4D00]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <circle cx="12" cy="12" r="9" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                                                                </svg>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <div class="text-sm sm:text-base font-semibold text-slate-900">Previous Result</div>
                                                            <div class="text-xs text-slate-500 mt-1">
                                                                {{ optional($r->created_at)->locale('uz')->translatedFormat('j F H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <a href="{{ route('user.full.results.show', ['id' => $r->id,'type'=>1]) }}"
                                                       class="inline-flex items-center justify-center gap-2 rounded-[12px] bg-orange-50 px-5 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-orange-100 transition-colors">
                                                        View Result
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M5 12h14M13 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Right Sidebar - Membership Card --}}
                @include('components.membership-card')

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (() => {
            const btn = document.getElementById('toggleMoreResultsBtn');
            const wrap = document.getElementById('moreResultsWrap');
            const icon = document.getElementById('toggleMoreResultsIcon');

            if (!btn || !wrap) return;

            let opened = false;

            const open = () => {
                wrap.classList.remove('hidden');
                const items = wrap.querySelectorAll('.more-result-item');

                // trigger layout
                requestAnimationFrame(() => {
                    items.forEach((el, i) => {
                        const h = el.scrollHeight; // element inner content height
                        // staggered simple effect
                        setTimeout(() => {
                            el.style.maxHeight = h + 'px';
                            el.style.opacity = '1';
                            el.style.transform = 'translateY(0)';
                        }, i * 60);
                    });
                });

                btn.firstChild.textContent = ' Hide';
                if (icon) icon.style.transform = 'rotate(180deg)';
                opened = true;
            };

            const close = () => {
                const items = wrap.querySelectorAll('.more-result-item');

                items.forEach((el, i) => {
                    setTimeout(() => {
                        el.style.maxHeight = '0px';
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(4px)';
                    }, i * 40);
                });

                // after animation hide wrapper
                const totalDelay = items.length * 40 + 320;
                setTimeout(() => {
                    wrap.classList.add('hidden');
                }, totalDelay);

                btn.firstChild.textContent = ' Show more';
                if (icon) icon.style.transform = 'rotate(0deg)';
                opened = false;
            };

            btn.addEventListener('click', () => {
                opened ? close() : open();
            });
        })();
    </script>
@endsection
