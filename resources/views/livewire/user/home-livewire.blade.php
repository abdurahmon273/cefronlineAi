<div class="space-y-4 sm:space-y-6">
    <style>
        .home-layout {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .home-layout { gap: 1.5rem; }
        }
        @media (min-width: 1536px) {
            .home-layout {
                flex-direction: row;
                align-items: flex-start;
            }
            .home-main {
                flex: 1 1 auto;
                min-width: 0;
            }
            .home-aside {
                flex: 0 0 clamp(280px, 22vw, 380px);
                width: clamp(280px, 22vw, 380px);
            }
        }
    </style>
    @php
        $asset = fn($p) => asset("assets/{$p}");
        $assetsIcon = fn($p) => asset("assets/{$p}");
    @endphp
    <div class="relative w-full">
        <div class="home-layout">
                <div class="home-main w-full space-y-4 sm:space-y-6">
                    @if($news->isNotEmpty())
                    <div class="relative overflow-hidden" x-data="{
                        currentSlide: 0,
                        slideCount: {{ $slideCount }},
                        step: 100,
                        showModal: false,
                        showNewsModal: false,
                        selectedNews: {}
                    }" x-init="() => {
                        const t = setInterval(() => { currentSlide = (currentSlide + 1) % slideCount }, 4500);
                        $el._carouselInterval = t;
                        window.addEventListener('keydown', e => {
                            if (e.key === 'ArrowLeft') currentSlide = (currentSlide - 1 + slideCount) % slideCount;
                            if (e.key === 'ArrowRight') currentSlide = (currentSlide + 1) % slideCount;
                        });
                    }"
                        x-on:destroy.window="clearInterval($el._carouselInterval)">
                        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-sm">
                            <div class="flex w-[{{ $slideCount * 100 }}%] transition-transform duration-500 ease-out"
                                data-carousel-track :style="`transform: translateX(-${currentSlide * (100 / slideCount)}%)`">
                                @foreach ($news as $new)
                                    <div class="cursor-pointer shrink-0" style="width: {{ 100 / $slideCount }}%"
                                        @click="selectedNews = {{ Js::from($new) }}; showNewsModal = true">
                                        <div class="relative h-[200px] sm:h-[200px] lg:h-[200px] rounded-3xl overflow-hidden">
                                            <img src="{{ $new->web_image ? $new->web_image->original_url : '' }}"
                                                class="absolute inset-0 w-full h-full object-cover" alt="">
                                            <div class="absolute inset-0 bg-black/20 rounded-2xl sm:rounded-3xl"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- indicators --}}
                            <div class="pointer-events-auto absolute bottom-2 sm:bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 opacity-80"
                                x-cloak>
                                @for ($i = 0; $i < $slideCount; $i++)
                                    <button type="button" @click="currentSlide = {{ $i }}"
                                        :class="currentSlide === {{ $i }} ?
                                            'h-2 w-8 rounded-full bg-white/90 transition-all duration-200' :
                                            'h-2 w-4 rounded-full bg-white/30 transition-all duration-200'"
                                        aria-label="Go to slide {{ $i + 1 }}"></button>
                                @endfor
                            </div>
                        </div>

                        <div x-show="showNewsModal" style="display: none;"
                            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                            <div @click.away="showNewsModal = false"
                                class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-3xl shadow-2xl transform transition-all flex flex-col"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                                {{-- Close Button --}}
                                <button @click="showNewsModal = false"
                                    class="absolute top-4 right-4 z-10 p-2 bg-black/10 hover:bg-black/20 rounded-full text-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                {{-- Featured Image --}}
                                <div class="relative h-32 sm:h-48 lg:h-64 bg-gray-100 shrink-0">
                                    <img :src="selectedNews?.web_image?.url || selectedNews?.image?.url"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8">
                                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white leading-tight"
                                            x-text="selectedNews.title"></h3>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="p-4 sm:p-5 lg:p-8 space-y-4 sm:space-y-6">
                                    <div class="text-gray-600 text-base sm:text-lg leading-relaxed"
                                        x-html="selectedNews.description"></div>
                                    <template x-if="selectedNews.application_button">
                                        <div class="flex justify-end pt-4">
                                            <a :href="selectedNews.link" target="_blank"
                                                class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold px-4 py-2 sm:px-6 sm:py-3 rounded-xl transition-all shadow-lg hover:shadow-orange-600/30 transform hover:-translate-y-0.5">
                                                <span x-text="selectedNews.button_text"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                        {{-- CEFR Practice --}}
                        <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                            <div class="text-xl sm:text-2xl lg:text-3xl xl:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">
                                CEFR Practice
                            </div>

                            <div class="mt-5 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                                @foreach ($cards as $c)
                                    <a href="{{ route('user.practice-tests', $c['type']) }}"
                                       class="relative overflow-hidden rounded-[24px] bg-gradient-to-b {{ $c['grad'] }} p-5 text-white">
                                        <div class="absolute right-[-50px] bottom-[-50px] opacity-40">
                                            <img class="h-48 w-auto" src="{{ $asset($c['effect']) }}" alt="effect">
                                        </div>
                                        <div class="relative z-10">
                                            <div class="mb-4">
                                                <img class="h-[70px] drop-shadow-lg" src="{{ $assetsIcon($c['icon']) }}"
                                                     alt="{{ $c['t'] }}">
                                            </div>
                                            <div class="text-sm font-extrabold">{{ $c['t'] }}</div>
                                            <div class="mt-1 text-xs text-white/80">{{ $c['sub'] }} tests</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div
                                class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl sm:rounded-3xl bg-[#FFE9E0] p-4 sm:p-6 lg:p-[32px]">
                                <div class="w-full sm:w-auto">
                                    <div class="text-lg sm:text-xl lg:text-2xl xl:text-[32px] font-extrabold text-orange-600">Full
                                        mock</div>
                                    <div
                                        class="text-xs sm:text-sm lg:text-base xl:text-[16px] font-medium text-orange-500 mt-0.5 sm:mt-1">
                                        {{$mock_data['count']}} Mock Tests </div>
                                </div>
                                <a href="{{ route('user.practice-tests', App\Enums\CefrTypeEnum::FULL_MOCK) }}"
                                   class="inline-flex items-center gap-2 rounded-[12px] bg-orange-600 px-4 sm:px-[16px] py-2 sm:py-[8px] text-xs sm:text-sm font-normal text-white shadow-sm hover:bg-orange-700 active:bg-orange-800 w-full sm:w-auto justify-center touch-manipulation">
                                    Start Full Mock
                                    <img class="h-5 w-5 sm:h-6 sm:w-6" src="{{ $asset('arrow-right-02.svg') }}"
                                         alt="arrow right">
                                </a>
                            </div>
                        </div>

                        {{-- Learn via Video --}}
                        <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                                <div class="text-xl sm:text-2xl lg:text-3xl xl:text-[32px] font-extrabold text-slate-900">Learn via
                                    Video</div>
                                <a href="{{ route('user.lessons') }}"
                                   class="inline-flex items-center gap-2 rounded-[12px] bg-orange-600 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-xs font-semibold text-white shadow-sm whitespace-nowrap hover:bg-orange-700 active:bg-orange-800 touch-manipulation">
                                    Full video lessons
                                    <svg class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2">
                                        <path d="M5 12h14" />
                                        <path d="M13 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                            <div class="mt-4 sm:mt-5 grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
                                @foreach ($courses as $course)
                                    <a href="{{ route('user.lessons.detail', $course->id) }}"
                                       class="relative overflow-hidden rounded-[24px] text-white block">
                                        <img src="{{ $course->image->original_url ?? '' }}"
                                             class="absolute inset-0 w-full h-full object-cover" alt="">
                                        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                                        <div class="relative p-[24px]">
                                            <div class="max-w-xs whitespace-pre-line text-lg font-extrabold leading-tight">
                                                {{ $course->name }}</div>
                                            <div class="mt-3 flex items-center gap-2 text-xs text-white/80">
                                                <img class="h-6 w-6" src="{{ $asset('video.svg') }}" alt="videos">
                                                <span>{{ $course->lessons->count() }} Lessons</span>
                                            </div>
                                            <button
                                                class="mt-5 h-[40px] rounded-[14px] text-black bg-white px-[16px] py-[8px] text-[14px] font-normal hover:bg-gray-100">
                                                View lessons
                                            </button>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- My favorite videos --}}
                        <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                                <div class="text-xl sm:text-2xl lg:text-3xl xl:text-[32px] font-extrabold text-slate-900">My
                                    favorite videos</div>
                                <a href="{{ route('user.favorite') }}"
                                   class="inline-flex items-center gap-2 rounded-[12px] bg-orange-600 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-xs font-semibold text-white shadow-sm whitespace-nowrap hover:bg-orange-700 active:bg-orange-800 touch-manipulation">
                                    Full video lessons
                                    <svg class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2">
                                        <path d="M5 12h14" />
                                        <path d="M13 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                            <div
                                class="mt-4 sm:mt-5 w-full overflow-x-auto overflow-y-hidden scrollbar-hide overscroll-x-contain -mx-4 sm:-mx-6 px-4 sm:px-6">
                                <div class="flex gap-2.5 sm:gap-3 lg:gap-4 pb-1 overflow-visible">
                                    @forelse ($savedLessons as $lesson)
                                        <div
                                            class="w-[120px] sm:w-[200px] lg:w-[280px] xl:w-[300px] 2xl:w-[343px] h-[110px] sm:h-[135px] lg:h-[160px] xl:h-[180px] 2xl:h-[193px] rounded-xl sm:rounded-2xl lg:rounded-3xl bg-slate-100 flex items-center justify-center text-center text-[10px] sm:text-xs lg:text-sm xl:text-medium text-slate-400 flex-shrink-0">
                                            <a href="{{ route('user.lesson', ['id' => $lesson->video->lesson_id, 'video_id' => $lesson->video_id]) }}"
                                               class="relative w-full h-full rounded-[24px] bg-slate-100 cursor-pointer transition-all">
                                                <img src="{{ $lesson->video->lesson->files[0]->original_url ?? '' }}"
                                                     class="absolute inset-0 w-full h-full object-cover rounded-[24px] transition-all"
                                                     alt="">
                                                <div
                                                    class="absolute inset-0 flex items-center justify-center z-10 opacity-0 hover:opacity-100 hover:backdrop-blur rounded-[24px] transition-opacity">
                                        <span
                                            class="bg-black bg-opacity-50 px-2 py-1 rounded text-white text-[10px] sm:text-xs lg:text-sm">{{ $lesson->video->name ?? 'No Video' }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    @empty
                                        <div class="text-center text-slate-500 col-span-2">No favorite videos found.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                </div>
                @include('components.membership-card')
        </div>
    </div>


</div>
