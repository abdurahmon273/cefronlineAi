@extends('layouts.user')

@section('content')
    @php
        $asset = fn ($p) => asset("assets/{$p}");
    @endphp

            <div class="flex-1 w-full lg:w-auto">
                <div class="px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                        {{-- Lessons Content --}}
                        <div class="lg:col-span-9 rounded-2xl sm:rounded-[32px] bg-[#FaFaFa] p-4 sm:p-6 lg:p-[32px]">
                            <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">Courses</div>

                            {{-- Lesson Cards Grid --}}
                            @livewire('user.lessons.index-livewire')
                        </div>

                        {{-- Right Sidebar - Membership Card --}}
                        @include('components.membership-card')
                </div>
            </div>

@endsection

