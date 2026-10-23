@extends('layouts.user')

@section('content')

    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    @livewire('user.results.show-writing-livewire',['data'=>$data])

                </div>
                @include('components.membership-card')
            </div>
        </div>
    </div>
@endsection

