@extends('layouts.user')

@section('content')
    @php
        $asset = fn($p) => asset("assets/{$p}");
        $assetsIcon = fn($p) => asset("assets/{$p}");
    @endphp

    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                {{-- Practice Content --}}
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
                                <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px] whitespace-nowrap">{{\App\Enums\CefrTypeEnum::CEFR_TYPE_NAMES[$type]}}</span>
                            </div>
                        </div>
{{--                         <div class="mb-4 mt-4 p-4 sm:p-5 bg-orange-100 rounded-2xl group-hover:bg-orange-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers-icon lucide-layers"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/></svg>
                                    </div> --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6">
                            @foreach($parts as $key => $partName)
                                @php
                                    // $key 3 bo‘lsa (sizda full/mock shu bo‘lsa) yoki nomiga qarab tekshiring
                                    $isFull = in_array(strtolower($partName), ['full', 'mock test', 'full mock', 'full test']) || $key == 3;
                                @endphp

                                <a href="{{ route('user.practice-tests', ['type' => $type, 'part' => $key]) }}"
                                   class="group flex flex-col items-center p-6 sm:p-8 bg-white rounded-2xl border border-gray-100 hover:border-orange-300 hover:shadow-lg transition-all duration-300
                                         {{ ($isFull && count($parts)==3) ? 'sm:col-span-2' : '' }}">
                                    <div class="mb-4 mt-4 p-4 sm:p-5 bg-orange-100 rounded-2xl group-hover:bg-orange-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers-icon lucide-layers"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/></svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 text-center">{{ $partName }}</h3>
                                </a>
                            @endforeach
                        </div>

                    </div>
                </div>
                @include('components.membership-card')
            </div>
        </div>
    </div>
@endsection
