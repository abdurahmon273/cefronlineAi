// resources/js/test/listening/audio.js

(function () {
    function $(id) { return document.getElementById(id); }

    function getHiddenNumber(id, fallback = 0) {
        const el = $(id);
        if (!el) return fallback;
        const n = Number(String(el.value ?? '').trim());
        return Number.isFinite(n) ? n : fallback;
    }

    function getAssetBase() {
        const el = $('asset_url');
        const base = (el?.value || '/').trim();
        return base.endsWith('/') ? base.slice(0, -1) : base;
    }

    function formatTime(sec) {
        const s = Math.max(0, Math.floor(sec || 0));
        const m = Math.floor(s / 60);
        const r = s % 60;
        return `${m}:${String(r).padStart(2, '0')}`;
    }

    function extractAudioUrl(partData) {
        if (!partData) return null;

        const raw =
            partData.audio_url ??
            partData.audio ??
            partData.audioPath ??
            partData.audio_path ??
            partData.file ??
            null;

        if (!raw) return null;

        let url = String(raw).trim();
        if (!url) return null;

        if (url.startsWith('http://') || url.startsWith('https://')) return url;
        if (url.startsWith('/storage/')) return url;
        if (url.startsWith('/')) return url;

        if (url.startsWith('public/')) {
            return '/storage/' + url.replace(/^public\//, '');
        }

        return getAssetBase() + '/' + url.replace(/^\/+/, '');
    }

    const partKeyToAudioId = {
        partOne: 'audioPlayerPartOne',
        partTwo: 'audioPlayerPartTwo',
        partThree: 'audioPlayerPartThree',
        partFour: 'audioPlayerPartFour',
        partFive: 'audioPlayerPartFive',
        partSix: 'audioPlayerPartSix',
    };

    const state = {
        activeKey: null,
        activeAudio: null,
        isSeeking: false,

        loadedCount: 0,
        totalToLoad: 0,

        audioControl: 0, // 0 free, 1 locked (seek disabled + pause blocked)
        available: [],
        btnEls: [],
        activeIndex: 0,

        volume: 0.65, // 0..1

        // fully downloaded cache: key -> { url, blob, blobUrl, size }
        cache: new Map(),

        // countdown after ended
        endedCountdownTimer: null,
        endedCountdownRemaining: 0,

        // last-ended coloring
        shouldMarkRedAfterCountdown: false,
        endedPartIndexSnapshot: null,
        completedParts: new Set(), // stores partKey: 'partOne'...'partSix'

    };

    function clamp01(n) {
        const x = Number(n);
        if (!Number.isFinite(x)) return 0;
        return Math.max(0, Math.min(1, x));
    }

    // -----------------------------
    // audioTime color helpers
    // -----------------------------
    function audioTimeEl() { return $('audioTime'); }

    function setAudioTimeNormal() {
        const el = audioTimeEl();
        if (!el) return;
        el.classList.remove('text-red-600');
        el.classList.add('text-gray-600');
    }

    function setAudioTimeRed() {
        const el = audioTimeEl();
        if (!el) return;
        el.classList.remove('text-gray-600');
        el.classList.add('text-red-600');
    }

    // -----------------------------
    // Countdown UI (no layout impact)
    // -----------------------------
    function countdownEls() {
        return {
            wrap: $('audioNextCountdown'),
            sec: $('audioNextCountdownSec'),
        };
    }

    function hideEndedCountdown() {
        const { wrap } = countdownEls();
        if (wrap) wrap.classList.add('hidden');
    }

    function setEndedCountdownSeconds(n) {
        const { sec } = countdownEls();
        if (sec) sec.textContent = String(Math.max(0, Math.floor(n || 0)));
    }

    function clearEndedCountdown() {
        if (state.endedCountdownTimer) {
            clearInterval(state.endedCountdownTimer);
            state.endedCountdownTimer = null;
        }
        state.endedCountdownRemaining = 0;
        hideEndedCountdown();

        // cancel red-mark request too (user interacted)
        state.shouldMarkRedAfterCountdown = false;
        state.endedPartIndexSnapshot = null;
    }

    function startEndedCountdown(seconds, onDone) {
        // do NOT clear the red-mark flags here
        if (state.endedCountdownTimer) {
            clearInterval(state.endedCountdownTimer);
            state.endedCountdownTimer = null;
        }

        state.endedCountdownRemaining = Math.max(0, Math.floor(seconds || 0));
        setEndedCountdownSeconds(state.endedCountdownRemaining);

        const { wrap } = countdownEls();
        if (wrap) wrap.classList.remove('hidden');

        state.endedCountdownTimer = setInterval(() => {
            state.endedCountdownRemaining -= 1;
            setEndedCountdownSeconds(state.endedCountdownRemaining);

            if (state.endedCountdownRemaining <= 0) {
                if (state.endedCountdownTimer) clearInterval(state.endedCountdownTimer);
                state.endedCountdownTimer = null;
                hideEndedCountdown();

                try { onDone?.(); } catch (_) { }
            }
        }, 1000);
    }

    // -----------------------------
    // Overlay loading
    // -----------------------------
    function overlayShow(total) {
        const ov = $('audio-loading-overlay');
        const pr = $('audio-loading-progress');
        if (pr) pr.textContent = `0/${total}`;
        if (ov) ov.classList.remove('hidden');
    }

    function overlayUpdate(loaded, total, extraText = '') {
        const pr = $('audio-loading-progress');
        if (pr) pr.textContent = `${loaded}/${total}${extraText ? ' — ' + extraText : ''}`;
    }

    function overlayHide() {
        const ov = $('audio-loading-overlay');
        if (ov) ov.classList.add('hidden');
    }

    // -----------------------------
    // UI basics
    // -----------------------------
    function setIcons(isPlaying) {
        const playIcon = $('playIcon');
        const pauseIcon = $('pauseIcon');
        if (!playIcon || !pauseIcon) return;
        playIcon.classList.toggle('hidden', isPlaying);
        pauseIcon.classList.toggle('hidden', !isPlaying);
    }

    function resetUI() {
        const prog = $('audioProgress');
        const t = $('audioTime');
        if (prog) prog.value = '0';
        if (t) t.textContent = '0:00';
        setIcons(false);
    }

    function applyControlToUI() {
        const progress = $('audioProgress');
        const playBtn = $('audioPlayBtn');

        const mock = isMockMode(); // hidden is_mock dan o'qiydi
        const locked = (state.audioControl === 1) || mock;

        // ✅ SEEK disable (mock/lock)
        if (locked) {
            if (progress) {
                progress.disabled = true;
                progress.classList.add('opacity-60', 'cursor-not-allowed');
            }
            if (playBtn) playBtn.title = mock ? 'Mock mode: audio locked' : 'Audio is locked';
        } else {
            if (progress) {
                progress.disabled = false;
                progress.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            if (playBtn) playBtn.title = '';
        }
    }

    function setAllAudiosVolume(vol01) {
        const v = clamp01(vol01);
        state.volume = v;

        Object.values(partKeyToAudioId).forEach((audioId) => {
            const a = $(audioId);
            if (!a) return;
            try { a.volume = v; } catch (_) { }
        });
    }

    function syncVolumeSliderUI() {
        const slider = $('audioVolume');
        if (!slider) return;
        slider.value = String(Math.round(clamp01(state.volume) * 100));
    }

    function bindVolumeSlider() {
        const slider = $('audioVolume');
        if (!slider) return;

        slider.addEventListener('input', () => {
            const pct = Number(slider.value || 0);
            const v = clamp01(pct / 100);

            setAllAudiosVolume(v);

            if (typeof window.getListeningAudioDraft === 'function' && typeof window.setListeningAudioDraft === 'function') {
                const d = window.getListeningAudioDraft();
                d.volume = v;
                window.setListeningAudioDraft(d);
            }
        });
    }

    function updateTime() {
        const audio = state.activeAudio;
        if (!audio) return;

        const t = $('audioTime');
        const prog = $('audioProgress');

        if (t) t.textContent = formatTime(audio.currentTime);

        if (!prog || state.isSeeking) return;

        const dur = audio.duration || 0;
        if (dur > 0) {
            const pct = Math.min(100, Math.max(0, (audio.currentTime / dur) * 100));
            prog.value = String(pct);
        } else {
            prog.value = '0';
        }
    }

    function bindControls() {
        const playBtn = $('audioPlayBtn');
        const progress = $('audioProgress');

        if (playBtn) {
            playBtn.addEventListener('click', async () => {
                if (!state.activeAudio) return;
                if (!state.activeAudio.src) return;

                // user interaction => countdown cancel
                clearEndedCountdown();

                const mock = isMockMode();

                // ✅ MOCK: pause yo'q. Faqat play.
                if (mock) {
                    try { await state.activeAudio.play(); setIcons(true); } catch (_) { setIcons(false); }
                    return;
                }

                // ✅ locked mode: pause yo'q (audio_control=1)
                if (state.audioControl === 1) {
                    try { await state.activeAudio.play(); setIcons(true); } catch (_) { }
                    return;
                }

                // ✅ normal mode
                if (state.activeAudio.paused) {
                    try { await state.activeAudio.play(); setIcons(true); } catch (_) { setIcons(false); }
                } else {
                    state.activeAudio.pause();
                    setIcons(false);
                }
            });
        }

        if (progress) {
            progress.addEventListener('input', () => {
                // user interaction => countdown cancel
                clearEndedCountdown();

                // ✅ MOCK yoki LOCK: seek yo'q
                if (isMockMode() || state.audioControl === 1) return;

                state.isSeeking = true;
            });

            progress.addEventListener('change', () => {
                // ✅ MOCK yoki LOCK: seek yo'q
                if (isMockMode() || state.audioControl === 1) {
                    state.isSeeking = false;
                    return;
                }

                if (!state.activeAudio || !state.activeAudio.duration) {
                    state.isSeeking = false;
                    return;
                }
                const pct = Number(progress.value || 0) / 100;
                state.activeAudio.currentTime = pct * state.activeAudio.duration;
                state.isSeeking = false;
            });
        }

        bindVolumeSlider();
    }

    // -----------------------------
    // Blob full-download cache
    // -----------------------------
    function getFileNameFromUrl(url, fallback = 'audio.mp3') {
        try {
            const u = new URL(url, window.location.origin);
            const p = u.pathname.split('/').filter(Boolean);
            const last = p[p.length - 1] || fallback;
            return last.includes('.') ? last : fallback;
        } catch (_) {
            const parts = String(url).split('?')[0].split('/').filter(Boolean);
            const last = parts[parts.length - 1] || fallback;
            return last.includes('.') ? last : fallback;
        }
    }

    async function fetchAsBlobWithProgress(url, onProgress) {
        const res = await fetch(url, { cache: 'force-cache' });
        if (!res.ok) throw new Error(`Fetch failed ${res.status} for ${url}`);

        const total = Number(res.headers.get('content-length') || 0);

        if (res.body && typeof ReadableStream !== 'undefined') {
            const reader = res.body.getReader();
            const chunks = [];
            let received = 0;

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                chunks.push(value);
                received += value.byteLength;

                if (typeof onProgress === 'function' && total > 0) {
                    const pct = Math.floor((received / total) * 100);
                    onProgress({ received, total, pct });
                }
            }

            const blob = new Blob(chunks, { type: res.headers.get('content-type') || 'audio/mpeg' });
            return { blob, size: received || blob.size };
        }

        const blob = await res.blob();
        return { blob, size: blob.size };
    }

    async function ensureDownloadedToCache(partKey, url, progressCb) {
        const existing = state.cache.get(partKey);
        if (existing && existing.url === url && existing.blobUrl) return existing;

        if (existing?.blobUrl) {
            try { URL.revokeObjectURL(existing.blobUrl); } catch (_) { }
        }

        const { blob, size } = await fetchAsBlobWithProgress(url, progressCb);
        const blobUrl = URL.createObjectURL(blob);

        const entry = { url, blob, blobUrl, size };
        state.cache.set(partKey, entry);
        return entry;
    }

    function applyCachedBlobToAudioEl(partKey) {
        const audioId = partKeyToAudioId[partKey];
        const audio = audioId ? $(audioId) : null;
        const cached = state.cache.get(partKey);

        if (!audio || !cached?.blobUrl) return;

        audio.preload = 'auto';
        audio.src = cached.blobUrl;
        try { audio.load(); } catch (_) { }
    }

    // -----------------------------
    // playback helpers
    // -----------------------------
    function stopAllAudios() {
        clearEndedCountdown();

        Object.values(partKeyToAudioId).forEach((audioId) => {
            const a = $(audioId);
            if (!a) return;
            try { a.pause(); a.currentTime = 0; } catch (_) { }
        });

        setIcons(false);
        resetUI();
    }

    function getFirstUncompletedIndex() {
        const av = Array.isArray(state.available) ? state.available : [];
        for (let i = 0; i < av.length; i++) {
            const key = av[i]?.key;
            if (key && !state.completedParts.has(key)) return i;
        }
        return -1;
    }

    function isMockMode() {
        return getHiddenNumber('is_mock', 0) == 1;
    }

    function attachActiveListeners(audio) {
        audio.onloadedmetadata = null;
        audio.ontimeupdate = null;
        audio.onended = null;
        audio.onpause = null;

        audio.onloadedmetadata = () => updateTime();
        audio.ontimeupdate = () => updateTime();

        // ✅ MOCK yoki audio_control=1: pause bo'lmasin
        audio.onpause = async () => {
            const mock = isMockMode();
            if (!(mock || state.audioControl === 1)) return;
            if (audio.ended) return;

            try { await audio.play(); setIcons(true); } catch (_) {}
        };

        audio.onended = () => {
            setIcons(false);
            const prog = $('audioProgress');
            if (prog) prog.value = '0';

            const snapshotIndex = state.activeIndex;
            const snapshotPartKey = state.activeKey;

            // ✅ tugagan part belgilansin
            if (snapshotPartKey) state.completedParts.add(snapshotPartKey);

            // ✅ non-mock: hech qanaqa auto-next / countdown yo'q
            if (!isMockMode()) {
                return;
            }

            // ✅ mock: countdown + red + auto next / finish modal
            state.shouldMarkRedAfterCountdown = true;
            state.endedPartIndexSnapshot = snapshotIndex;

            startEndedCountdown(15, () => {
                // user boshqa partga o'tib ketgan bo'lsa bekor
                if (state.activeIndex !== snapshotIndex) return;

                // countdown tugadi => qizil
                if (state.shouldMarkRedAfterCountdown && state.endedPartIndexSnapshot === snapshotIndex) {
                    setAudioTimeRed();
                }

                const nextIndex = (snapshotIndex ?? 0) + 1;
                const hasNext = Array.isArray(state.available) && nextIndex < state.available.length;

                // ✅ keyingi part bo'lsa -> auto next
                if (hasNext) {
                    window.setActive?.(nextIndex, state.available, state.btnEls, { force: true });
                    return;
                }

                // ✅ oxirgi part -> finish modal auto open
                if (typeof window.openListeningFinishModal === 'function') {
                    window.openListeningFinishModal();
                    return;
                }

                // fallback
                if (typeof window.finishListeningTest === 'function') {
                    window.finishListeningTest();
                } else {
                    const form = document.getElementById('listening-form');
                    if (form) form.submit();
                }
            });
        };
    }


    // -----------------------------
    // Public API
    // -----------------------------
    async function setActivePart(partKey, autoPlay = true, ctx = {}) {
        // yangi part boshlangan zahoti audioTime normal bo‘lsin
        setAudioTimeNormal();

        clearEndedCountdown();

        state.audioControl = Number(ctx.audioControl ?? state.audioControl) === 1 ? 1 : 0;
        state.available = Array.isArray(ctx.available) ? ctx.available : state.available;
        state.btnEls = Array.isArray(ctx.btnEls) ? ctx.btnEls : state.btnEls;
        state.activeIndex = Number.isFinite(ctx.activeIndex) ? ctx.activeIndex : state.activeIndex;

        applyControlToUI();

        const audioId = partKeyToAudioId[partKey];
        const audio = audioId ? $(audioId) : null;

        if (!audio) {
            state.activeKey = partKey;
            state.activeAudio = null;
            resetUI();
            return;
        }

        stopAllAudios();

        state.activeKey = partKey;
        state.activeAudio = audio;

        try { audio.volume = clamp01(state.volume); } catch (_) { }

        attachActiveListeners(audio);
        resetUI();

        if (autoPlay) {
            const mock = isMockMode();

            // ✅ MOCK: autoplayni imkon boricha ishlatamiz (muted -> play -> unmute + retry)
            if (mock) {
                try {
                    // 1) muted trick
                    const prevMuted = audio.muted;
                    audio.muted = true;

                    await audio.play();
                    setIcons(true);

                    // 2) unmute qaytarish
                    setTimeout(() => {
                        try { audio.muted = prevMuted; } catch (_) {}
                    }, 150);

                } catch (err) {
                    setIcons(false);

                    // 3) retry (ba'zan DOM/overlay sabab kechikadi)
                    setTimeout(async () => {
                        try {
                            await audio.play();
                            setIcons(true);
                        } catch (_) {
                            setIcons(false);
                            // bu holatda user play bosishi kerak bo'ladi (browser policy)
                        }
                    }, 400);
                }

                return;
            }

            // ✅ NORMAL
            try { await audio.play(); setIcons(true); }
            catch (_) { setIcons(false); }
        } else {
            setIcons(false);
        }
    }

    /**
     * initPartAudios
     * - audio'larni to‘liq yuklab (blob) oladi
     * - overlay faqat hammasi tugagach yopiladi (timeout YO‘Q)
     * - so‘ng audio elementlarga blob: src beriladi
     */
    async function initPartAudios(available, partsData, cfg = {}) {
        state.completedParts = new Set();
        state.audioControl = Number(cfg.audioControl ?? 0) === 1 ? 1 : 0;
        state.available = Array.isArray(cfg.available) ? cfg.available : available;

        if (typeof cfg.volume === 'number') state.volume = clamp01(cfg.volume);

        applyControlToUI();
        setAllAudiosVolume(state.volume);
        syncVolumeSliderUI();

        const partKeys = (Array.isArray(available) ? available : []).map(a => a.key);
        const downloadList = [];

        partKeys.forEach((k) => {
            const url = extractAudioUrl(partsData?.[k]);
            if (url) downloadList.push({ key: k, url });
        });

        state.totalToLoad = downloadList.length;
        state.loadedCount = 0;

        if (state.totalToLoad > 0) overlayShow(state.totalToLoad);
        overlayUpdate(0, state.totalToLoad);

        // sequential download (stable + serverga yengil)
        for (const item of downloadList) {
            const { key, url } = item;
            const fileName = getFileNameFromUrl(url, key);

            try {
                await ensureDownloadedToCache(key, url, (p) => {
                    const extra = (p?.total > 0 && Number.isFinite(p?.pct))
                        ? `${fileName} ${p.pct}%`
                        : `${fileName}`;
                    overlayUpdate(state.loadedCount, state.totalToLoad, extra);
                });

                applyCachedBlobToAudioEl(key);
            } catch (err) {
                console.error('[Listening Audio] download failed:', key, url, err);
                // davom etamiz
            } finally {
                state.loadedCount++;
                overlayUpdate(state.loadedCount, state.totalToLoad);
            }
        }

        if (state.totalToLoad > 0) overlayHide();
    }

    // init volume from storage
    document.addEventListener('DOMContentLoaded', () => {
        // audioTime default normal
        setAudioTimeNormal();

        if (typeof window.getListeningAudioDraft === 'function') {
            const d = window.getListeningAudioDraft();
            if (typeof d?.volume === 'number') state.volume = clamp01(d.volume);
        }
        setAllAudiosVolume(state.volume);
        syncVolumeSliderUI();
    });

    // cleanup blob urls
    window.addEventListener('beforeunload', () => {
        try {
            state.cache.forEach((v) => {
                if (v?.blobUrl) URL.revokeObjectURL(v.blobUrl);
            });
        } catch (_) { }
    });

    window.PartAudio = {
        bindControls,
        initPartAudios,
        setActivePart,
        stopAllAudios,
        setAllAudiosVolume,
        _cache: state.cache,
    };
})();
