@php
    $asset = fn ($p) => asset("{$p}");
@endphp

<div class="hidden lg:flex items-center gap-2"
     x-data="{ pulse: false }"
     x-on:balance-updated.window="pulse = true; setTimeout(() => pulse = false, 700)">
    @if($balanceData['success'])
        <a href="{{route('user.market',['type'=>1])}}"
           :class="pulse ? 'scale-105 ring-2 ring-[#FD4D00] border-[#FD4D00]' : 'ring-2 ring-transparent'"
           class="highlight-credit-element rounded-[16px] bg-slate-100 px-4 py-2 text-md font-regular  {{$balanceData['expired'] ? 'text-red-600 border border-red-200' : 'text-slate-800' }}  flex items-center gap-1 transition-all duration-300 ease-out border">
            <span class="whitespace-nowrap">
                <span class="font-regular ">{{$balanceData['balance']}}</span>
                <span class="text-md font-medium {{$balanceData['expired'] ? '' : 'text-slate-400' }} ">UZS</span>
            </span>
        </a>
    @endif

    @if ($planName)
        <a href="{{route('user.market',['type'=>0])}}"
           :class="pulse ? 'scale-105 ring-2 ring-[#FD4D00] border-[#FD4D00]' : 'ring-2 ring-transparent'"
           class="rounded-[16px] bg-slate-100 px-4 py-2 text-md font-regular text-slate-800 flex items-center gap-2 transition-all duration-300 ease-out border border-transparent">
            <img src="{{ $asset('assets/crown.svg') }}" alt="Plan" class="h-4 w-4">
            <span class="whitespace-nowrap">{{ $planName }}</span>
        </a>
    @endif
</div>
