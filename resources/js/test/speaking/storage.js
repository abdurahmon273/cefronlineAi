/* storage.js - speaking
   - draft structure
   - localStorage blob (base64) chunk save/load
   - MediaRecorder wrapper
*/

window.getSpeakingDraft = function () {
    try { return JSON.parse(localStorage.getItem(window.STORAGE_KEY)) || {}; }
    catch (_) { return {}; }
};

window.setSpeakingDraft = function (draft) {
    try { localStorage.setItem(window.STORAGE_KEY, JSON.stringify(draft || {})); }
    catch (e) { console.error('setSpeakingDraft failed', e); }
};

window.ensureSpeakingDraftBase = function () {
    const d = window.getSpeakingDraft();

    if (!d.version) d.version = 2; // ✅ NEW VERSION
    if (!d.exercise_id) d.exercise_id = window.__SPEAKING__?.exerciseId ?? 0;
    if (!d.created_at) d.created_at = new Date().toISOString();
    if (typeof d.current_index !== 'number') d.current_index = 0;

    // ✅ NEW STRUCTURE
    if (!d.part_one) d.part_one = {};
    if (!Array.isArray(d.part_one.one_first_question)) d.part_one.one_first_question = [];
    if (!Array.isArray(d.part_one.one_second_question)) d.part_one.one_second_question = [];

    if (!d.part_two) d.part_two = {};
    if (!d.part_two.question) d.part_two.question = null; // {id, seq}

    if (!d.part_three) d.part_three = null; // {id, seq}

    window.setSpeakingDraft(d);
    return d;
};

window.getSpeakingCurrentIndex = function () {
    const d = window.getSpeakingDraft();
    return Number(d?.current_index ?? 0);
};
window.setSpeakingCurrentIndex = function (idx) {
    const d = window.ensureSpeakingDraftBase();
    d.current_index = Number(idx || 0);
    window.setSpeakingDraft(d);
};

window.clearSpeakingDraft = async function () {
    // remove main draft
    try { localStorage.removeItem(window.STORAGE_KEY); } catch (_) {}

    // ✅ IndexedDB blobs cleanup
    try {
        await window.idbClearRecordingsByStorageKey?.(window.STORAGE_KEY);
    } catch (e) {
        console.warn('idbClearRecordingsByStorageKey failed', e);
    }
};

// --- base64 helpers ---
window.blobToBase64 = function (blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onerror = reject;
        reader.onload = () => {
            const res = reader.result || '';
            // "data:audio/webm;base64,...."
            const str = String(res);
            const commaIdx = str.indexOf(',');
            resolve(commaIdx >= 0 ? str.slice(commaIdx + 1) : str);
        };
        reader.readAsDataURL(blob);
    });
};

window.base64ToBlob = function (b64, mime) {
    const byteChars = atob(b64);
    const arr = new Uint8Array(byteChars.length);
    for (let i = 0; i < byteChars.length; i++) arr[i] = byteChars.charCodeAt(i);
    return new Blob([arr], { type: mime || 'audio/webm' });
};

window.saveBlobChunks = async function (seq, blob) {
    const mime = blob?.type || 'audio/webm';
    const base64 = await window.blobToBase64(blob);

    const CHUNK_SIZE = 700_000; // ~0.7MB text
    const total = Math.ceil(base64.length / CHUNK_SIZE);

    const metaKey = `${window.STORAGE_KEY}-blob-${seq}-meta`;
    const chunkPrefix = `${window.STORAGE_KEY}-blob-${seq}-chunk-`;

    // clear old if exists
    try {
        localStorage.removeItem(metaKey);
        for (let i = 0; i < 9999; i++) {
            const k = chunkPrefix + i;
            if (localStorage.getItem(k) == null) break;
            localStorage.removeItem(k);
        }
    } catch (_) {}

    // write chunks
    try {
        for (let i = 0; i < total; i++) {
            const part = base64.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE);
            localStorage.setItem(chunkPrefix + i, part);
        }
        localStorage.setItem(metaKey, JSON.stringify({ mime, total, size: blob.size, saved_at: new Date().toISOString() }));
    } catch (e) {
        console.error('saveBlobChunks quota error', e);
        // best-effort: cleanup partial
        try {
            localStorage.removeItem(metaKey);
            for (let i = 0; i < total; i++) localStorage.removeItem(chunkPrefix + i);
        } catch (_) {}
        throw e;
    }

    return { mime, total, size: blob.size };
};

window.loadBlobChunks = function (seq) {
    const metaKey = `${window.STORAGE_KEY}-blob-${seq}-meta`;
    const chunkPrefix = `${window.STORAGE_KEY}-blob-${seq}-chunk-`;
    const metaRaw = localStorage.getItem(metaKey);
    if (!metaRaw) return null;

    let meta;
    try { meta = JSON.parse(metaRaw); } catch (_) { return null; }

    const total = Number(meta.total || 0);
    if (!total) return null;

    let base64 = '';
    for (let i = 0; i < total; i++) {
        const part = localStorage.getItem(chunkPrefix + i);
        if (part == null) return null;
        base64 += part;
    }
    return window.base64ToBlob(base64, meta.mime || 'audio/webm');
};

// --- recorder wrapper (FIXED: timeslice + requestData + final chunk wait) ---
window.SpeakingRecorder = window.SpeakingRecorder || {
    stream: null,
    mediaRecorder: null,
    chunks: [],
    mime: 'audio/webm',
    isRecording: false,

    async _getStream() {
        if (this.stream) return this.stream;
        // ✅ speech uchun mono — hajm kichik, upload tez, AI transkripsiya aniqroq
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    channelCount: 1,        // mono (stereo bo'lsa ~2x tejaladi)
                    echoCancellation: true, // ovoz aks-sadosini kamaytiradi
                    noiseSuppression: true, // shovqinni bosadi -> AI aniqroq
                },
            });
        } catch (_) {
            // eski / qo'llab-quvvatlamaydigan qurilmalar uchun fallback
            this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        }
        return this.stream;
    },

    async start() {
        if (this.isRecording) return;

        const stream = await this._getStream();

        this.chunks = [];
        const options = {};

        // ✅ best compatibility
        if (MediaRecorder.isTypeSupported?.('audio/webm;codecs=opus')) {
            this.mime = 'audio/webm;codecs=opus';
            options.mimeType = this.mime;
        } else if (MediaRecorder.isTypeSupported?.('audio/webm')) {
            this.mime = 'audio/webm';
            options.mimeType = this.mime;
        } else if (MediaRecorder.isTypeSupported?.('audio/ogg;codecs=opus')) {
            // ✅ some browsers
            this.mime = 'audio/ogg;codecs=opus';
            options.mimeType = this.mime;
        } else {
            this.mime = '';
        }

        // ✅ speech uchun 48kbps yetarli — hajm ~2x kichik, upload tezroq
        options.audioBitsPerSecond = 24000;

        this.mediaRecorder = new MediaRecorder(stream, options);

        this.mediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) {
                this.chunks.push(e.data);
            }
        };

        this.isRecording = true;

        // ✅ TIMESLICE: har 1000ms dataavailable chiqadi (0 chunk muammosini kesadi)
        try {
            this.mediaRecorder.start(1000);
        } catch (e) {
            // fallback
            this.mediaRecorder.start();
        }
    },

    stop({ timeoutMs = 2500 } = {}) {
        return new Promise((resolve) => {
            if (!this.mediaRecorder || !this.isRecording) {
                resolve(null);
                return;
            }

            const mr = this.mediaRecorder;

            let done = false;
            const finish = (blob) => {
                if (done) return;
                done = true;

                try { mr.ondataavailable = null; } catch (_) {}
                try { mr.onstop = null; } catch (_) {}
                try { mr.onerror = null; } catch (_) {}

                this.isRecording = false;

                if (!blob) {
                    // final build
                    blob = new Blob(this.chunks || [], { type: this.mime || 'audio/webm' });
                }

                // reset chunks after blob created
                this.chunks = [];

                // ✅ if empty => null (so we can show error / retry)
                if (!blob || !blob.size) return resolve(null);

                resolve(blob);
            };

            const onStop = () => {
                const blob = new Blob(this.chunks || [], { type: this.mime || 'audio/webm' });
                finish(blob);
            };

            const onErr = () => finish(null);

            mr.onstop = onStop;
            mr.onerror = onErr;

            // ✅ request final data before stop (some browsers need this)
            try { mr.requestData?.(); } catch (_) {}

            // ✅ stop
            try { mr.stop(); } catch (_) { finish(null); }

            // ✅ hard timeout (if onstop never fires)
            setTimeout(() => finish(null), timeoutMs);
        });
    }
};

// --- save recording per queue item ---
window.upsertArrayById = function (arr, rec) {
    const a = Array.isArray(arr) ? arr : [];
    const idx = a.findIndex(x => Number(x?.id) === Number(rec?.id));
    if (idx >= 0) a[idx] = rec;
    else a.push(rec);
    return a;
};

window.saveSpeakingRecordingForItem = async function (item, blob) {
    const d = window.ensureSpeakingDraftBase();

    const seq = Number(item?.seq || 0);
    if (!seq || !blob) throw new Error('Invalid seq/blob');

    // ✅ save blob to IndexedDB (no base64)
    await window.idbSaveRecording?.(window.STORAGE_KEY, seq, blob);

    const qid = Number(item.questionId || 0);
    const partNum = Number(item.partNum || 0);
    const partSub = (item.partSub || '').toString();

    // ✅ PART 1
    if (partNum === 1) {
        const rec = { id: qid, seq };

        if (partSub === 'one_second') {
            const arr = d.part_one.one_second_question;
            const idx = arr.findIndex(x => Number(x?.id) === qid);
            if (idx >= 0) arr[idx] = rec; else arr.push(rec);
        } else {
            const arr = d.part_one.one_first_question;
            const idx = arr.findIndex(x => Number(x?.id) === qid);
            if (idx >= 0) arr[idx] = rec; else arr.push(rec);
        }
    }
    // ✅ PART 2
    else if (partNum === 2) {
        d.part_two.question = { id: qid, seq };
    }
    // ✅ PART 3
    else if (partNum === 3) {
        d.part_three = { id: qid, seq };
    }

    d.last_saved_at = new Date().toISOString();
    window.setSpeakingDraft(d);
};

window.getTextSizeDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)) || {}; }
    catch { return {}; }
};

// ===================== AUDIO PRELOAD + CACHE =====================

window.__SPEAKING_AUDIO_CACHE__ = window.__SPEAKING_AUDIO_CACHE__ || new Map();

/**
 * Overlay helpers
 */
window.showAudioLoadingOverlay = function () {
    const el = document.getElementById('audio-loading-overlay');
    if (el) el.classList.remove('hidden');
};
window.hideAudioLoadingOverlay = function () {
    const el = document.getElementById('audio-loading-overlay');
    if (el) el.classList.add('hidden');
};
window.setAudioLoadingProgress = function (done, total) {
    const el = document.getElementById('audio-loading-progress');
    if (el) el.textContent = `${done}/${total}`;
};

/**
 * Get or create cached Audio instance
 */
window.getCachedAudio = function (url) {
    const u = (url || '').toString().trim();
    if (!u) return null;

    const abs = window.resolveAssetUrl ? window.resolveAssetUrl(u) : u;

    if (window.__SPEAKING_AUDIO_CACHE__.has(abs)) {
        return window.__SPEAKING_AUDIO_CACHE__.get(abs);
    }

    const a = new Audio(abs);
    a.preload = 'auto';
    a.crossOrigin = 'anonymous'; // safe (local assets bo'lsa ham zarar qilmaydi)
    window.__SPEAKING_AUDIO_CACHE__.set(abs, a);
    return a;
};

/**
 * Preload single audio url
 */
window.preloadSingleAudioUrl = function (url, timeoutMs = 15000) {
    return new Promise((resolve) => {
        const audio = window.getCachedAudio(url);
        if (!audio || !audio.src) return resolve({ ok: false, url });

        // already loaded enough
        try {
            if (audio.readyState >= 3) return resolve({ ok: true, url: audio.src });
        } catch (_) {}

        let done = false;
        const finish = (ok) => {
            if (done) return;
            done = true;
            audio.removeEventListener('canplaythrough', onOk);
            audio.removeEventListener('loadeddata', onOk);
            audio.removeEventListener('error', onErr);
            resolve({ ok, url: audio.src });
        };

        const onOk = () => finish(true);
        const onErr = () => finish(false);

        audio.addEventListener('canplaythrough', onOk, { once: true });
        audio.addEventListener('loadeddata', onOk, { once: true });
        audio.addEventListener('error', onErr, { once: true });

        try { audio.load(); } catch (_) {}

        setTimeout(() => finish(audio.readyState >= 2), timeoutMs);
    });
};

/**
 * Collect all speaking audios:
 * - tingVoiseLink
 * - instructionAudioLink for each available part
 * - question audio_link for every question
 */
window.collectSpeakingAudioUrls = function (partsData) {
    const urls = new Set();

    const add = (u) => {
        const s = (u || '').toString().trim();
        if (s) urls.add(s);
    };

    const aInst = partsData?.audioInstructionsData;
    add(aInst?.tingVoiseLink);

    // Part 1.1
    const p11 = partsData?.partOneFirst;
    add(p11?.instructionAudioLink);
    (p11?.questions || []).forEach(q => add(q?.audio_link));

    // Part 1.2
    const p12 = partsData?.partOneSecond;
    add(p12?.instructionAudioLink);
    (p12?.questions || []).forEach(q => add(q?.audio_link));

    // Part 2
    const p2 = partsData?.partTwo;
    add(p2?.instructionAudioLink);
    add(p2?.question?.audio_link);

    // Part 3
    const p3 = partsData?.partThree;
    add(p3?.instructionAudioLink);
    add(p3?.audio_link);

    return Array.from(urls);
};

/**
 * Main preload function (modal with progress)
 */
window.preloadSpeakingAudios = async function (partsData) {
    const urls = window.collectSpeakingAudioUrls(partsData || {});
    const total = urls.length;

    if (!total) return { total: 0, done: 0 };

    window.showAudioLoadingOverlay?.();
    window.setAudioLoadingProgress?.(0, total);

    let done = 0;

    // sequential: barqarorroq (parallel ham bo'ladi, lekin bu xavfsiz)
    for (const u of urls) {
        try { await window.preloadSingleAudioUrl(u); } catch (_) {}
        done += 1;
        window.setAudioLoadingProgress?.(done, total);
    }

    window.hideAudioLoadingOverlay?.();
    return { total, done };
};
