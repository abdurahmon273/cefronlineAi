<div class="space-y-4">
    @livewire('user.filter-livewire', ['section' => 'full'])

    {{-- Table header --}}
    <div class="bg-white rounded-2xl p-2 hidden sm:block">
        <div class="grid grid-cols-12 gap-4 px-4 py-3 text-md font-semibold text-gray-500 uppercase tracking-wider items-center">

            <div class="col-span-1 text-center">
                <input type="checkbox"
                       class="w-4 h-4 cursor-pointer accent-[#ea580c] border-gray-300"
                       wire:model.live="selectAll">
            </div>

            <div class="col-span-3">Test name</div>

            <div class="col-span-6">
                <div class="grid items-center text-center font-semibold text-gray-500 gap-2 [grid-template-columns:0.9fr_1fr_1fr_1fr_1fr]">
                    <div class="whitespace-nowrap">Score</div>

                    <div class="whitespace-nowrap">
                        <span class="min-[1300px]:hidden">Lis</span>
                        <span class="hidden min-[1300px]:inline min-[1558px]:hidden">Lis</span>
                        <span class="hidden min-[1558px]:inline">Listening</span>
                    </div>

                    <div class="whitespace-nowrap">
                        <span class="min-[1300px]:hidden">Read</span>
                        <span class="hidden min-[1300px]:inline min-[1558px]:hidden">Read</span>
                        <span class="hidden min-[1558px]:inline">Reading</span>
                    </div>

                    <div class="whitespace-nowrap">
                        <span class="min-[1300px]:hidden">Wri</span>
                        <span class="hidden min-[1300px]:inline min-[1558px]:hidden">Wri</span>
                        <span class="hidden min-[1558px]:inline">Writing</span>
                    </div>

                    <div class="whitespace-nowrap">
                        <span class="min-[1300px]:hidden">Spe</span>
                        <span class="hidden min-[1300px]:inline min-[1558px]:hidden">Spe</span>
                        <span class="hidden min-[1558px]:inline">Speaking</span>
                    </div>
                </div>
            </div>

            <div class="col-span-2 text-right">Action</div>

        </div>
    </div>

    @foreach ($results as $result)
        <div class="bg-white rounded-2xl shadow-sm">
            <div class="grid grid-cols-12 gap-4 px-4 sm:px-6 py-4 items-center">

                {{-- Checkbox --}}
                <div class="col-span-12 sm:col-span-1 flex sm:justify-center">
                    <input
                        type="checkbox"
                        wire:key="chk-{{ $result->id }}"
                        value="{{ (string) $result->id }}"
                        wire:model.live="selected"
                        class="w-4 h-4 cursor-pointer accent-[#ea580c] border-gray-300">
                </div>

                {{-- Test name --}}
                <div class="col-span-12 sm:col-span-3">
                    <div class="text-md font-semibold text-gray-900 space-y-1">
                        <p>{{ $result->exercise->title ?? '' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($result->created_at)->locale('uz')->translatedFormat('j F H:i') }}
                        </p>
                        <p class="text-[#ea580c] text-sm"></p>
                    </div>
                </div>

                {{-- Scores --}}
                <div class="col-span-12 sm:col-span-6">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5 sm:gap-2 items-center">

                        {{-- Degree --}}
                        <div class="rounded-xl bg-orange-50 px-3 py-3 text-center">
                            <div class="text-xs font-medium text-gray-500 sm:hidden mb-1">Ball</div>
                            <div class="text-xl sm:text-2xl font-bold text-[#ea580c]">
                                {{$result->overall}}
                            </div>
                        </div>

                        {{-- Listening --}}
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center">
                            <div class="text-xs font-medium text-gray-500 sm:hidden mb-1">Listening</div>
                            <div class="text-base sm:text-lg font-semibold text-gray-700">
                                {{$result->listening_ball ?? '-'}}
                            </div>
                        </div>

                        {{-- Reading --}}
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center">
                            <div class="text-xs font-medium text-gray-500 sm:hidden mb-1">Reading</div>
                            <div class="text-base sm:text-lg font-semibold text-gray-700">
                                {{$result->reading_ball ?? '-'}}
                            </div>
                        </div>

                        {{-- Writing --}}
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center">
                            <div class="text-xs font-medium text-gray-500 sm:hidden mb-1">Writing</div>
                            <div class="text-base sm:text-lg font-semibold text-gray-700">
                                {{$result->writing_ball ?? '-'}}
                            </div>
                        </div>

                        {{-- Speaking --}}
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-center">
                            <div class="text-xs font-medium text-gray-500 sm:hidden mb-1">Speaking</div>
                            <div class="text-base sm:text-lg font-semibold text-gray-700">
                                {{$result->speaking_ball ?? '-'}}
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action --}}
                <div class="col-span-12 sm:col-span-2 sm:text-right">
                    <a href="{{ route('user.full.results.show',['id'=>$result->id]) }}"
                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 bg-[#ea580c] text-white text-sm font-medium rounded-xl hover:bg-orange-600 transition-colors duration-200 shadow-sm hover:shadow">
                        View
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="2"
                             stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.5 6l6 6m0 0l-6 6m6-6H3"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    @endforeach

    <div class="mt-6">
        {{ $results->links('components.pagination') }}
    </div>
</div>
