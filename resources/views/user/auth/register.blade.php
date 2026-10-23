<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    @php
        $asset = fn ($p) => asset("assets/{$p}");
        $authAsset = fn ($p) => asset("assets/auth/{$p}");
    @endphp

    <div class="min-h-screen bg-white lg:flex">
        {{-- Left promo carousel --}}
        <div class="hidden lg:flex lg:w-1/2 lg:flex-col p-2">
            <div class="relative flex min-h-screen flex-col overflow-hidden bg-orange-50 rounded-xl">
                <div class="px-8 py-8">
                    <div class="flex items-center">
                        <div class="p-1">
                            <img class="h-8 w-auto" src="{{ asset('logo/logo.png') }}" alt="TM">
                        </div>
                    </div>
                </div>

                <div class="relative flex flex-1 flex-col px-10 pb-10 pt-2" data-auth-carousel data-interval="5000">
                    <div class="relative flex-1 overflow-hidden">
                        @livewire('user.auth.step-page-livewire')
                    </div>

                    {{-- Dots --}}
                    <div class="mt-6 flex items-center justify-center gap-2">
                        <button type="button" class="h-2 w-2 rounded-full bg-orange-600" data-auth-carousel-to="0" aria-label="Slide 1"></button>
                        <button type="button" class="h-2 w-2 rounded-full bg-slate-300" data-auth-carousel-to="1" aria-label="Slide 2"></button>
                        <button type="button" class="h-2 w-2 rounded-full bg-slate-300" data-auth-carousel-to="2" aria-label="Slide 3"></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right register form --}}
        <div class="flex w-full items-center justify-center lg:w-1/2">
            <div class="w-full max-w-md px-4 py-10 sm:px-6 lg:px-12">
                {{-- Mobile header (since left promo is hidden on mobile) --}}
                <div class="mb-8 flex items-center justify-center lg:hidden">
                    <div class="rounded-xl bg-white/60 p-3 shadow-sm">
                        <img class="h-16 w-auto" src="{{ asset('logo/logo.png') }}" alt="TM">
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-xl font-extrabold text-orange-600">
                        Welcome to the Teacher Muzaffar<br/>platform
                    </div>
                    <div class="mt-6 text-3xl font-extrabold text-slate-900">Register</div>
                </div>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('google.redirect') }}"
                       class="flex h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gray-100 text-sm font-semibold text-gray-800 hover:bg-gray-200">
                        <img src="{{ asset('logo/google.svg') }}" class="h-5 w-5" alt="">
                        Google
                    </a>

                    <a href="https://t.me/cefr_online_support_login_bot?start=mobile"
                       target="_blank"
                       class="flex h-12 w-full items-center justify-center gap-3 rounded-2xl bg-gray-100 text-sm font-semibold text-gray-800 hover:bg-gray-200">
                        <img src="{{ asset('logo/telegram.svg') }}" class="h-5 w-5" alt="">
                        Telegram
                    </a>
                </div>
                <div class="my-4 flex items-center gap-3">
                    <div class="h-px w-full bg-gray-200"></div>
                    <span class="text-sm text-gray-400">or</span>
                    <div class="h-px w-full bg-gray-200"></div>
                </div>

                <form class="mt-4 space-y-5" action="{{ route('register.submit') }}" method="post">
                    @csrf
                    <input type="hidden" value="{{$id}}" name="user_id">
                    <input
                        type="text"
                        name="name"
                        class="block h-12 w-full rounded-2xl border border-[#D4D4D4] bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                        placeholder="Full name"
                        autocomplete="name" value="{{old('name','')}}"
                    />

                    {{-- Phone --}}
                    <div class="flex h-12 items-center gap-2 rounded-2xl border border-[#D4D4D4] bg-white px-3 shadow-sm focus-within:border-orange-400 focus-within:ring-4 focus-within:ring-orange-500/10">

                        <input
                            type="text"
                            data-mask="number_or_email" name="mobile_number_or_email" id="mobile_number"
                            class="w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                            placeholder="Mobile number or Email"
                            inputmode="numeric"
                            autocomplete="tel" value="{{old('mobile_number_or_email','')}}"
                        />


                    </div>

                    {{-- Password --}}
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            data-password
                            class="block h-12 w-full rounded-2xl border border-[#D4D4D4] bg-white px-3 pr-12 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                            placeholder="Password"
                            autocomplete="new-password"  value="{{old('password','')}}"
                        />
                        <button type="button"
                                data-password-toggle
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon>
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                            <svg class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon-open>
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Confirm password --}}
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            data-password
                            class="block h-12 w-full rounded-2xl border border-[#D4D4D4] bg-white px-3 pr-12 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                            placeholder="Confirm password"
                            autocomplete="new-password" value="{{old('password_confirmation','')}}"
                        />
                        <button type="button"
                                data-password-toggle
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon>
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                            <svg class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon-open>
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit"
                            class="mt-6 h-12 w-full rounded-2xl bg-[#FD4D00] px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#E64500]">
                        Register
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-slate-600">
                    Already have an account?
                    <a class="font-semibold text-orange-600 hover:underline" href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            // Password show/hide
            document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const wrap = btn.closest('.relative');
                    const input = wrap?.querySelector('[data-password]');
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';

                    // Toggle icons
                    const iconClosed = btn.querySelector('[data-password-icon]');
                    const iconOpen = btn.querySelector('[data-password-icon-open]');
                    if (iconClosed && iconOpen) {
                        if (isPassword) {
                            iconClosed.classList.add('hidden');
                            iconOpen.classList.remove('hidden');
                        } else {
                            iconClosed.classList.remove('hidden');
                            iconOpen.classList.add('hidden');
                        }
                    }
                });
            });

            // Left auth carousel
            document.querySelectorAll('[data-auth-carousel]').forEach((root) => {
                const track = root.querySelector('[data-auth-carousel-track]');
                const dots = Array.from(root.querySelectorAll('[data-auth-carousel-to]'));
                if (!track) return;
                const slidesCount = track.children.length;
                if (slidesCount <= 1) return;

                const intervalMs = Number(root.getAttribute('data-interval') || 5000);
                let index = 0;
                let timer = null;

                const setActive = (next) => {
                    index = (next + slidesCount) % slidesCount;
                    const slidePct = 100 / slidesCount;
                    track.style.transform = `translate3d(${-index * slidePct}%, 0, 0)`;
                    dots.forEach((d, i) => {
                        d.classList.toggle('bg-orange-600', i === index);
                        d.classList.toggle('bg-slate-300', i !== index);
                    });
                };

                const start = () => {
                    if (!intervalMs || intervalMs < 1000) return;
                    stop();
                    timer = window.setInterval(() => setActive(index + 1), intervalMs);
                };
                const stop = () => {
                    if (timer) window.clearInterval(timer);
                    timer = null;
                };

                dots.forEach((d) => {
                    d.addEventListener('click', () => {
                        const to = Number(d.getAttribute('data-auth-carousel-to'));
                        if (Number.isNaN(to)) return;
                        setActive(to);
                        start();
                    });
                });

                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);

                setActive(0);
                start();
            });
        })();
    </script>

</body>
</html>
