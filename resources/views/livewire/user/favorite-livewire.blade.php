<div class="lg:col-span-9">
    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
        <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">Favorite videos</div>

        {{-- Video Grid --}}
        <div class="grid gap-4 sm:grid-cols-2">
            {{-- Top Row - 2 videos --}}
            @forelse ($savedLessons as $lesson)
                <a href="{{ route('user.lesson', ['id' => $lesson->video->lesson_id, 'video_id' => $lesson->video_id]) }}"
                    class="relative w-full h-[193px] rounded-[24px] bg-slate-100 cursor-pointer transition-all">
                    <img src="{{ $lesson->video->lesson->files[0]->original_url ?? '' }}"
                        class="absolute inset-0 w-full h-full object-cover rounded-[24px] transition-all"
                        alt="">
                    <div
                        class="absolute inset-0 flex items-center justify-center z-10 opacity-0 hover:opacity-100 hover:backdrop-blur rounded-[24px] transition-opacity">
                        <span
                            class="bg-black bg-opacity-50 px-2 py-1 rounded text-white">{{ $lesson->video->name ?? 'No Video' }}</span>
                    </div>
                </a>
            @empty
                <div class="text-center text-slate-500 col-span-2">No favorite videos found.</div>
            @endforelse
        </div>
    </div>
</div>
