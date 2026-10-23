<div class="bg-white h-full overflow-y-auto">
    <div class="space-y-0.5 p-3 py-6">
    @php $authCheck = auth()->check(); @endphp

        {{-- Home page --}}
        @php $homeActive = request()->routeIs('user.dashboard'); @endphp
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $homeActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($homeActive ? 'assets/sidebar/home-active.svg' : 'assets/sidebar/home.svg') }}"
                alt="Home page"
                class="h-5 w-5 {{ $homeActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Home page</span>
        </a>

        {{-- Lessons --}}
        @php $lessonsActive = request()->routeIs('user.lesson*'); @endphp
        <a href="{{ route('user.lessons') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $lessonsActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($lessonsActive ? 'assets/sidebar/youtube-active.svg' : 'assets/sidebar/youtube.svg') }}"
                alt="Lessons"
                class="h-5 w-5 {{ $lessonsActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Lessons</span>
        </a>

        @if($authCheck)
        {{-- Favorite videos --}}
        @php $favActive = request()->routeIs('user.favorite'); @endphp
        <a href="{{ route('user.favorite') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $favActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($favActive ? 'assets/sidebar/all-bookmark-active.svg' : 'assets/sidebar/all-bookmark.svg') }}"
                alt="Favorite videos"
                class="h-5 w-5 {{ $favActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Favorite videos</span>
        </a>
        @endif
        {{-- Practice --}}
        @php $practiceActive = request()->routeIs('user.practices', 'user.practice-tests', 'user.practice-test', 'user.practice-parts'); @endphp
        <a href="{{ route('user.practices') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $practiceActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($practiceActive ? 'assets/sidebar/note-active.svg' : 'assets/sidebar/note.svg') }}"
                alt="Practice"
                class="h-5 w-5 {{ $practiceActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Practice</span>
        </a>

        @if($authCheck)
         @php $resultActive = request()->routeIs('user.results*'); @endphp
        <a href="{{ route('user.results') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $resultActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($resultActive ? 'assets/sidebar/badge-check-active.png' : 'assets/sidebar/badge-check.png') }}"
                alt="Results"
                class="h-5 w-5 {{ $resultActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Results</span>
        </a>
        @endif
        @php $resultActive = request()->routeIs('user.market*'); @endphp
        <a href="{{ route('user.market') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $resultActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($resultActive ? 'assets/sidebar/market-active.png' : 'assets/sidebar/market.png') }}"
                alt="Market"
                class="h-5 w-5 {{ $resultActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Market</span>
        </a>

        {{-- Profile --}}
        @php $profileActive = request()->routeIs('user.profile'); @endphp

        <a href="{{ route('user.profile') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $profileActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <img
                src="{{ asset($profileActive ? 'assets/sidebar/user-active.svg' : 'assets/sidebar/user.svg') }}"
                alt="Profile"
                class="h-5 w-5 {{ $profileActive ? 'opacity-100' : 'opacity-40' }}"
            >
            <span>Profile</span>
        </a>

        {{-- Contact --}}
        @php $contactActive = request()->routeIs('user.contact'); @endphp
        <a href="{{ route('user.contact') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-full text-base leading-[1.5] font-normal
           {{ $contactActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}">
            <span class="flex h-5 w-5 items-center justify-center">
                @if($contactActive)
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="rgb(194 65 12 / var(--tw-bg-opacity, 1))"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M3 7.5C3 6.11929 4.11929 5 5.5 5H18.5C19.8807 5 21 6.11929 21 7.5V16.5C21 17.8807 19.8807 19 18.5 19H5.5C4.11929 19 3 17.8807 3 16.5V7.5Z"/>
                        <path d="M4.82243 6.35417C4.43205 6.09392 3.90498 6.19941 3.64473 6.5898C3.38448 6.98018 3.48997 7.50725 3.88036 7.7675L10.8197 12.3933C11.7333 13.0023 12.9891 13.0023 13.9027 12.3933L20.8421 7.7675C21.2325 7.50725 21.338 6.98018 21.0777 6.5898C20.8175 6.19941 20.2904 6.09392 19.9 6.35417L12.9607 10.98C12.6179 11.2085 12.1468 11.2085 11.804 10.98L4.86467 6.35417H4.82243Z" fill="white"/>
                    </svg>
                @else
                    <svg
                        class="h-5 w-5 opacity-40"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M21 7.5C21 6.11929 19.8807 5 18.5 5H5.5C4.11929 5 3 6.11929 3 7.5V16.5C3 17.8807 4.11929 19 5.5 19H18.5C19.8807 19 21 17.8807 21 16.5V7.5Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="M4 7L10.9393 11.6258C11.568 12.0449 12.432 12.0449 13.0607 11.6258L20 7"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                @endif
            </span>
            <span>Contact</span>
        </a>
    </div>
</div>
