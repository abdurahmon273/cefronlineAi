@extends('layouts.user')

@section('content')
    @php
        $telegramUrl = $url ?? '#';
        $telegramLabel = $telegramUrl && $telegramUrl !== '#'
            ? preg_replace('#^https?://(t\.me/|telegram\.me/)#', '@', rtrim($telegramUrl, '/'))
            : '@telegram';

        $phoneRaw = $tel ?? '#';
        $phoneHref = $phoneRaw && $phoneRaw !== '#'
            ? 'tel:' . preg_replace('/[^0-9+]/', '', $phoneRaw)
            : '#';
        $phoneDigits = preg_replace('/\D/', '', $phoneRaw ?? '');
        $phoneDisplay = $phoneRaw;

        if (strlen($phoneDigits) === 12) {
            $phoneDisplay = '+(' . substr($phoneDigits, 0, 3) . ') ' .
                substr($phoneDigits, 3, 2) . ' ' .
                substr($phoneDigits, 5, 3) . ' ' .
                substr($phoneDigits, 8, 2) . ' ' .
                substr($phoneDigits, 10, 2);
        }

        $locationUrl = $address ?? '#';
    @endphp

    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm sm:rounded-[32px] sm:p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <a
                                href="{{ $telegramUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group rounded-[24px] border border-slate-200 bg-[#F8FAFC] p-6 transition duration-200 hover:-translate-y-1 hover:border-emerald-200 hover:bg-emerald-50"
                            >
                                <div class="flex h-16 w-16 items-center justify-center rounded-[20px] bg-emerald-500 shadow-lg shadow-emerald-500/20">
                                    <img src="{{ asset('assets/telegram.svg') }}" alt="Telegram" class="h-8 w-8">
                                </div>
                                <h2 class="mt-6 text-2xl font-extrabold text-slate-900">Telegram</h2>
                                <div class="mt-3 text-lg font-bold text-emerald-600 break-all">
                                    {{ $telegramLabel }}
                                </div>
                            </a>

                            <a
                                href="{{ $phoneHref }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group rounded-[24px] border border-slate-200 bg-[#F8FAFC] p-6 transition duration-200 hover:-translate-y-1 hover:border-sky-200 hover:bg-sky-50"
                            >
                                <div class="flex h-16 w-16 items-center justify-center rounded-[20px] bg-sky-500 text-white shadow-lg shadow-sky-500/20">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.62 10.79C8.06 13.62 10.38 15.94 13.21 17.38L15.41 15.18C15.69 14.9 16.1 14.8 16.47 14.91C17.66 15.3 18.94 15.5 20.25 15.5C20.66 15.5 21 15.84 21 16.25V19.75C21 20.16 20.66 20.5 20.25 20.5C10.45 20.5 2.5 12.55 2.5 2.75C2.5 2.34 2.84 2 3.25 2H6.75C7.16 2 7.5 2.34 7.5 2.75C7.5 4.06 7.7 5.34 8.09 6.53C8.2 6.9 8.1 7.31 7.82 7.59L6.62 10.79Z" fill="currentColor"/>
                                    </svg>
                                </div>
                                <h2 class="mt-6 text-2xl font-extrabold text-slate-900">Phone</h2>
                                <div class="mt-3 text-lg font-bold text-sky-600">
                                    {{ $phoneDisplay }}
                                </div>
                            </a>
                        </div>

                        <div class="mt-4">
                            <div class="flex w-full flex-col gap-5 rounded-[24px] border border-slate-200 bg-[#F8FAFC] p-6 transition duration-200 hover:border-amber-200 hover:bg-amber-50 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="flex h-16 w-16 items-center justify-center rounded-[20px] bg-amber-500 text-white shadow-lg shadow-amber-500/20">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 21C16.5 16.8 19 13.2 19 9.75C19 5.47 15.87 2.5 12 2.5C8.13 2.5 5 5.47 5 9.75C5 13.2 7.5 16.8 12 21Z" fill="currentColor"/>
                                            <circle cx="12" cy="9.75" r="2.75" fill="white"/>
                                        </svg>
                                    </div>
                                    <h2 class="mt-6 text-2xl font-extrabold text-slate-900">Location</h2>
{{--                                    <div class="mt-3 text-lg font-bold text-amber-600 break-all">--}}
{{--                                        {{ $locationUrl }}--}}
{{--                                    </div>--}}
                                </div>
                                <div class="flex justify-end items-end">
                                    <a
                                        href="{{ $locationUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-amber-500 px-6 text-sm font-bold text-white transition hover:bg-amber-600"
                                    >
                                        Open map
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('components.membership-card')
            </div>
        </div>
    </div>
@endsection
