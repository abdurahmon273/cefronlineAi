@extends('layouts.user')

@section('content')
    @php
        $bg = '#567378'; // teal-ish like your screenshot

        $wrap = 'rounded-[22px] border-2 border-dashed border-violet-400/60 p-6';
        $title = 'mb-3 flex items-center gap-2 text-xs font-semibold tracking-wide text-violet-200/90';
        $panelTitle = 'text-xs font-semibold tracking-wide text-violet-200/90';

        $field = 'relative w-full h-12 rounded-2xl bg-white px-4 py-3 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4]';
        // for "floating label" variants: never overlap label/text
        $fieldLabeled = 'relative w-full h-12 rounded-2xl bg-white px-4 pt-6 pb-2 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4]';
        $fieldFocus = 'focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none';
        $fieldOk = 'border-[1.5px] border-[#D4D4D4]';
        $fieldError = 'border-2 border-orange-500';

        $label = 'pointer-events-none absolute top-2 left-5 text-[11px] leading-none text-slate-500';
        $labelError = 'text-orange-500';
    @endphp

    <div class="min-h-screen" style="background: {{ $bg }}">
        <div class="mx-auto max-w-7xl px-6 py-10">
            <div class="mb-10 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-white/90">UI Components</div>
                    <div class="text-xs text-white/60">Tailwind-only (no plugins). Matches your screenshots.</div>
                </div>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/10 hover:bg-white/15">
                    Back
                </a>
            </div>

            {{-- ===== Screen 1: Form components ===== --}}
            <div class="grid gap-10 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>Form/Input/Password</span>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach ([false, true] as $withIcon)
                                <div class="space-y-4">
                                    {{-- empty --}}
                                    <div class="relative">
                                        <input type="password"
                                               class="{{ $field }} {{ $fieldFocus }} {{ $fieldOk }} pr-12"
                                               placeholder="Password"
                                               data-password />

                                        <button type="button"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                                data-password-toggle>
                                            {{-- eye / eye-off --}}
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- filled --}}
                                    <div class="relative">
                                        <span class="{{ $label }}">Password</span>
                                        <input type="text"
                                               class="{{ $fieldLabeled }} {{ $fieldFocus }} {{ $fieldOk }} pr-12"
                                               value="Sukhrobjon1122QAZ"
                                               data-password />
                                        <button type="button"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                                data-password-toggle>
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- error --}}
                                    <div class="relative">
                                        <span class="{{ $label }} {{ $labelError }}">Password</span>
                                        <input type="text"
                                               class="{{ $fieldLabeled }} {{ $fieldFocus }} {{ $fieldError }} pr-12"
                                               value="Sukhrobjon1122QAZ"
                                               data-password />
                                        <button type="button"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                                data-password-toggle>
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-10 grid gap-10 md:grid-cols-2">
                        {{-- Text input --}}
                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>Form/Input/Text</span>
                            </div>

                            <div class="space-y-4">
                                <div class="relative">
                                    <input class="{{ $field }} {{ $fieldFocus }} {{ $fieldOk }}"
                                           placeholder="Text label" />
                                </div>

                                <div class="relative">
                                    <span class="{{ $label }}">Text label</span>
                                    <input class="{{ $fieldLabeled }} {{ $fieldFocus }} {{ $fieldOk }}"
                                           value="Input text" />
                                </div>

                                <div class="relative">
                                    <span class="{{ $label }} {{ $labelError }}">Password</span>
                                    <input class="{{ $fieldLabeled }} {{ $fieldFocus }} {{ $fieldError }}"
                                           value="Input text" />
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown --}}
                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>Form/Input/Dropdown</span>
                            </div>

                            <div class="space-y-4">
                                <x-ui.select
                                    label="Text Label"
                                    placeholder="Text Label"
                                    :options="['Option', 'Option', 'Option', 'Option']"
                                />

                                <x-ui.select
                                    label="Text label"
                                    value="Input text"
                                    :options="['Input text', 'Option', 'Option', 'Option']"
                                />

                                <x-ui.select
                                    label="Text label"
                                    value="Input text"
                                    :options="['Input text', 'Option', 'Option', 'Option']"
                                    :error="true"
                                    :open="true"
                                />
                            </div>
                        </div>

                        {{-- Input group (phone) --}}
                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>Form/Input/Group</span>
                            </div>

                            <div class="space-y-4">
                                @foreach ([false, false, true] as $isError)
                                    <div class="relative">
                                        <span class="{{ $label }} {{ $isError ? $labelError : '' }}">Text label</span>
                                        <div class="flex h-12 items-center gap-3 rounded-2xl bg-white px-4 pt-6 pb-2 border-[1.5px] {{ $isError ? 'border-2 border-orange-500' : 'border-[#D4D4D4] focus-within:border-2 focus-within:border-[#FD4D00]' }}">
                                            <div class="flex shrink-0 items-center gap-2 text-slate-700">
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-200 text-[10px] font-semibold">UZ</span>
                                                <span class="text-sm font-medium leading-none">+998</span>
                                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 9l6 6 6-6"/>
                                                </svg>
                                            </div>
                                            <div class="h-5 w-px bg-slate-200"></div>
                                            <input class="w-full bg-transparent text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] focus:outline-none"
                                                   placeholder="__________" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right rail: Form + Checkbox --}}
                <div class="space-y-10">
                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>Form</span>
                        </div>

                        <div class="space-y-3">
                            <div class="relative">
                                <input class="{{ $field }} {{ $fieldFocus }}" placeholder="Text label" />
                            </div>
                            <div class="pl-4 text-xs text-white/60">Description</div>
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>Checkbox</span>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-3 text-white/80">
                                <input type="checkbox" checked
                                       class="h-5 w-5 appearance-none rounded-md bg-orange-600 ring-2 ring-white/40 checked:bg-orange-600" />
                                <span class="text-sm">Label</span>
                            </label>
                            <label class="flex items-center gap-3 text-white/80">
                                <input type="checkbox"
                                       class="h-5 w-5 appearance-none rounded-md bg-white ring-2 ring-white/40 checked:bg-orange-600" />
                                <span class="text-sm">Label</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Screen 2: Buttons + layout components ===== --}}
            <div class="mt-14 grid gap-10 lg:grid-cols-12">
                {{-- Left: Button variants + Sidebar --}}
                <div class="space-y-10 lg:col-span-3">
                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>topnav</span>
                        </div>

                        <div class="space-y-4 -mx-6">
                            <x-ui.topnav variant="signed_out" />
                            <x-ui.topnav variant="signed_in" />
                            <x-ui.topnav variant="menu_open" user-name="Sukhrob Khurramov" />
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>nav button</span>
                        </div>

                        <div class="space-y-3">
                            <x-ui.nav-button :active="false" label="Home page" />
                            <x-ui.nav-button :active="true" label="Home page" />
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>button</span>
                        </div>
                        <div class="space-y-3">
                            <button class="flex w-full items-center justify-center gap-2 rounded-full bg-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-sm">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-white/15">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                Sign in
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-white/15">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14"/>
                                        <path d="M13 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>

                            <button class="flex w-full items-center justify-center gap-2 rounded-full bg-orange-50 px-5 py-3 text-sm font-semibold text-orange-600 shadow-sm ring-1 ring-orange-200">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-orange-600 text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                Sign in
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-orange-600 text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14"/>
                                        <path d="M13 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>

                            <button class="flex w-full items-center justify-center gap-2 rounded-full bg-transparent px-5 py-3 text-sm font-semibold text-orange-500">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-orange-600 text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                Sign in
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-orange-600 text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14"/>
                                        <path d="M13 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>mock test card/left ...</span>
                        </div>

                        <x-ui.sidebar active="Home page" />
                    </div>
                </div>

                {{-- Main: banners + cards --}}
                <div class="space-y-8 lg:col-span-9">
                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>hero banners</span>
                        </div>

                        <div class="relative" data-carousel data-interval="4500">
                            <div class="relative overflow-hidden rounded-2xl shadow-sm ring-1 ring-black/5">
                                <div class="flex w-[200%] transition-transform duration-500 ease-[cubic-bezier(.22,1,.36,1)] will-change-transform"
                                     data-carousel-track
                                     style="transform: translate3d(0, 0, 0);">
                                    {{-- Slide 1 --}}
                                    <div class="w-1/2 shrink-0">
                                        <div class="relative flex min-h-[160px] items-center bg-gradient-to-r from-emerald-500 to-emerald-600 px-7 py-6 text-white md:min-h-[180px]">
                                            <div class="max-w-xl pr-24">
                                                <div class="text-3xl font-extrabold leading-tight md:text-4xl">Raise your CEFR<br/>level faster with us</div>
                                            </div>
                                            <div class="absolute right-6 top-1/2 hidden -translate-y-1/2 md:block">
                                                <div class="grid h-24 w-24 place-items-center rounded-2xl bg-white/20 text-sm font-bold backdrop-blur-[1px]">EXAM</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Slide 2 --}}
                                    <div class="w-1/2 shrink-0">
                                        <div class="relative flex min-h-[160px] items-center bg-gradient-to-r from-amber-400 to-amber-500 px-7 py-6 text-white md:min-h-[180px]">
                                            <div class="max-w-xl pr-24">
                                                <div class="text-3xl font-extrabold leading-tight md:text-4xl">Raise your CEFR<br/>level faster with us</div>
                                            </div>
                                            <div class="absolute right-6 top-1/2 hidden -translate-y-1/2 md:block">
                                                <div class="grid h-24 w-24 place-items-center rounded-2xl bg-white/20 text-sm font-bold backdrop-blur-[1px]">EXAM</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Indicators --}}
                            <div class="pointer-events-auto absolute bottom-5 left-7 flex items-center gap-2 opacity-80">
                                <button type="button"
                                        class="h-1.5 w-8 rounded-full bg-white/70 transition"
                                        aria-label="Slide 1"
                                        data-carousel-to="0"></button>
                                <button type="button"
                                        class="h-1.5 w-8 rounded-full bg-white/30 transition"
                                        aria-label="Slide 2"
                                        data-carousel-to="1"></button>
                            </div>
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>mock test card</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ([
                                ['t' => 'Listening', 'c' => 'from-rose-500 to-rose-600', 'sub' => '200 questions'],
                                ['t' => 'Reading', 'c' => 'from-amber-400 to-amber-500', 'sub' => '200 questions'],
                                ['t' => 'Writing', 'c' => 'from-emerald-500 to-emerald-600', 'sub' => '200 questions'],
                                ['t' => 'Speaking', 'c' => 'from-orange-500 to-orange-600', 'sub' => '200 questions'],
                            ] as $card)
                                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $card['c'] }} p-5 text-white shadow-sm">
                                    <div class="text-lg font-extrabold">{{ $card['t'] }}</div>
                                    <div class="mt-1 text-xs text-white/80">{{ $card['sub'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="{{ $wrap }}">
                        <div class="{{ $title }}">
                            <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                            <span>fill card</span>
                        </div>

                        <div class="space-y-4">
                            @foreach ([1,2] as $n)
                                <div class="flex items-center justify-between gap-4 rounded-3xl bg-orange-50 px-6 py-5 shadow-sm">
                                    <div>
                                        <div class="text-2xl font-extrabold text-orange-600">Full mock</div>
                                        <div class="text-xs text-orange-500">500+ English questions</div>
                                    </div>
                                    <button class="inline-flex items-center gap-2 rounded-full bg-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-sm">
                                        Start Full Mock
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14"/>
                                            <path d="M13 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-8 lg:grid-cols-2">
                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>video</span>
                            </div>
                            <div class="grid h-56 place-items-center rounded-3xl bg-white/80 text-sm text-slate-500">
                                video image
                            </div>
                        </div>

                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>breadcrumb</span>
                            </div>
                            <div class="space-y-3">
                                @foreach (['About Multilevel Test', 'Baholash tizimi'] as $crumb)
                                    <div class="flex items-center gap-3">
                                        <button class="grid h-10 w-10 place-items-center rounded-2xl bg-white text-slate-700 shadow-sm">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15 18l-6-6 6-6"/>
                                            </svg>
                                        </button>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-white px-3 py-1 text-xs text-slate-600 shadow-sm">Lessons</span>
                                            <span class="rounded-full bg-white px-3 py-1 text-xs text-slate-600 shadow-sm">About Multilevel Test</span>
                                            <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-600 shadow-sm">Baholash tizimi</span>
                                        </div>
                                    </div>
                                    <div class="pl-14 text-2xl font-extrabold text-white/35">{{ $crumb }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-8 lg:grid-cols-2">
                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>TabsContainer</span>
                            </div>
                            <x-ui.tabs />
                        </div>

                        <div class="{{ $wrap }}">
                            <div class="{{ $title }}">
                                <span class="h-2 w-2 rotate-45 rounded-sm bg-violet-300"></span>
                                <span>toast</span>
                            </div>
                            <div class="flex flex-col gap-3">
                                <x-ui.toast type="success" text="Saved" />
                                <x-ui.toast type="success" text="Saved" />
                                <x-ui.toast type="warn" text="Saved" />
                                <x-ui.toast type="error" text="Saved" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (() => {
            // Password show/hide
            const toggles = document.querySelectorAll('[data-password-toggle]');
            toggles.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const wrap = btn.closest('.relative');
                    const input = wrap?.querySelector('[data-password]');
                    if (!input) return;
                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });

            // Topnav profile dropdown
            const topnavs = document.querySelectorAll('[data-topnav]');
            const closeAll = () => {
                topnavs.forEach((nav) => {
                    const menu = nav.querySelector('[data-topnav-menu]');
                    const profile = nav.querySelector('[data-topnav-profile]');
                    if (!menu || !profile) return;
                    menu.hidden = true;
                    profile.setAttribute('aria-expanded', 'false');
                    nav.setAttribute('data-open', 'false');
                });
            };

            topnavs.forEach((nav) => {
                const menu = nav.querySelector('[data-topnav-menu]');
                const profile = nav.querySelector('[data-topnav-profile]');
                if (!menu || !profile) return;

                // initial state
                const initiallyOpen = nav.getAttribute('data-open') === 'true';
                menu.hidden = !initiallyOpen;
                profile.setAttribute('aria-expanded', initiallyOpen ? 'true' : 'false');

                profile.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !menu.hidden;
                    closeAll();
                    menu.hidden = isOpen;
                    profile.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    nav.setAttribute('data-open', isOpen ? 'false' : 'true');
                });
            });

            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });

            // Tabs
            document.querySelectorAll('[data-tabs]').forEach((tabs) => {
                tabs.querySelectorAll('[data-tab]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        tabs.querySelectorAll('[data-tab]').forEach((b) => {
                            b.setAttribute('aria-selected', 'false');
                            b.classList.remove('bg-white', 'text-orange-600', 'shadow-sm', 'ring-1', 'ring-black/5');
                            b.classList.add('text-slate-400');
                        });
                        btn.setAttribute('aria-selected', 'true');
                        btn.classList.remove('text-slate-400');
                        btn.classList.add('bg-white', 'text-orange-600', 'shadow-sm', 'ring-1', 'ring-black/5');
                    });
                });
            });

            // Custom selects
            const selects = document.querySelectorAll('[data-select]');
            const closeAllSelects = () => {
                selects.forEach((root) => {
                    const menu = root.querySelector('[data-select-menu]');
                    const trigger = root.querySelector('[data-select-trigger]');
                    if (!menu || !trigger) return;
                    menu.hidden = true;
                    trigger.setAttribute('aria-expanded', 'false');
                    root.setAttribute('data-open', 'false');
                });
            };

            selects.forEach((root) => {
                const menu = root.querySelector('[data-select-menu]');
                const trigger = root.querySelector('[data-select-trigger]');
                const display = root.querySelector('[data-select-display]');
                const hidden = root.querySelector('input[type="hidden"]');
                if (!menu || !trigger || !display) return;

                // initial state
                const initiallyOpen = root.getAttribute('data-open') === 'true';
                menu.hidden = !initiallyOpen;
                trigger.setAttribute('aria-expanded', initiallyOpen ? 'true' : 'false');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !menu.hidden;
                    closeAllSelects();
                    menu.hidden = isOpen;
                    trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    root.setAttribute('data-open', isOpen ? 'false' : 'true');
                });

                root.querySelectorAll('[data-select-option]').forEach((opt) => {
                    opt.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const val = opt.getAttribute('data-value') ?? opt.textContent?.trim() ?? '';
                        if (!val) return;

                        display.textContent = val;
                        root.setAttribute('data-value', val);
                        if (hidden) hidden.value = val;

                        // active styling + aria
                        root.querySelectorAll('[data-select-option]').forEach((b) => {
                            b.setAttribute('aria-selected', 'false');
                            b.classList.remove('bg-slate-100');
                        });
                        opt.setAttribute('aria-selected', 'true');
                        opt.classList.add('bg-slate-100');

                        // make display look selected (not placeholder)
                        trigger.classList.remove('text-slate-400');
                        trigger.classList.add('text-slate-800');

                        menu.hidden = true;
                        trigger.setAttribute('aria-expanded', 'false');
                        root.setAttribute('data-open', 'false');
                    });
                });
            });

            document.addEventListener('click', () => closeAllSelects());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAllSelects();
            });

            // Carousel (hero banners) — translateX track (smooth, no stacking)
            document.querySelectorAll('[data-carousel]').forEach((root) => {
                const track = root.querySelector('[data-carousel-track]');
                const indicators = Array.from(root.querySelectorAll('[data-carousel-to]'));
                if (!track) return;

                const slides = Array.from(track.children);
                const slidesCount = slides.length;
                if (slidesCount <= 1) return;

                const intervalMs = Number(root.getAttribute('data-interval') || 4500);
                let index = 0;
                let timer = null;

                const slidePct = 100 / slidesCount;

                const setActive = (next) => {
                    index = (next + slidesCount) % slidesCount;
                    track.style.transform = `translate3d(${-index * slidePct}%, 0, 0)`;
                    indicators.forEach((btn, i) => {
                        const active = i === index;
                        btn.classList.toggle('bg-white/70', active);
                        btn.classList.toggle('bg-white/30', !active);
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

                indicators.forEach((btn) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const to = Number(btn.getAttribute('data-carousel-to'));
                        if (Number.isNaN(to) || to < 0 || to >= slidesCount) return;
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
@endsection
