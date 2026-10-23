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
                                <div class="flex justify-between gap-2 sm:gap-3 mb-2 sm:mb-2">
                                    <div class="flex items-center gap-2 sm:gap-3 mb-2 sm:mb-2">
                                        <a href="{{ route('user.practice-tests',$type) }}"
                                           class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                                            <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ $asset('arrow-left.svg') }}" alt="arrow left">
                                        </a>
                                        <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-slate-600 overflow-x-auto">
                                            <a href="{{ route('user.practices') }}" class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">Practice</a>
                                            <span class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                                            <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                                        </span>
                                            @if($type == \App\Enums\CefrTypeEnum::WRITING->value || $type == \App\Enums\CefrTypeEnum::SPEAKING->value)
                                                <a href="{{ route('user.practice-parts', $type) }}" class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">{{\App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type]}} Parts</a>
                                                <span class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                                                <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                                            </span>
                                            @endif
                                            <a href="{{ route('user.practice-tests',['type' => $type, 'part' => $part_category]) }}"
                                               class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">{{\App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type]}}</a>
                                            <span class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                                            <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                                        </span>
                                            <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px] whitespace-nowrap">{{\App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type]}}
                                                {{\App\Enums\CefrTypeEnum::PART_CATEGORIES[$type][$part_category]}}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="">
                                        @if($status)
                                        <div class="inline-flex h-[40px] w-[40px] items-center justify-center rounded-2xl">
                                            <img class="h-[32px] w-[32px]" src="/assets/crown.svg" alt="crown">
                                        </div>
                                        @endif

                                    </div>
                                </div>


                                {{-- Headphones Graphic --}}
                                <div class="flex items-start justify-center
                                            right-[-20px] bottom-[-20px]
                                            sm:right-[-30px] sm:bottom-[-30px]
                                            md:right-[-40px] md:bottom-[-40px]
                                            lg:right-[-60px] lg:bottom-[-60px]
                                            opacity-40">
                                    <img
                                        class="w-24 sm:w-32 md:w-40 lg:w-48 xl:w-64 h-auto"
                                        src="{{ $asset(\App\Enums\CefrTypeEnum::CEFR_IMAGE_DATA[$type]['asset']) }}"
                                        alt="{{\App\Enums\CefrTypeEnum::CEFR_IMAGE_DATA[$type]['alt']}}"
                                    >
                                </div>


                                {{-- Test Details --}}
                                <div class="space-y-4 mt-2 mb-6 sm:mb-8 p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-[32px] text-black bg-white">

                                    @if($title)
                                        <div class="flex items-center gap-3">
                                            <h3 class="font-semibold">{{$title}}</h3>
                                        </div>
                                    @endif


                                    {{-- Difficulty --}}
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100">
                                            <svg class="h-6 w-6 text-[#FD4D00]" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <rect x="7" y="8" width="10" height="2" />
                                                <rect x="7" y="12" width="8" height="2" />
                                                <rect x="7" y="16" width="6" height="2" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Difficulty of the test</div>
                                            <div class="text-sm text-slate-600">{{\App\Models\ListeningExercise::DIFFICULTIES[$difficulty]}}</div>
                                        </div>
                                    </div>

                                    {{-- Given time --}}
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100">
                                            <svg class="h-6 w-6 text-[#FD4D00]" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Time Limit</div>
                                            <div class="text-sm text-slate-600">{{$time_limit}} minutes</div>
                                        </div>
                                    </div>

                                        @if($data)
                                    <div class="flex justify-end gap-3">
                                        <button type="button"
                                            id="open-modal-answer"
                                            class="inline-flex items-center gap-2 rounded-xl bg-orange-50 px-6 py-3 text-sm font-semibold text-orange-600 hover:bg-orange-100 transition-all duration-200 shadow-sm border border-orange-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Model Answer
                                        </button>
                                    </div>
                                        @endif
                                </div>


                                {{-- Action Buttons --}}
                                <div class="flex items-center justify-between gap-4">
                                    @if($type == \App\Enums\CefrTypeEnum::WRITING->value || $type == \App\Enums\CefrTypeEnum::SPEAKING->value)
                                    <a href="{{ route('user.practice-tests',['type' => $type, 'part' => $part_category]) }}"
                                        class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-orange-50 px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-orange-100 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M19 12H5M12 19l-7-7 7-7" />
                                        </svg>
                                        Back
                                    </a>
                                    @else
                                        <a href="{{ route('user.practice-tests',['type' => $type]) }}"
                                           class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-orange-50 px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-orange-100 transition-colors">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2">
                                                <path d="M19 12H5M12 19l-7-7 7-7" />
                                            </svg>
                                            Back
                                        </a>
                                    @endif

                                    @if($type==0)
                                        <form id="startListeningForm" method="GET" action="{{ route('user.listening.start', $id) }}" class="w-full">
                                            <input type="hidden" name="exam_type" id="exam_type_hidden" value="real">

                                            <button type="button"
                                                    onclick="submitStartListening()"
                                                    class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                                Start
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14M13 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </form>
                                    @elseif($type==1)
                                        <a href="{{route('user.reading.start',$id)}}"
                                           class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                            Start
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2">
                                                <path d="M5 12h14M13 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @elseif($type==2)
                                        <a href="{{route('user.writing.start',$id)}}"
                                           class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                            Start
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2">
                                                <path d="M5 12h14M13 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @elseif($type==3)
                                        <a href="{{route('user.speaking.start',$id)}}"
                                           class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                            Start
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2">
                                                <path d="M5 12h14M13 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @elseif($type==4)
                                        <a href="{{route('user.reading.start',$id)}}"
                                           class="inline-flex w-full justify-center items-center gap-2 rounded-[12px] bg-[#FD4D00] px-6 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition-colors">
                                            Start
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="2">
                                                <path d="M5 12h14M13 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>

                        {{-- Right Sidebar - Membership Card --}}
                        @include('components.membership-card')
                    </div>
                </div>
            </div>

            <div id="modal-answer" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[100] transition-all duration-300">
        <div class="relative bg-white rounded-[32px] max-h-[85vh] max-w-3xl w-full mx-4 shadow-2xl border border-slate-100 overflow-hidden transform transition-all py-4 px-6 flex flex-col">
            <div class="px-8 pt-8 pb-4 flex items-center justify-between border-b border-slate-50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Model Answer</h3>
                </div>
                <button id="modal-answer-close" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8 relative flex-1 min-h-0">
                @if(count($data) >1 )
                <button id="modal-prev" class="absolute -left-5 sm:-left-5 top-[40%] -translate-y-1/2 w-10 h-10 rounded-full border border-slate-200 bg-white shadow-md flex items-center justify-center text-slate-400 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50 transition-all z-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="modal-next" class="absolute -right-5 sm:-right-5 top-[40%] -translate-y-1/2 w-10 h-10 rounded-full border border-slate-200 bg-white shadow-md flex items-center justify-center text-slate-400 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50 transition-all z-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                @endif
                <div class="relative overflow-hidden">
                    <div class="flex items-start transition-transform duration-500 ease-out" data-modal-carousel-track>
                        @foreach($data as $slide)
                            <div class="w-full flex-shrink-0">
                                <div class="bg-slate-50 rounded-2xl p-6 text-slate-700 text-[15px] max-h-[420px] overflow-y-auto custom-scrollbar leading-relaxed">
                                    @if(($slide['mode'] ?? 'text') === 'html')

                                        {!! $slide['html'] !!}
                                    @else
                                        @if($slide['html'])
                                            Question: <br>
                                            {!! nl2br(e($slide['html'] ?? '')) !!} <br> <br>
                                        @endif

                                    Example Text: <br> <br>

                                        {!! nl2br(e($slide['text'] ?? '')) !!}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 relative flex items-center justify-center shrink-0">
                    <div class="flex gap-1.5" data-modal-carousel-dots>
                        @if(count($data) >1 )
                        @for($i = 0; $i < count($data); $i++)
                            <button
                                data-modal-carousel-to="{{ $i }}"
                                class="w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-orange-500 w-4' : 'bg-slate-200' }}">
                            </button>
                        @endfor
                        @endif
                    </div>

                    <button id="modal-answer-continue" class="absolute right-0 px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-2xl shadow-lg shadow-orange-200 transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm sm:text-base">
                        Got it
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
        (() => {
            // Topnav profile dropdown (shared behavior)
            const topnavs = document.querySelectorAll('[data-topnav]');
            const closeAll = () => {
                topnavs.forEach((nav) => {
                    const menu = nav.querySelector('[data-topnav-menu]');
                    const profile = nav.querySelector('[data-topnav-profile]');
                    if (!menu || !profile) return;
                    menu.hidden = true;
                    profile.setAttribute('aria-expanded', 'false');
                    nav.setAttribute('data-open', 'false');
                });
            };
            topnavs.forEach((nav) => {
                const menu = nav.querySelector('[data-topnav-menu]');
                const profile = nav.querySelector('[data-topnav-profile]');
                if (!menu || !profile) return;
                const initiallyOpen = nav.getAttribute('data-open') === 'true';
                menu.hidden = !initiallyOpen;
                profile.setAttribute('aria-expanded', initiallyOpen ? 'true' : 'false');
                profile.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !menu.hidden;
                    closeAll();
                    menu.hidden = isOpen;
                    profile.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    nav.setAttribute('data-open', isOpen ? 'false' : 'true');
                });
            });
            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });

            // Notifications Dropdown
            const notificationModal = document.querySelector('[data-notification-modal]');
            const notificationTriggers = document.querySelectorAll('[data-notification-trigger]');
            const notificationClose = document.querySelector('[data-notification-close]');

            if (notificationModal) {
                const openModal = () => {
                    notificationModal.classList.remove('hidden');
                };

                const closeModal = () => {
                    notificationModal.classList.add('hidden');
                };

                notificationTriggers.forEach((trigger) => {
                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = !notificationModal.classList.contains('hidden');
                        if (isOpen) {
                            closeModal();
                        } else {
                            openModal();
                        }
                    });
                });

                // Close button
                if (notificationClose) {
                    notificationClose.addEventListener('click', (e) => {
                        e.stopPropagation();
                        closeModal();
                    });
                }

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (!notificationModal.contains(e.target) && !e.target.closest('[data-notification-trigger]')) {
                        closeModal();
                    }
                });

                // Close on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !notificationModal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            }
        })();
    </script>

    <script>
        function submitStartListening(btn) {
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            // radio tanlangan qiymatni olib hidden inputga yozamiz
            const selected = document.querySelector('input[name="exam_type"]:checked');
            const hidden = document.getElementById('exam_type_hidden');
            if (selected && hidden) hidden.value = selected.value;

            document.getElementById('startListeningForm')?.submit();
        }

        // modal/carousel behavior
        (() => {
            const modal = document.getElementById('modal-answer');
            const openButton = document.getElementById('open-modal-answer');
            const closeButton = document.getElementById('modal-answer-close');
            const continueButton = document.getElementById('modal-answer-continue');

            const getSlidesCount = () => modal ? modal.querySelectorAll('[data-modal-carousel-track] > *').length : 0;

            const track = modal?.querySelector('[data-modal-carousel-track]');
            const prevBtn = document.getElementById('modal-prev');
            const nextBtn = document.getElementById('modal-next');

            let index = 0;

            const setActive = (newIndex) => {
                const slidesCount = getSlidesCount();
                const dots = Array.from(modal?.querySelectorAll('[data-modal-carousel-to]') || []);
                if (!track || slidesCount <= 0) return;

                index = (newIndex + slidesCount) % slidesCount;
                track.style.transform = `translateX(${-index * 100}%)`;

                dots.forEach((d, i) => {
                    if (i === index) {
                        d.classList.add('bg-orange-500', 'w-4');
                        d.classList.remove('bg-slate-200');
                    } else {
                        d.classList.remove('bg-orange-500', 'w-4');
                        d.classList.add('bg-slate-200');
                    }
                });
            };

            openButton?.addEventListener('click', () => {
                modal.classList.remove('hidden');
                setTimeout(() => setActive(0), 10);
            });

            const hideModal = () => {
                modal.classList.add('hidden');
            };

            closeButton?.addEventListener('click', hideModal);
            continueButton?.addEventListener('click', hideModal);

            prevBtn?.addEventListener('click', () => setActive(index - 1));
            nextBtn?.addEventListener('click', () => setActive(index + 1));

            modal?.addEventListener('click', (e) => {
                const dot = e.target.closest('[data-modal-carousel-to]');
                if (dot) {
                    const to = Number(dot.getAttribute('data-modal-carousel-to'));
                    setActive(to);
                }

                if (e.target === modal) {
                    hideModal();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (modal && !modal.classList.contains('hidden')) {
                    if (e.key === 'ArrowLeft') setActive(index - 1);
                    if (e.key === 'ArrowRight') setActive(index + 1);
                    if (e.key === 'Escape') hideModal();
                }
            });
        })();
    </script>

@endsection
