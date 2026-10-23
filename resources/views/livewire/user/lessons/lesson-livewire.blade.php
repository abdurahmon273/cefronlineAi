<div class="lg:col-span-9">
    @php
        $asset = fn ($p) => asset("assets/{$p}");
    @endphp
    <div class="rounded-3xl bg-[#fafafa] p-[32px]">
        {{-- Back Button, Breadcrumb, and Bookmark --}}
        @if ($videoLessonFirst)
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('user.lessons.detail', $videoLessonFirst->lesson->course->id) }}"
                    class="flex h-[54px] w-[54px] items-center justify-center rounded-[16px] bg-white hover:bg-slate-200 transition-colors">
                    <img class="h-[24px] w-[24px]" src="{{ $asset('arrow-left.svg') }}" alt="arrow left">
                </a>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <a href="{{ route('user.lessons') }}"
                        class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900">Courses</a>
                    <span
                        class="py-[2px] px-[4px] w-[20px] h-[20px] flex items-center justify-center bg-white rounded-[6px]">
                        <img class="h-[12px] w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                    </span>
                    <a href="{{ route('user.lessons.detail', $videoLessonFirst->lesson->course->id) }}"
                        class="py-[2px] px-[4px] bg-white rounded-[6px] text-[#a1a1a1] hover:text-slate-900">{{ $videoLessonFirst->lesson->course->name }}</a>
                    <span
                        class="py-[2px] px-[4px] w-[20px] h-[20px] flex items-center justify-center bg-white rounded-[6px]">
                        <img class="h-[12px] w-[12px]" src="{{ $asset('arrow-right.svg') }}" alt="arrow right">
                    </span>
                    <span class="text-[#FD4D00] py-[2px] px-[4px] bg-white rounded-[6px]">{{ $videoLessonFirst->name ?? $videoLessonFirst->lesson->name }}</span>
                </div>
            </div>
            <button wire:click="saveLesson({{ $videoLessonFirst->id }})"
                class="flex h-[53px] w-[53px] z-0 items-center justify-center rounded-[16px] bg-white hover:bg-slate-200 transition-colors">
                @if($is_saved)
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bookmark-check-icon lucide-bookmark-check text-black"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2Z"/><path d="m9 10 2 2 4-4"/></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bookmark-icon lucide-bookmark"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                @endif
            </button>
        </div>

        {{-- Title --}}
        <div class="text-[32px] font-extrabold text-slate-900 mb-2">{{ $videoLessonFirst->name ?? $videoLessonFirst->lesson->name }}</div>
        {{-- Views --}}
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2.457 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.543 7-1.275 4.057-5.065 7-9.543 7-4.478 0-8.268-2.943-9.543-7z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ $videoLessonFirst->view_count ?? 0 }} views
        </div>
        {{-- Video Player --}}
        <div class="w-full h-[500px] rounded-3xl bg-slate-100 flex items-center justify-center mb-6">
            @if($this->isYouTube() && $this->getVideoId())
                <iframe class="w-full h-full rounded-3xl"
                    src="https://www.youtube.com/embed/{{ $this->getVideoId() }}?rel=0&modestbranding=1" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            @else
                <video class="w-full h-full rounded-3xl" controls preload="metadata">
                    <source src="{{ $videoLessonFirst->link }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="mb-6">
            <div class = 'w-full rounded-2xl bg-[#F0F0F0] p-1 text-[16px]'>
                <div class="flex gap-1">
                    <button type="button"
                        data-tab="comments"
                        class="flex-1 rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition border-none bg-white text-orange-600">
                            Comments
                    </button>
                    <button type="button"
                        data-tab="files"
                        class="flex-1 rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition border-none text-slate-400 hover:bg-slate-50 hover:text-slate-600">
                            Files
                    </button>
                    <button type="button"
                        data-tab="playlist"
                        class="flex-1 rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition border-none text-slate-400 hover:bg-slate-50 hover:text-slate-600">
                            Playlist
                    </button>
                </div>
            </div>

            {{-- Comments Section --}}
            <div id="tab-comments" class="mt-6">
                <div class="max-h-96 overflow-y-auto space-y-6">
                    {{-- Comment 1 --}}
                    @foreach ($comments as $comment)
                        <div class="flex gap-4 bg-white rounded-[24px] p-[24px]">
                            <div class="h-10 w-10 shrink-0 rounded-full bg-slate-200">
                                @if($comment->user?->image) <img class="h-10 w-10 rounded-full object-cover" src="{{ $comment->user->image }}" alt="user avatar"> @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-slate-900">{{ $comment->user?->name }}</span>
                                    @if($comment->user?->id === auth()->id())
                                        <span class="text-xs text-slate-500">(you)</span>
                                    @endif
                                    <span class="text-xs text-slate-500">•</span>
                                    <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-700">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Write Comment Input --}}
                <div class="flex gap-3 pt-4 sticky bottom-0 pb-4 bg-[#fafafa]">
                    <div class="flex-1 flex gap-2">
                        <input type="text" placeholder="Write a comment" wire:model.live="commentText" wire:keydown.enter="sendComment({{ $videoLessonFirst->id }})"
                            class="flex-1 rounded-2xl border-[1.5px] border-[#D4D4D4] bg-white px-4 py-2 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:border-2 focus:border-[#FD4D00] focus:outline-none">
                        <button wire:click="sendComment({{ $videoLessonFirst->id }})"
                            class="flex h-[48px] w-[48px] items-center justify-center rounded-[16px] bg-[#FD4D00] hover:bg-[#E64500] transition-colors">
                            <img class="h-[24px] w-[24px]" src="{{ $asset('telegram.svg') }}" alt="send">
                        </button>
                    </div>
                </div>
            </div>
            {{-- Files Section --}}
            <div id="tab-files" class="mt-6 hidden">
                @if($videoLessonFirst->files && $videoLessonFirst->files->count() > 0)
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($videoLessonFirst->files as $file)
                            <a href="{{ $file->original_url }}" target="_blank"
                                class="block p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow border">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if(str_contains($file->mime_type, 'image'))
                                            <img src="{{ $file->original_url }}" alt="file" class="w-12 h-12 object-cover rounded">
                                        @elseif(str_contains($file->mime_type, 'pdf'))
                                            <div class="w-12 h-12 bg-red-100 rounded flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ explode('_', $file->file_name ?? 'File', 2)[1] ?? ($file->file_name ?? 'File') }}</div>
                                        <div class="text-xs text-gray-500">{{ strtoupper(pathinfo($file->original_url, PATHINFO_EXTENSION)) }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="gap-4 text-center py-12 text-slate-500">
                        No files available.
                    </div>
                @endif
            </div>
            <div id="tab-playlist" class="mt-6 hidden">
                @if($videoLessons && $videoLessons->count() > 0)
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($videoLessons as $video)
                            <button wire:click="selectVideo({{ $video->id }})"
                                class="block p-4 bg-white rounded-lg shadow hover:shadow-lg transition-all @if($videoLessonFirst->id === $video->id) ring-2 ring-[#FD4D00] @endif hover:bg-slate-50 border">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if(str_contains($video->link, 'youtube.com') || str_contains($video->link, 'youtu.be'))
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-youtube-icon lucide-youtube"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $video->name ?? 'Video ' . $loop->iteration }}</div>
                                        <div class="text-xs text-gray-500">{{ $video->view_count ?? 0 }} views</div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-slate-500">
                        No videos available.
                    </div>
                @endif
            </div>
        </div>
        @else
            <div class="text-center py-12 text-slate-500">
                Video not found.
            </div>
        @endif
    </div>
</div>
