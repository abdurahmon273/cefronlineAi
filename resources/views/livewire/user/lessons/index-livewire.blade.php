<div class="mt-5 grid gap-4 lg:grid-cols-2">
    @php
        $asset = fn ($p) => asset("assets/{$p}");
    @endphp
    @foreach ($courses as $course)
        <a href="{{ route('user.lessons.detail', $course->id) }}"
            class="relative overflow-hidden rounded-[24px] text-white block">
            <img src="{{ $course->image->original_url ?? '' }}" class="absolute inset-0 w-full h-full object-cover" alt="">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="relative p-[24px]">
                <div class="max-w-xs whitespace-pre-line text-lg font-extrabold leading-tight">{{ $course->name }}</div>
                <div class="mt-3 flex items-center gap-2 text-xs text-white/80">
                    <img class="h-6 w-6" src="{{ $asset('video.svg') }}" alt="videos">
                    <span>{{ $course->lessons->count() }} Lessons</span>
                </div>
                <button
                    class="mt-5 h-[40px] rounded-[14px] text-black bg-white px-[16px] py-[8px] text-[14px] font-normal hover:bg-gray-100">
                    View lessons
                </button>
            </div>
        </a>
    @endforeach
</div>
