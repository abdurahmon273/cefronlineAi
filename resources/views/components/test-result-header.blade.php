<div class="mb-6 flex items-center gap-4">
    @if(isset($mockType))
        @if($mockType)
            @if(!isset($mockShow))
                <a href="{{route('user.results')}}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                    <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ asset("assets/arrow-left.svg") }}" alt="arrow left">
                </a>
            @elseif($mockShow) a
                <a href="{{route('user.results')}}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                    <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ asset("assets/arrow-left.svg") }}" alt="arrow left">
                </a>
            @else b
                <a href="{{route('user.results')}}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                    <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ asset("assets/arrow-left.svg") }}" alt="arrow left">
                </a>
            @endif

        @endif
    @endif

    <div>
        @if(isset($mockType))
            @if($mockType)
        <p class="text-sm text-gray-500">Results > <span class="text-orange-500 font-medium">{{ $type }}</span></p>
           @endif
        @endif
        <h1 class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">{{ $title }}</h1>
    </div>
</div>
