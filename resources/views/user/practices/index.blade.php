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
                        <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">Practice</div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($cards as $c)
                                <a href="{{route('user.practice-tests', $c['type'])}}" class="relative overflow-hidden rounded-[24px] bg-gradient-to-b {{ $c['grad'] }} p-5 text-white">
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

                        <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl sm:rounded-3xl bg-[#FFE9E0] p-4 sm:p-6 lg:p-[32px]">
                            <div>
                                <div class="text-xl sm:text-2xl lg:text-[32px] font-extrabold text-orange-600">Full mock</div>
                                <div class="text-sm sm:text-base lg:text-[16px] font-medium text-orange-500">{{$mock_data['count']}} Mock Tests</div>
                            </div>
                            <a href="{{route('user.practice-tests',4)}}"
                                class="inline-flex items-center gap-2 rounded-[12px] bg-orange-600 px-4 sm:px-[16px] py-2 sm:py-[8px] text-sm font-normal text-white shadow-sm hover:bg-orange-700 w-full sm:w-auto justify-center">
                                Start Full Mock
                                <img class="h-[24px] w-[24px]" src="{{ $asset('arrow-right-02.svg') }}" alt="arrow right">
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Right Sidebar - Membership Card --}}
                @include('components.membership-card')
            </div>
        </div>
    </div>

@endsection
