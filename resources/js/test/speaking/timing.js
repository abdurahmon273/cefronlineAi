/* timing.js - speaking exam flow
   Audio (once) -> WAIT countdown -> RECORD countdown -> save -> next
*/

window.ensureMicPermissionOrReload = async function () {
    try {
        // bir marta permission so‘raymiz — speech uchun mono
        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    channelCount: 1,        // mono
                    echoCancellation: true,
                    noiseSuppression: true,
                },
            });
        } catch (_) {
            // eski qurilmalar uchun fallback
            stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        }

        // streamni recorder ga berib qo‘yamiz (keyin qayta so‘ramasin)
        window.SpeakingRecorder = window.SpeakingRecorder || {};
        window.SpeakingRecorder.stream = stream;

        return true;
    } catch (e) {
        const ok = confirm(
            "Microphone permission is required.\n\n" +
            "OK -> reload and try again\n" +
            "Cancel -> stop"
        );

        if (ok) location.reload();
        return false;
    }
};

window.__SP_TIMING__ = window.__SP_TIMING__ || {
    state: 'idle', // idle | audio | wait | record | done | stopped
    timerId: null,
    secondsLeft: 0,
    currentIndex: 0,
    running: false,
    recordStartedAt: 0,
};

window.__SPEAKING_INSTRUCTION_STATE__ = window.__SPEAKING_INSTRUCTION_STATE__ || {
    p11: false,
    p12: false,
    p2: false,
    p3: false,
};

window.isSpeakingMock = function () {
    try {
        const el = document.getElementById('is_mock');
        const v = (el?.value ?? '0').toString().trim();
        return Number(v) == 0;
    } catch (_) {
        return false;
    }
};
// Instruction key: qaysi itemga qaysi instruction tegishli?
window.getInstructionKeyForItem = function (item) {
    const partNum = Number(item?.partNum || 0);
    if (partNum === 1) {
        const set = (item?.part1Set || item?.partSub || '').toString();
        if (set === 'one_second') return 'p12';
        return 'p11'; // default one_first
    }
    if (partNum === 2) return 'p2';
    if (partNum === 3) return 'p3';
    return null;
};

window.getInstructionAudioUrlForKey = function (key) {
    const pd = window.__SPEAKING__?.partsData || {};
    if (key === 'p11') return pd?.partOneFirst?.instructionAudioLink || '';
    if (key === 'p12') return pd?.partOneSecond?.instructionAudioLink || '';
    if (key === 'p2')  return pd?.partTwo?.instructionAudioLink || '';
    if (key === 'p3')  return pd?.partThree?.instructionAudioLink || '';
    return '';
};

// Instruction o'ynatish: hech narsa ko'rsatmasdan
window.playInstructionIfNeededForItem = async function (item) {
    // ✅ MOCK bo'lsa instruction audio kerak emas
    if (window.isSpeakingMock?.()) {
        return false;
    }

    const key = window.getInstructionKeyForItem(item);
    if (!key) return false;

    if (window.__SPEAKING_INSTRUCTION_STATE__?.[key]) return false;

    const url = window.getInstructionAudioUrlForKey(key);
    if (!url) {
        window.__SPEAKING_INSTRUCTION_STATE__[key] = true;
        return false;
    }

    // ✅ Hamma savollarni yashiramiz (hech narsa ko'rinmasin)
    try { window.hideAllSpeakingQuestions?.(); } catch (_) {}

    // ✅ Timer UI: Instruction
    window.setTimerUI?.({
        status: 'audio',
        seconds: 0,
        sub: 'Instructions...',
        isRecording: false,
        questionLabel: 'Instruction'
    });

    await window.playAudioUrlOnce?.(url);

    window.__SPEAKING_INSTRUCTION_STATE__[key] = true;
    return true;
};

window.formatMMSS = function (sec) {
    const s = Math.max(0, Number(sec || 0));
    const mm = String(Math.floor(s / 60)).padStart(2, '0');
    const ss = String(s % 60).padStart(2, '0');
    return `${mm}:${ss}`;
};

window.setTimerUI = function ({ status, seconds, sub, isRecording, questionLabel }) {
    const timeBig = document.getElementById('timeBig');
    const statusText = document.getElementById('timerStatusText');
    const subText = document.getElementById('timerSubText');
    const dot = document.getElementById('recordDot');
    const ping = document.getElementById('recordPing');
    const qLabel = document.getElementById('timerQuestionLabel');

    if (timeBig) timeBig.textContent = window.formatMMSS(seconds ?? 0);
    if (statusText) statusText.textContent = (status ?? 'IDLE').toUpperCase();
    if (subText) subText.textContent = sub ?? '';
    if (qLabel) qLabel.textContent = questionLabel ?? 'Question -';

    // record dot styles
    if (dot) {
        dot.classList.remove('bg-gray-400', 'bg-yellow-500', 'bg-red-500', 'bg-green-500');
        if (isRecording) dot.classList.add('bg-red-500');
        else if ((status ?? '').toLowerCase() === 'wait') dot.classList.add('bg-yellow-500');
        else if ((status ?? '').toLowerCase() === 'done') dot.classList.add('bg-green-500');
        else dot.classList.add('bg-gray-400');
    }

    if (ping) {
        if (isRecording) ping.classList.remove('hidden');
        else ping.classList.add('hidden');
    }
    // ✅ Voice effect: faqat recording paytida
    try {
        window.setVoiceEffectRecording?.(!!isRecording);
    } catch (_) {}
};

window.clearTick = function () {
    const st = window.__SP_TIMING__;
    if (st.timerId) clearInterval(st.timerId);
    st.timerId = null;
};

// ========= NEW helpers for controls =========

window.getSpeakingItemByOrder = function (order) {
    const queue = window.__SPEAKING__?.queue || [];
    return queue.find(x => Number(x.globalOrder) === Number(order)) || null;
};

window.syncSpeakingControlsForItem = function (item, mode) {
    // mode: 'hide' | 'wait' | 'record'
    try { window.setSpeakingControlsMode?.(item?.controlsEl, mode); } catch (_) {}
};

window.speakingControlStartByOrder = function (order) {
    const st = window.__SP_TIMING__;
    const item = window.getSpeakingItemByOrder(order);
    if (!item) return;

    // faqat current item + wait holatida ishlasin
    const current = window.__SPEAKING__?.queue?.[st.currentIndex];
    if (!current || current.globalOrder !== item.globalOrder) return;
    if (st.state !== 'wait') return;

    // wait ni darhol tugatib recordga o‘tamiz
    window.clearTick();
    window.runRecordPhase(item);
};

window.speakingControlNextByOrder = async function (order) {
    const st = window.__SP_TIMING__;
    const item = window.getSpeakingItemByOrder(order);
    if (!item) return;

    const current = window.__SPEAKING__?.queue?.[st.currentIndex];
    if (!current || current.globalOrder !== item.globalOrder) return;

    // ✅ faqat record holatida ishlasin
    if (st.state !== 'record') return;

    // ✅ NEW: min 2 sekund record himoya
    const MIN_MS = 2000;
    const passed = Date.now() - Number(st.recordStartedAt || 0);
    const remain = MIN_MS - passed;

    if (remain > 0) {
        // UI lock (yana bir marta)
        try { window.lockSpeakingNextButtonsForMs?.(item.controlsEl, remain); } catch (_) {}
        // JS wait
        await window.sleepMs?.(remain);
    }

    window.clearTick();

    // ✅ oxirgi bo'lsa: Next o'rniga Finish ishlashi kerak
    const isLast = window.isSpeakingLastItem?.(item);
    if (isLast) {
        await window.finishRecordAndStayForFinish(item, { openModal: true });
        return;
    }

    await window.finishRecordAndGoNext(item);
};


window.finishRecordAndGoNext = async function (item) {
    if (!item) return;

    window.setTimerUI({
        status: 'record',
        seconds: 0,
        sub: 'Saving...',
        isRecording: true,
        questionLabel: `Question ${item.globalOrder}`
    });

    // buttons: yashiramiz
    window.syncSpeakingControlsForItem(item, 'hide');

    let blob = null;

    // stop recorder + save blob
    try {
        blob = await window.SpeakingRecorder?.stop?.();
    } catch (e) {
        console.error('Recorder stop failed', e);
    }

    if (!blob || !blob.size) {
        console.warn('Empty recording blob. Not saving.');
        // xohlasangiz userga alert:
        // alert('Audio recording failed (empty). Please try again.');
    } else {
        try { await window.saveSpeakingRecordingForItem(item, blob); } catch (e) { console.error(e); }
    }

    // ✅ record tugadi -> ting chalinsin (manual Next bosilganda)
    await window.playTingSound?.();

    window.gotoNextSpeakingItem();
};

window.isSpeakingLastItem = function (item) {
    const queue = window.__SPEAKING__?.queue || [];
    if (!queue.length) return false;
    const last = queue[queue.length - 1];
    return Number(last?.globalOrder) === Number(item?.globalOrder);
};

window.finishRecordAndStayForFinish = async function (item, opts = {}) {
    if (!item) return;

    const { openModal = false } = opts;

    window.setTimerUI({
        status: 'done',
        seconds: 0,
        sub: 'Saving...',
        isRecording: false,
        questionLabel: `Question ${item.globalOrder}`
    });

    // ✅ countdown bo'lsa to'xtatamiz
    window.clearTick();

    // stop recorder + save blob
    let blob = null;
    try {
        blob = await window.SpeakingRecorder?.stop?.();
    } catch (e) {
        console.error('Recorder stop failed', e);
    }

    if (blob) {
        try {
            await window.saveSpeakingRecordingForItem(item, blob);
        } catch (e) {
            console.error('Save recording failed', e);
        }
    }

    // ✅ record tugadi -> ting chalinsin (Finish bosilganda yoki last auto)
    await window.playTingSound?.();

    // ✅ oxirgi savolda: Finish tugma ko'rinib turadi
    window.syncSpeakingControlsForItem(item, 'finish');

    // ✅ exam running bo'lib turaversin (modal accept bilan yakunlaymiz)
    const st = window.__SP_TIMING__;
    st.state = 'done';

    window.setTimerUI({
        status: 'done',
        seconds: 0,
        sub: 'Recorded. Ready to finish.',
        isRecording: false,
        questionLabel: `Question ${item.globalOrder}`
    });

    // ✅ agar kerak bo'lsa modalni avtomatik ochamiz
    if (openModal) {
        window.openSpeakingFinishModal?.();
    }
};

window.speakingControlFinishByOrder = async function (order) {
    const st = window.__SP_TIMING__;
    const item = window.getSpeakingItemByOrder(order);
    if (!item) return;

    const current = window.__SPEAKING__?.queue?.[st.currentIndex];
    if (!current || current.globalOrder !== item.globalOrder) return;

    // ✅ agar RECORD paytida bosilsa: min 2s kutib keyin stop+save+modal
    if (st.state === 'record') {
        const MIN_MS = 1000;
        const passed = Date.now() - Number(st.recordStartedAt || 0);
        const remain = MIN_MS - passed;

        if (remain > 0) {
            try { window.lockSpeakingNextButtonsForMs?.(item.controlsEl, remain); } catch (_) {}
            await window.sleepMs?.(remain);
        }

        await window.finishRecordAndStayForFinish(item, { openModal: true });
        return;
    }

    // ✅ agar DONE bo'lsa: faqat modal
    window.openSpeakingFinishModal?.();
};
// ========= core controls =========

window.stopSpeakingExam = function () {
    const st = window.__SP_TIMING__;
    st.running = false;
    st.state = 'stopped';
    window.clearTick();

    // stop recorder if recording
    try { window.SpeakingRecorder?.stop?.(); } catch (_) {}

    // hide current controls
    try {
        const current = window.__SPEAKING__?.queue?.[st.currentIndex];
        if (current) window.syncSpeakingControlsForItem(current, 'hide');
    } catch (_) {}

    window.setTimerUI({
        status: 'stopped',
        seconds: 0,
        sub: 'Stopped',
        isRecording: false
    });
};

window.markActiveQuestion = function (el) {
    document.querySelectorAll('.sp-active-question').forEach(x => {
        x.classList.remove('sp-active-question');
    });
    if (!el) return;
    el.classList.add('sp-active-question');
};

window.scrollToQuestion = function (el) {
    if (!el) return;
    try {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } catch (_) { }
};

window.playQuestionAudioOnce = async function (audioEl) {
    if (!audioEl || !audioEl.src) return { played: false };

    try { audioEl.currentTime = 0; } catch (_) { }

    const waitMeta = () => new Promise(res => {
        if (audioEl.readyState >= 1) return res();
        const onMeta = () => {
            audioEl.removeEventListener('loadedmetadata', onMeta);
            audioEl.removeEventListener('canplay', onMeta);
            res();
        };
        audioEl.addEventListener('loadedmetadata', onMeta);
        audioEl.addEventListener('canplay', onMeta);
        setTimeout(res, 800);
    });

    await waitMeta();

    const waitEnded = () => new Promise(res => {
        let done = false;

        const finish = () => {
            if (done) return;
            done = true;
            audioEl.removeEventListener('ended', onEnded);
            audioEl.removeEventListener('error', onErr);
            audioEl.removeEventListener('timeupdate', onTime);
            res();
        };

        const onEnded = () => finish();
        const onErr = () => finish();

        const onTime = () => {
            const d = Number(audioEl.duration || 0);
            if (d && audioEl.currentTime >= d - 0.05) finish();
        };

        audioEl.addEventListener('ended', onEnded);
        audioEl.addEventListener('error', onErr);
        audioEl.addEventListener('timeupdate', onTime);

        const d = Number(audioEl.duration || 0);
        if (d && Number.isFinite(d) && d > 0) {
            setTimeout(finish, Math.ceil(d * 1000) + 300);
        } else {
            setTimeout(finish, 15000);
        }
    });

    try {
        await audioEl.play();
        await waitEnded();
        return { played: true };
    } catch (e) {
        console.warn('Audio play blocked, skipping audio phase.', e);
        return { played: false, error: e };
    }
};

window.playAudioUrlOnce = async function (url) {
    const audio = window.getCachedAudio?.(url);
    if (!audio || !audio.src) return { played: false };

    try { audio.currentTime = 0; } catch (_) {}

    const waitEnded = () => new Promise(res => {
        let done = false;

        const finish = () => {
            if (done) return;
            done = true;
            audio.removeEventListener('ended', onEnded);
            audio.removeEventListener('error', onErr);
            audio.removeEventListener('timeupdate', onTime);
            res();
        };

        const onEnded = () => finish();
        const onErr = () => finish();

        const onTime = () => {
            const d = Number(audio.duration || 0);
            if (d && audio.currentTime >= d - 0.05) finish();
        };

        audio.addEventListener('ended', onEnded);
        audio.addEventListener('error', onErr);
        audio.addEventListener('timeupdate', onTime);

        const d = Number(audio.duration || 0);
        if (d && Number.isFinite(d) && d > 0) {
            setTimeout(finish, Math.ceil(d * 1000) + 300);
        } else {
            setTimeout(finish, 15000);
        }
    });

    try {
        await audio.play();
        await waitEnded();
        return { played: true };
    } catch (e) {
        console.warn('Audio play blocked', e);
        return { played: false, error: e };
    }
};

window.startCountdown = function ({ seconds, onTick, onDone }) {
    const st = window.__SP_TIMING__;
    window.clearTick();

    st.secondsLeft = Math.max(0, Number(seconds || 0));
    onTick?.(st.secondsLeft);

    st.timerId = setInterval(() => {
        st.secondsLeft -= 1;
        onTick?.(st.secondsLeft);

        if (st.secondsLeft <= 0) {
            window.clearTick();
            onDone?.();
        }
    }, 1000);
};

window.runWaitPhase = function (item) {
    const st = window.__SP_TIMING__;
    st.state = 'wait';

    // buttons: faqat Start
    window.syncSpeakingControlsForItem(item, 'wait');

    const waitSec = Number(item.waitTime || 0);
    window.setTimerUI({
        status: 'wait',
        seconds: waitSec,
        sub: 'Prepare to speak',
        isRecording: false,
        questionLabel: `Question ${item.globalOrder}`
    });

    window.startCountdown({
        seconds: waitSec,
        onTick: (left) => {
            window.setTimerUI({
                status: 'wait',
                seconds: left,
                sub: 'Prepare to speak',
                isRecording: false,
                questionLabel: `Question ${item.globalOrder}`
            });
        },
        onDone: () => window.runRecordPhase(item)
    });
};

window.playTingSound = async function () {
    const tingUrl = window.__SPEAKING__?.partsData?.audioInstructionsData?.tingVoiseLink || '';
    if (!tingUrl) return;

    try {
        await window.playAudioUrlOnce?.(tingUrl);
    } catch (_) {}
};

window.runRecordPhase = async function (item) {
    const st = window.__SP_TIMING__;
    st.state = 'record';

    const isLast = window.isSpeakingLastItem?.(item);

    // ✅ oxirgi savolda Next emas — Finish ko'rinsin
    window.syncSpeakingControlsForItem(item, isLast ? 'finish' : 'record');

    const recSec = Number(item.recordTime || 0);

    // ✅ "ting" audio (record oldidan)
    const tingUrl = window.__SPEAKING__?.partsData?.audioInstructionsData?.tingVoiseLink || '';

    // UI: hozir record emas, tayyorlanayapti
    window.setTimerUI({
        status: 'record',
        seconds: recSec,
        sub: 'Get ready...',
        isRecording: false,
        questionLabel: `Question ${item.globalOrder}`
    });

    // ting play (1 sec) -> keyin record boshlanadi
    if (tingUrl) {
        try { await window.playAudioUrlOnce?.(tingUrl); } catch (_) {}
    }

    window.setTimerUI({
        status: 'record',
        seconds: recSec,
        sub: 'Recording...',
        isRecording: true,
        questionLabel: `Question ${item.globalOrder}`
    });

    // start recorder
    let started = false;
    try {
        await window.SpeakingRecorder?.start?.();
        started = true;
    } catch (e) {
        console.error('Recorder start failed', e);
    }

    // ✅ NEW: record start timestamp
    st.recordStartedAt = Date.now();

    // ✅ NEW: 2 sekund Next/Finish bosilmasin (UI lock)
    // (mode record yoki finish bo‘lishidan qat’i nazar, ikkisini ham lock qilamiz)
    try {
        window.lockSpeakingNextButtonsForMs?.(item.controlsEl, 2000);
    } catch (_) {}

    window.startCountdown({
        seconds: recSec,
        onTick: (left) => {
            window.setTimerUI({
                status: 'record',
                seconds: left,
                sub: 'Recording...',
                isRecording: true,
                questionLabel: `Question ${item.globalOrder}`
            });
        },
        onDone: async () => {
            if (!started) {
                if (isLast) {
                    window.syncSpeakingControlsForItem(item, 'finish');
                    window.openSpeakingFinishModal?.();
                    return;
                }
                window.gotoNextSpeakingItem();
                return;
            }

            if (isLast) {
                // ✅ record tugashi bilan: stop+save + AUTO modal
                await window.finishRecordAndStayForFinish(item, { openModal: true });
            } else {
                await window.finishRecordAndGoNext(item);
            }
        }
    });
};

window.runSpeakingItem = async function (index) {
    const st = window.__SP_TIMING__;
    const queue = window.__SPEAKING__?.queue || [];
    const item = queue[index];

    if (!item) {
        st.state = 'done';
        st.running = false;
        window.clearTick();

        window.setTimerUI({
            status: 'done',
            seconds: 0,
            sub: 'All questions completed',
            isRecording: false,
            questionLabel: 'Finished'
        });

        return;
    }

    st.currentIndex = index;

    // Panelni ko'rsatamiz
    window.showSpeakingPanelById(item.panelId);

// ✅ PART 1 title: 1.1 / 1.2
    if (Number(item.partNum) === 1) {
        window.updatePartOneTitle?.(item);
    }
    // ✅ PART instruction: savol ko'rsatishdan oldin
    // (hech narsa ko'rinmasin)
    await window.playInstructionIfNeededForItem?.(item);

    // endi savolni ko'rsatamiz
    window.showOnlyThisSpeakingQuestion?.(item.el);
    window.markActiveQuestion(item.el);
    window.scrollToQuestion(item.el);

    window.setSpeakingCurrentIndex(index);

    // ✅ shared controls target order update
    window.setSpeakingControlsTargetOrder?.(item.controlsEl, item.globalOrder);

    // audio phase: buttons HIDE
    st.state = 'audio';
    window.syncSpeakingControlsForItem(item, 'hide');

    const audioEl = item.audioEl;

    window.setTimerUI({
        status: 'audio',
        seconds: 0,
        sub: 'Listening...',
        isRecording: false,
        questionLabel: `Question ${item.globalOrder}`
    });

    if (audioEl && audioEl.src) {
        await window.playQuestionAudioOnce(audioEl);
    }

    window.runWaitPhase(item);
};

window.gotoNextSpeakingItem = function () {
    const st = window.__SP_TIMING__;
    if (!st.running) return;

    const next = (st.currentIndex || 0) + 1;
    window.runSpeakingItem(next);
};

window.startSpeakingExam = function () {
    const st = window.__SP_TIMING__;
    const queue = window.__SPEAKING__?.queue || [];

    if (!queue.length) {
        window.setTimerUI({
            status: 'idle',
            seconds: 0,
            sub: 'No questions',
            isRecording: false
        });
        return;
    }

    st.running = true;
    st.state = 'idle';

    // continue from draft (if exists)
    const savedIndex = window.getSpeakingCurrentIndex?.() ?? 0;
    const startIdx = Math.min(Math.max(0, Number(savedIndex || 0)), queue.length - 1);

    window.runSpeakingItem(startIdx);
};

// panel show helper (called by timing)
window.showSpeakingPanelById = function (panelId) {
    const available = window.availableSpeakingParts || [];
    const btnEls = window.speakingBtnElements || [];

    const idx = available.findIndex(x => x.panelId === panelId);
    if (idx >= 0 && typeof window.setSpeakingActive === 'function') {
        window.setSpeakingActive(idx, available, btnEls);
    }
};

// bind start/stop buttons (optional debug)
document.addEventListener('DOMContentLoaded', () => {
    const startBtn = document.getElementById('spStartBtn');
    const stopBtn = document.getElementById('spStopBtn');

    if (startBtn && !startBtn.dataset.bound) {
        startBtn.dataset.bound = '1';
        startBtn.addEventListener('click', () => window.startSpeakingExam());
    }
    if (stopBtn && !stopBtn.dataset.bound) {
        stopBtn.dataset.bound = '1';
        stopBtn.addEventListener('click', () => window.stopSpeakingExam());
    }
});
