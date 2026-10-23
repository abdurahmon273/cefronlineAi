@props([
    'variant' => 'signed_out', // signed_out | signed_in | menu_open
])

@php
    $signedOut = $variant === 'signed_out';
    $defaultOpen = $variant === 'menu_open';
    $asset = fn ($p) => asset("{$p}");
    $user = auth()->user();
    $unreadCount = $signedOut || ! $user ? 0 : $user->unreadNotificationsCount();
    @endphp

<div {{ $attributes->merge(['class' => 'relative w-full']) }} data-topnav data-open="{{ $defaultOpen ? 'true' : 'false' }}">
    <div class="flex w-full items-center justify-between bg-white px-4 sm:px-6 py-4">
        <div class="flex items-center gap-3">
            {{-- Hamburger Menu Button (Mobile Only) --}}
            @if (!$signedOut)
            <button type="button"
                    data-mobile-menu-toggle
                    class="lg:hidden grid h-[40px] w-[40px] place-items-center rounded-[12px] bg-slate-100 text-slate-600 hover:bg-slate-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            @endif
            <div class="flex items-center">
                {{-- TM logo --}}
               <img src="{{ $asset('logo/logo.svg') }}" alt="TM logo" class="h-[28px] sm:h-[32px]">
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if (! $signedOut && $user)
                @livewire('user.balance-plan-livewire')
            @endif

            <div class="relative">
                <button type="button"
                        data-notification-trigger
                        class="relative grid h-[48px] w-[48px] place-items-center rounded-[16px] bg-slate-100 text-slate-600 hover:bg-slate-200">
                   <img src="{{ $asset('assets/notification-02.svg') }}" alt="Notifications" class="h-[24px]">
                   @if($unreadCount > 0)
                       <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $unreadCount }}</span>
                   @endif
                </button>

                {{-- Notifications Dropdown --}}
                <div id="notifications-modal"
                    class="absolute left-1/3 -translate-x-3/4 sm:left-auto sm:translate-x-0 sm:right-0 top-full z-50 mt-2 hidden w-[calc(100vw-2rem)] sm:w-96 max-w-sm max-h-96 overflow-y-auto overflow-hidden rounded-3xl bg-white p-4 shadow-lg"
                    data-notification-modal>
                    @livewire('user.notification.index-livewire')
                </div>
            </div>

            @livewire('show-news-modal-livewire')
            @if ($signedOut)
                <a type="button" href="{{route('login')}}"
                        class="rounded-lg bg-[#FD4D00] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#E64500]">
                    Sign in
                </a>
            @else
                <button type="button"
                        class="relative h-[48px] w-[48px] overflow-hidden rounded-[1000px]"
                        data-topnav-profile
                        aria-haspopup="menu"
                        aria-expanded="{{ $defaultOpen ? 'true' : 'false' }}">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(($user->name ?? '') . ' ' . ($user->surname ?? '')) }}&background=1f2937&color=fff&size=256"
                         alt="{{ $user->name }}"
                         class="h-full w-full object-cover">
                </button>
            @endif
        </div>
    </div>

    @if (! $signedOut)
        <div class="absolute right-0 top-full z-50 mt-2 w-[calc(100vw-2rem)] sm:w-64 max-w-xs rounded-xl bg-white p-2 shadow-lg ring-1 ring-black/5"
             data-topnav-menu
             @if (! $defaultOpen) hidden @endif>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-3 py-2.5">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(($user->name ?? '') . ' ' . ($user->surname ?? '')) }}&background=1f2937&color=fff&size=256"
                     alt="{{ $user->name }}"
                     class="h-8 w-8 rounded-full object-cover">
                <div class="text-sm font-semibold text-slate-800">{{ $user->name }} {{ $user->surname }}</div>
            </div>

            <div class="mt-1 space-y-0.5">
                <a href="{{ route('user.profile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                   <img src="{{ $asset('assets/settings-02.svg') }}" alt="Settings" class="h-[24px]">
                    Settings
                </a>
                <hr class="my-4 border-slate-200 px-8">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 w-full text-left">
                       <img src="{{ $asset('assets/logout-02.svg') }}" alt="Logout" class="h-[24px]">
                        Log out of profile
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>


