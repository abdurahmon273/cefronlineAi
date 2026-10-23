@extends('layouts.user')

@section('content')
    @php
        $asset = fn($p) => asset("assets/{$p}");
    @endphp


    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                {{-- Lesson Detail Content --}}
                @livewire('user.lessons.detail-livewire', compact('id'))

                {{-- Right Sidebar - Membership Card --}}
                @include('components.membership-card')

            </div>
        </div>
    </div>

@endsection
