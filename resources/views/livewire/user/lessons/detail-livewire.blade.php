<div class="lg:col-span-9">
    @php
        $asset = fn($p) => asset("assets/{$p}");
    @endphp
    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
        {{-- Back Button and Breadcrumb --}}
        <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
            <a href="{{ route('user.lessons') }}"
                class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ $asset('arrow-left.svg') }}"
                    alt="arrow left">
            </a>
            <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-slate-600 overflow-x-auto">
                <a href="{{ route('user.lessons') }}"
                    class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900 whitespace-nowrap">Course</a>
                <span
                    class="py-[2px] px-[4px] w-[16px] h-[16px] sm:w-[20px] sm:h-[20px] flex items-center justify-center bg-white rounded-[6px] flex-shrink-0">
                    <img class="h-[10px] w-[10px] sm:h-[12px] sm:w-[12px]" src="{{ $asset('arrow-right.svg') }}"
                        alt="arrow right">
                </span>
                <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px] whitespace-nowrap">
                    {{ $lessons->first()->course->name ?? 'No Course' }}
                </span>
            </div>
        </div>

        {{-- Title --}}
        <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">
            {{ $lessons->first()->course->name ?? 'No Course' }}
        </div>

        {{-- Video Grid --}}
        <div class="grid gap-4 sm:grid-cols-2">
            {{-- Top Row - 2 videos --}}
            @foreach ($lessons as $lesson)
                <a href="{{ route('user.lesson', ['id' => $lesson->id]) }}"
                    class="relative w-full h-[193px] rounded-[24px] bg-slate-100 cursor-pointer transition-all">
                    <img src="{{ $lesson->files[0]->original_url ?? '' }}"
                        class="absolute inset-0 w-full h-full object-cover rounded-[24px] transition-all"
                        alt="">
                    <div
                        class="absolute inset-0 flex items-center justify-center z-10 opacity-0 hover:opacity-100 hover:backdrop-blur rounded-[24px] transition-opacity">
                        <span
                            class="bg-black bg-opacity-50 px-2 py-1 rounded text-white">{{ $lesson->name ?? 'No Video' }} </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
