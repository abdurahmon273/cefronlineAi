
@extends('layouts.user')

@section('content')
    @php
        $asset = fn ($p) => asset("assets/{$p}");
        $assetsIcon = fn ($p) => asset("assets/{$p}");
        $hasPremium =auth()->user() ?  auth()->user()->activePremium() : false;
    @endphp


    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                {{-- Practice Test Content --}}
                <div class="lg:col-span-9">
                    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                        {{-- Back Button and Breadcrumb --}}
                        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                            <a href="{{ route('user.practices') }}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
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
                                <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px] whitespace-nowrap">{{\App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type]}}</span>
                            </div>
                        </div>

                        {{-- Practice Session Cards --}}
                        <div class="space-y-4">

                            @foreach($exercises as $exercise)
                                <div class="rounded-[20px] bg-white p-[20px]" data-session-card>
                                    <button class="w-full flex items-center justify-between" data-session-toggle>
                                        <div class="flex items-center gap-4">
                                            <div class="shadow-md flex h-[53px] w-[53px] items-center justify-center rounded-[16px] bg-white">
                                                <img class="h-[24px] w-[24px]" src="{{ $assetsIcon('headphone.svg') }}" alt="Listening">
                                            </div>
                                            <div>
                                                <div class="text-[16px] font-extrabold text-slate-900 text-start">{{$exercise->name}}</div>
                                                <div class="text-[14px] text-slate-500 text-start">{{$exercise->{\App\Enums\CefrTypeEnum::CEFR_EXERCISES[$type]}->count()}} Practice Sessions</div>
                                            </div>
                                        </div>
                                        <div class="py-[2px] px-[4px] w-[53px] h-[53px] flex items-center justify-center bg-white rounded-[16px] shadow-lg">
                                            <img class="h-[24px] w-[24px]" src="{{ $asset('arrow-down.svg') }}" alt="arrow right">
                                        </div>
                                    </button>
                                    <div data-session-content class="hidden pl-16 space-y-3 mt-4">
                                        @foreach($exercise->{\App\Enums\CefrTypeEnum::CEFR_EXERCISES[$type]} as  $cefrExercise)
                                            <div class="flex items-center justify-between">
                                                <a href="{{ route('user.practice-test', ['id' => $cefrExercise->id, 'type' => $type,'difficulty'=>$cefrExercise->difficulty ?? 0 ,'time_limit'=>$cefrExercise->time_limit?? 0 ,'part_category'=>$cefrExercise->part_category??0 , 'title'=>$cefrExercise->title?$cefrExercise->title : $cefrExercise->name ,'status'=>$cefrExercise->status ]) }}" class="group flex items-center justify-between gap-3 rounded-md px-3 py-2 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 cursor-pointer">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-900 flex-shrink-0"></span>

                                                        <span class="text-sm text-slate-900 truncate group-hover:underline">
                                                             {{ $cefrExercise->{\App\Enums\CefrTypeEnum::CEFR_COLUMN_NAMES[$type]} }}
                                                        </span>
                                                    </div>

                                                    <svg xmlns="http://www.w3.org/2000/svg"  class="h-4 w-5 flex-shrink-0 text-slate-400 transition-all duration-200 ease-out group-hover:w-7 group-hover:translate-x-1 group-hover:text-slate-700"  fill="none"  viewBox="0 0 24 24"  stroke="currentColor"  aria-hidden="true" >
                                                        <path  stroke-linecap="round"  stroke-linejoin="round"  stroke-width="2"  d="M5 12h14m-5-5l5 5-5 5" />
                                                    </svg>
                                                </a>


                                            @if($cefrExercise->status && (!$hasPremium))
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#f1f1f1]">
                                                        <img class="h-4 w-4" src="{{ $asset('crown.svg') }}" alt="Premium">
                                                    </div>
                                                @else
{{--                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#f1f1f1]">--}}
{{--                                                        <span class="text-xs font-medium text-slate-900">0%</span>--}}
{{--                                                    </div>--}}
                                                @endif


                                            </div>

                                        @endforeach
                                        {{-- Content would go here when expanded --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            @include('components.pagination-tests', ['exercises' => $exercises])
                        </div>
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

            // Practice Session Toggle
            document.querySelectorAll('[data-session-toggle]').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const card = toggle.closest('[data-session-card]');
                    const content = card.querySelector('[data-session-content]');
                    const chevron = toggle.querySelector('[data-chevron]');

                    if (content) {
                        const isHidden = content.classList.contains('hidden');
                        if (isHidden) {
                            content.classList.remove('hidden');
                            chevron.classList.add('rotate-180');
                        } else {
                            content.classList.add('hidden');
                            chevron.classList.remove('rotate-180');
                        }
                    }
                });
            });
        })();
    </script>
@endsection
