<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $asset = fn ($p) => asset("assets/{$p}");
        $authAsset = fn ($p) => asset("assets/auth/{$p}");
    @endphp

    <div class="min-h-screen bg-white lg:flex">
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

        <div class="flex w-full items-center justify-center lg:w-1/2">
            <div class="w-full max-w-md px-4 py-10 sm:px-6 lg:px-12">
                <div class="mb-8 flex items-center justify-center lg:hidden">
                    <div class="rounded-xl bg-white/60 p-3 shadow-sm">
                        <img class="h-16 w-auto" src="{{ asset('logo/logo.png') }}" alt="TM">
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-3xl font-extrabold text-slate-900">{{$new ? 'New' : 'Reset'}} password</div>
                </div>

                <div class="mt-4 text-center text-md text-slate-600">
                    Enter a new password for <span class="font-regular text-slate-900">{{$type ?  $maskedPhone : $maskedEmail }}</span>
                </div>

                <form class="mt-8 space-y-4" action="{{ route('password.reset.submit') }}" method="post">
                    @csrf
                    <div>
                        <input
                            type="password"
                            name="password"
                            class="block h-12 w-full rounded-2xl border border-[#D4D4D4] bg-white px-3 pr-12 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                            placeholder="New password"
                            autocomplete="new-password"
                        />
                    </div>

                    <div>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="block h-12 w-full rounded-2xl border border-[#D4D4D4] bg-white px-3 pr-12 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                            placeholder="Confirm password"
                            autocomplete="new-password"
                        />
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit"
                            class="mt-6 h-12 w-full rounded-2xl bg-[#FD4D00] px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#E64500]">
                        {{$new ? 'Save' : 'Change'}} password
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-slate-600">
                    <a class="font-semibold text-orange-600 hover:underline" href="{{ route('login') }}">Back to login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
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
