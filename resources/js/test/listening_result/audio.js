// resources/js/test/listening_result/audio.js
(function () {
    function $(id) { return document.getElementById(id); }

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

    function normalizeUrl(raw) {
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

    function extractAudioUrl(partData) {
        if (!partData) return null;

        if (typeof partData === 'string') return normalizeUrl(partData);

        if (Array.isArray(partData)) {
            for (const item of partData) {
                const u = extractAudioUrl(item);
                if (u) return u;
            }
            return null;
        }

        if (typeof partData === 'object') {
            const direct =
                partData.audio_url ??
                partData.audio ??
                partData.audioPath ??
                partData.audio_path ??
                partData.file ??
                partData.url ??
                null;

            const u1 = normalizeUrl(direct);
            if (u1) return u1;

            const nestedCandidates = [
                partData.data,
                partData.meta,
                partData.part,
                partData.payload,
                partData.result,
                partData.exercise,
            ];

            for (const n of nestedCandidates) {
                const u = extractAudioUrl(n);
                if (u) return u;
            }
        }

        return null;
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
        volume: 0.65,

        // blob cache: partKey -> { url, blobUrl }
        cache: new Map(),

        // loading counter
        loadingTotal: 0,
        loadingDone: 0,
    };

    function clamp01(n) {
        const x = Number(n);
        if (!Number.isFinite(x)) return 0;
        return Math.max(0, Math.min(1, x));
    }

    // -----------------------------
    // Inline loading UI (panel ichida)
    // -----------------------------
    function showInlineLoading(total) {
        state.loadingTotal = total || 0;
        state.loadingDone = 0;

        const wrap = $('audioInlineLoading');
        const text = $('audioInlineLoadingText');
        if (text) text.textContent = total > 0 ? `Loading audio… 0/${total}` : 'Loading audio…';
        if (wrap) wrap.classList.remove('hidden');

        // UX: yuklanayotganda play/seek disable
        const play = $('audioPlayBtn');
        const prog = $('audioProgress');
        if (play) play.disabled = true;
        if (prog) prog.disabled = true;
    }

    function updateInlineLoading(done, total) {
        const text = $('audioInlineLoadingText');
        if (text) text.textContent = `Loading audio… ${done}/${total}`;
    }

    function hideInlineLoading() {
        const wrap = $('audioInlineLoading');
        if (wrap) wrap.classList.add('hidden');

        const play = $('audioPlayBtn');
        const prog = $('audioProgress');
        if (play) play.disabled = false;
        if (prog) prog.disabled = false;
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
        if (!slider || slider.dataset.bound) return;
        slider.dataset.bound = '1';

        slider.addEventListener('input', () => {
            const pct = Number(slider.value || 0);
            const v = clamp01(pct / 100);
            setAllAudiosVolume(v);
        });
    }

    function seekToProgressValue() {
        const progress = $('audioProgress');
        const audio = state.activeAudio;
        if (!progress || !audio) return;

        const dur = audio.duration;
        if (!dur || !Number.isFinite(dur) || dur <= 0) return;

        const pct = Number(progress.value || 0) / 100;
        const nextTime = pct * dur;

        try { audio.currentTime = nextTime; } catch (_) { }
    }

    function bindControls() {
        const playBtn = $('audioPlayBtn');
        const progress = $('audioProgress');

        if (playBtn && !playBtn.dataset.bound) {
            playBtn.dataset.bound = '1';
            playBtn.addEventListener('click', async () => {
                if (!state.activeAudio) return;
                if (!state.activeAudio.src) return;

                if (state.activeAudio.paused) {
                    try { await state.activeAudio.play(); setIcons(true); }
                    catch (_) { setIcons(false); }
                } else {
                    state.activeAudio.pause();
                    setIcons(false);
                }
            });
        }

        if (progress && !progress.dataset.bound) {
            progress.dataset.bound = '1';

            progress.addEventListener('input', () => {
                state.isSeeking = true;
                seekToProgressValue();
            });

            const endSeek = () => {
                seekToProgressValue();
                state.isSeeking = false;
            };

            progress.addEventListener('pointerup', endSeek);
            progress.addEventListener('change', endSeek);

            window.addEventListener('mouseup', endSeek);
            window.addEventListener('touchend', endSeek);
        }

        bindVolumeSlider();
    }

    function attachActiveListeners(audio) {
        audio.onloadedmetadata = null;
        audio.ontimeupdate = null;
        audio.onended = null;
        audio.onpause = null;

        audio.onloadedmetadata = () => updateTime();
        audio.ontimeupdate = () => updateTime();

        audio.onpause = () => setIcons(false);

        audio.onended = () => {
            setIcons(false);
            const prog = $('audioProgress');
            if (prog) prog.value = '0';
        };
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

    function stopAllAudios() {
        Object.values(partKeyToAudioId).forEach((audioId) => {
            const a = $(audioId);
            if (!a) return;
            try { a.pause(); } catch (_) { }
        });
        setIcons(false);
        resetUI();
    }

    async function setActivePart(partKey, autoPlay = false) {
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

        // RESULT page: autoplay ALWAYS false
        if (autoPlay) {
            try { await audio.play(); setIcons(true); }
            catch (_) { setIcons(false); }
        } else {
            setIcons(false);
        }
    }

    // -----------------------------
    // Blob download (no modal, inline only)
    // -----------------------------
    async function fetchAsBlob(url) {
        const res = await fetch(url, { cache: 'force-cache' });
        if (!res.ok) throw new Error(`Fetch failed ${res.status} for ${url}`);
        const blob = await res.blob();
        return blob;
    }

    async function ensureBlob(partKey, url) {
        const existing = state.cache.get(partKey);
        if (existing && existing.url === url && existing.blobUrl) return existing;

        if (existing?.blobUrl) {
            try { URL.revokeObjectURL(existing.blobUrl); } catch (_) {}
        }

        const blob = await fetchAsBlob(url);
        const blobUrl = URL.createObjectURL(blob);
        const entry = { url, blobUrl };
        state.cache.set(partKey, entry);
        return entry;
    }

    // IMPORTANT: this MUST be called on result page
    async function initPartAudios(available, partsData, cfg = {}) {
        if (typeof cfg.volume === 'number') state.volume = clamp01(cfg.volume);

        setAllAudiosVolume(state.volume);
        syncVolumeSliderUI();

        const list = (available || [])
            .map(a => a?.key)
            .filter(Boolean)
            .map((key) => {
                const url = extractAudioUrl(partsData?.[key]);
                return url ? { key, url } : null;
            })
            .filter(Boolean);

        // show inline loading if there is something to load
        if (list.length > 0) showInlineLoading(list.length);

        // sequential download (yengil + stabil)
        for (let i = 0; i < list.length; i++) {
            const { key, url } = list[i];

            try {
                const cached = await ensureBlob(key, url);

                const audioId = partKeyToAudioId[key];
                const audio = audioId ? $(audioId) : null;
                if (audio && cached?.blobUrl) {
                    audio.preload = 'auto';
                    audio.src = cached.blobUrl;
                    try { audio.load(); } catch (_) {}
                }
            } catch (err) {
                console.error('[Listening Result Audio] download failed:', key, url, err);
                // davom etamiz
            } finally {
                state.loadingDone += 1;
                updateInlineLoading(state.loadingDone, state.loadingTotal);
            }
        }

        if (list.length > 0) hideInlineLoading();
    }

    // cleanup blob urls
    window.addEventListener('beforeunload', () => {
        try {
            state.cache.forEach((v) => {
                if (v?.blobUrl) URL.revokeObjectURL(v.blobUrl);
            });
        } catch (_) { }
    });

    document.addEventListener('DOMContentLoaded', () => {
        setAllAudiosVolume(state.volume);
        syncVolumeSliderUI();
    });

    window.PartAudio = {
        bindControls,
        initPartAudios,
        setActivePart,
        stopAllAudios,
        setAllAudiosVolume,
    };
})();
