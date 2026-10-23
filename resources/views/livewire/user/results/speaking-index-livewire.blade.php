<div class="space-y-4">
    @livewire('user.filter-livewire', ['section' => 'speaking'])

    <div class="bg-white rounded-2xl p-2">
        <div
            class="hidden sm:grid sm:grid-cols-12 gap-4 px-4 py-3 text-md font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-1 text-center">
                <input type="checkbox"
                       class="w-4 h-4 cursor-pointer accent-[#ea580c] border-gray-300"
                       wire:model.live="selectAll">
            </div>
            <div class="col-span-3">Test name</div>
            <div class="col-span-2 text-center">Percent</div>
            <div class="col-span-2 text-center">Score</div>
            <div class="col-span-2 text-center">Max Score</div>
            <div class="col-span-2 text-right">Action</div>
        </div>
    </div>

    @foreach ($results as $result)
        <div class="bg-white rounded-2xl">
            <div class="grid sm:grid-cols-12 gap-4 px-4 sm:px-6 py-4 items-center">
                <div class="col-span-1 text-center">
                    <input type="checkbox"
                           wire:key="chk-{{ $result->id }}"
                           value="{{ (string) $result->id }}"
                           wire:model.live="selected"
                           class="w-4 h-4 cursor-pointer accent-[#ea580c] border-gray-300">
                </div>

                <div class="col-span-12 sm:col-span-3">
                    <div class="text-md font-semibold text-gray-900 space-y-1">
                        <p>{{ $result->exercise->title ?? '' }} </p>
                        <p class="text-xs text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($result->created_at)->locale('uz')->translatedFormat('j F H:i') }}
                        </p>
                        <p class="text-[#ea580c] text-sm">
                            {{ \App\Enums\CefrTypeEnum::PART_CATEGORIES[ \App\Enums\CefrTypeEnum::SPEAKING->value][$result->category_type] }}
                        </p>
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-2">
                    <div class="flex items-center sm:justify-center gap-2">
                        <span class="text-md font-medium text-gray-500 sm:hidden">Band score:</span>
                        <div class="relative w-[68px] h-[68px] shrink-0">
                            <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                                <circle cx="60" cy="60" r="38" class="fill-none stroke-gray-200"
                                        stroke-width="9" />

                                <circle cx="60" cy="60" r="38" class="fill-none stroke-orange-500"
                                        stroke-width="9" stroke-linecap="round"
                                        stroke-dasharray="{{ round(2 * pi() * 38) }}"
                                        stroke-dashoffset="{{ round(2 * pi() * 38 - (2 * pi() * 38 * $result->percentageData['percent']) / 100) }}" />
                            </svg>

                            <!-- center text -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-sm font-extrabold text-gray-900">
                                    {{ $result->percentageData['percent'] }}%
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-span-3 sm:col-span-2">
                    <div class="flex items-center sm:justify-center gap-2">
                        <span class="text-xs font-medium text-gray-500 sm:hidden">Correct:</span>
                        <span class="text-lg font-semibold text-green-600">{{ $result->percentageData['ball']  }}
</span>
                    </div>
                </div>
                <div class="col-span-3 sm:col-span-2">
                    <div class="flex items-center sm:justify-center gap-2">
                        <span class="text-xs font-medium text-gray-500 sm:hidden">Score:</span>
                        <span class="text-lg font-medium text-gray-700">{{$result->max_score}}</span>
                    </div>
                </div>



                <div class="col-span-12 sm:col-span-2 sm:text-right">
                    <a href="{{route('user.results.show',['id'=>$result->id,'type'=>\App\Enums\CefrTypeEnum::SPEAKING->value ])}}"
                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5
                              bg-[#ea580c] text-white text-sm font-medium rounded-xl
                              hover:bg-orange-600 transition-colors duration-200 shadow-sm hover:shadow">
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
