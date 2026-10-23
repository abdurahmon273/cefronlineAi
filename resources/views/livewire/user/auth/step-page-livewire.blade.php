<div class="flex h-full w-[300%] transition-transform duration-500 ease-[cubic-bezier(.22,1,.36,1)] will-change-transform"
     data-auth-carousel-track
     style="transform: translate3d(-33.333%, 0, 0);">
    {{-- Slide 1 --}}
    @foreach($stepPages as $stepPage)
        <div class="flex w-1/3 shrink-0 items-center justify-center">
            <div class="mx-auto flex max-w-md flex-col items-center text-center">
                <img class="w-[320px] max-w-full select-none" src="{{ asset('storage/'.$stepPage->image) }}" alt="Video lessons">
                <div class="mt-6 text-3xl font-extrabold text-orange-600">{{$stepPage->title}}</div>
                <div class="mt-2 max-w-sm text-sm text-slate-500">
                    {{$stepPage->description}}
                </div>
            </div>
        </div>

    @endforeach

</div>
