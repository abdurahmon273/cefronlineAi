@props([
    'key' => '',
    'question' => '',
    'answerText' => '',
    'audioLink' => null,
])

@php
    // unique id for each component instance
    $uid = 'sq_' . uniqid();
    $audioLink = $audioLink ? trim($audioLink) : null;
@endphp

<div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]" id="{{ $uid }}">
    <h3 class="text-sm font-normal text-gray-900 mb-3 {{$question ? '' : 'hidden'}}">
        {{ $key ? $key.'.' : ''}} {!! $question !!}
    </h3>

    <div class="mb-3">
        <p class="text-sm font-semibold text-gray-900">
            Your answer: {!! $answerText !!}
        </p>
    </div>

    @if(!empty($audioLink))
        <div class="w-full flex justify-end">
            <button
                type="button"
                data-audio-btn
                data-audio-src="{{ $audioLink }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#FFE9E0] text-[#FD4D00] rounded-xl hover:bg-orange-100 transition-colors font-medium text-sm disabled:opacity-60 disabled:cursor-not-allowed"
            >
                {{-- icon (will be swapped by JS) --}}
                <span data-icon class="flex items-center justify-center">
                    {{-- Play icon default --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z" />
                    </svg>
                </span>

                <span data-label>Play</span>

                {{-- tiny spinner (hidden by default) --}}
                <span data-spinner class="hidden ml-1">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                    </svg>
                </span>
            </button>
        </div>

        <script>
            (() => {
                const root = document.getElementById(@json($uid));
                if (!root) return;

                const btn = root.querySelector('[data-audio-btn]');
                if (!btn) return;

                const iconWrap = btn.querySelector('[data-icon]');
                const label = btn.querySelector('[data-label]');
                const spinner = btn.querySelector('[data-spinner]');
                const src = btn.getAttribute('data-audio-src');

                const ICONS = {
                    play: `
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z" />
                        </svg>
                    `,
                    pause: `
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4h4v16H6z"></path>
                            <path d="M14 4h4v16h-4z"></path>
                        </svg>
                    `,
                    loading: `
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4"></path>
                            <path d="M12 18v4"></path>
                            <path d="M4.93 4.93l2.83 2.83"></path>
                            <path d="M16.24 16.24l2.83 2.83"></path>
                            <path d="M2 12h4"></path>
                            <path d="M18 12h4"></path>
                            <path d="M4.93 19.07l2.83-2.83"></path>
                            <path d="M16.24 7.76l2.83-2.83"></path>
                        </svg>
                    `
                };

                // State
                let audio = null;
                let objectUrl = null;
                let isLoading = false;

                const setBtnState = (state) => {
                    // state: 'idle' | 'loading' | 'playing' | 'paused'
                    if (state === 'loading') {
                        isLoading = true;
                        btn.disabled = true;
                        iconWrap.innerHTML = ICONS.loading;
                        label.textContent = 'Loading...';
                        spinner.classList.remove('hidden');
                        return;
                    }

                    isLoading = false;
                    btn.disabled = false;
                    spinner.classList.add('hidden');

                    if (state === 'playing') {
                        iconWrap.innerHTML = ICONS.pause;
                        label.textContent = 'Pause';
                    } else {
                        iconWrap.innerHTML = ICONS.play;
                        label.textContent = 'Play';
                    }
                };

                const ensureAudioLoaded = async () => {
                    if (audio && objectUrl) return;

                    setBtnState('loading');

                    try {
                        // Fetch full audio file into memory (blob)
                        const res = await fetch(src, { cache: 'force-cache' });
                        if (!res.ok) throw new Error('Audio fetch failed: ' + res.status);

                        const blob = await res.blob();
                        objectUrl = URL.createObjectURL(blob);

                        audio = new Audio(objectUrl);
                        audio.preload = 'auto';

                        // When finished, return to Play state
                        audio.addEventListener('ended', () => {
                            setBtnState('paused');
                        });

                        // If user pauses from external reasons, keep UI synced
                        audio.addEventListener('pause', () => {
                            // If ended event fires, pause may fire too; still ok
                            setBtnState('paused');
                        });

                        audio.addEventListener('play', () => {
                            setBtnState('playing');
                        });

                        setBtnState('paused'); // ready to play
                    } catch (e) {
                        console.error(e);
                        // fallback: show Play but disabled
                        btn.disabled = true;
                        spinner.classList.add('hidden');
                        iconWrap.innerHTML = ICONS.play;
                        label.textContent = 'Unavailable';
                    }
                };

                btn.addEventListener('click', async () => {
                    if (isLoading) return;

                    // First click: load fully, then auto play
                    if (!audio) {
                        await ensureAudioLoaded();
                        if (audio && !btn.disabled) {
                            try { await audio.play(); } catch (e) { console.error(e); }
                        }
                        return;
                    }

                    // Toggle play/pause
                    if (audio.paused) {
                        try { await audio.play(); } catch (e) { console.error(e); }
                    } else {
                        audio.pause();
                    }
                });

                // cleanup on page unload
                window.addEventListener('beforeunload', () => {
                    try {
                        if (audio) audio.pause();
                        if (objectUrl) URL.revokeObjectURL(objectUrl);
                    } catch (e) {}
                });
            })();
        </script>
    @endif
</div>
