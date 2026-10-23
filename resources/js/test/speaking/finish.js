
window.getCsrfToken = function () {
    // Laravel blade: <input name="_token" ...> bor
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput?.value) return tokenInput.value;


    return '';
};

window.openSpeakingFinishModal = function () {
    window.renderSpeakingFinishModal?.();

    const modal = document.getElementById('finishModal');
    const card = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95', 'translate-y-2');
        card.classList.add('scale-100', 'translate-y-0');
    });
};

window.closeSpeakingFinishModal = function () {
    const modal = document.getElementById('finishModal');
    const card = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    card.classList.remove('scale-100', 'translate-y-0');
    card.classList.add('scale-95', 'translate-y-2');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
};

// ✅ Upload davomida sahifani reload / boshqa page ga o'tishni bloklaymiz.
//    Brauzer default ogohlantirish (confirm) alertini ko'rsatadi.
window.__SP_UPLOADING__ = window.__SP_UPLOADING__ || false;
if (!window.__SP_BEFOREUNLOAD_BOUND__) {
    window.__SP_BEFOREUNLOAD_BOUND__ = true;
    window.addEventListener('beforeunload', (e) => {
        if (window.__SP_UPLOADING__) {
            e.preventDefault();
            e.returnValue = ''; // brauzer default ogohlantirishini majburlaydi
            return '';
        }
    });
}

// ✅ Finish submit paytida ekran lock overlay
window.showFinishLoadingOverlay = function () {
    const ov = document.getElementById('finish-loading-overlay');
    if (!ov) return;

    ov.classList.remove('hidden');

    // scroll lock
    try {
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    } catch (_) {}
};

window.hideFinishLoadingOverlay = function () {
    const ov = document.getElementById('finish-loading-overlay');
    if (!ov) return;

    ov.classList.add('hidden');

    // scroll unlock
    try {
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    } catch (_) {}
};

// ✅ status list render
window.renderSpeakingFinishModal = function () {
    const list = document.getElementById('finishPartsList');
    const badge = document.getElementById('finishTotalBadge');
    const err = document.getElementById('finishErrorText');
    if (err) err.classList.add('hidden');

    if (!list || !badge) return;

    const partsData = window.__SPEAKING__?.partsData || {};
    const draft = window.getSpeakingDraft?.() || {};
    const doneSet = new Set();

    const doneFromDraft = () => {
        const p1f = draft?.part_one?.one_first_question || [];
        const p1s = draft?.part_one?.one_second_question || [];
        p1f.forEach(x => x?.id && doneSet.add(`p1f-${x.id}`));
        p1s.forEach(x => x?.id && doneSet.add(`p1s-${x.id}`));
        if (draft?.part_two?.question?.id) doneSet.add(`p2-${draft.part_two.question.id}`);
        if (draft?.part_three?.id) doneSet.add(`p3-${draft.part_three.id}`);
    };

    doneFromDraft();

    const rows = [];

    // Part 1.1
    const p11 = partsData.partOneFirst;
    if (p11?.questions?.length) {
        const total = p11.questions.length;
        const done = p11.questions.filter(q => doneSet.has(`p1f-${q.id}`)).length;
        rows.push({ title: 'Part 1.1', done, total });
    }

    // Part 1.2
    const p12 = partsData.partOneSecond;
    if (p12?.questions?.length) {
        const total = p12.questions.length;
        const done = p12.questions.filter(q => doneSet.has(`p1s-${q.id}`)).length;
        rows.push({ title: 'Part 1.2', done, total });
    }

    // Part 2
    const p2 = partsData.partTwo;
    if (p2?.question?.id) {
        const total = 1;
        const done = doneSet.has(`p2-${p2.question.id}`) ? 1 : 0;
        rows.push({ title: 'Part 2', done, total });
    }

    // Part 3
    const p3 = partsData.partThree;
    if (p3?.id) {
        const total = 1;
        const done = doneSet.has(`p3-${p3.id}`) ? 1 : 0;
        rows.push({ title: 'Part 3', done, total });
    }

    const allTotal = rows.reduce((a, r) => a + r.total, 0);
    const allDone = rows.reduce((a, r) => a + r.done, 0);

    badge.textContent = `${allDone}/${allTotal} Done`;
    list.innerHTML = '';

    rows.forEach(r => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between border border-gray-200 rounded-xl px-4 py-3';

        const left = document.createElement('div');
        left.className = 'font-extrabold text-gray-800';
        left.textContent = r.title;

        const right = document.createElement('div');
        right.className = 'text-sm font-extrabold';
        right.textContent = `${r.done}/${r.total}`;

        div.appendChild(left);
        div.appendChild(right);
        list.appendChild(div);
    });
};

// ✅ dynamic file inputs cleanup
window.clearSpeakingDynamicFileInputs = function () {
    const host = document.getElementById('speaking-files-host');
    if (host) host.innerHTML = '';

    const form = document.getElementById('speaking-form');
    if (!form) return;

    form.querySelectorAll('input[data-sp-dyn-file="1"]').forEach(el => el.remove());
};

// ✅ append hidden file input (DataTransfer -> input.files)
window.appendHiddenFileInputToForm = function (fieldName, file) {
    const form = document.getElementById('speaking-form');
    if (!form || !file) return;

    const host = document.getElementById('speaking-files-host') || form;

    const input = document.createElement('input');
    input.type = 'file';
    input.name = fieldName;
    input.setAttribute('data-sp-dyn-file', '1');
    input.style.display = 'none';

    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;

    host.appendChild(input);
};

// ✅ NEW: idb seq -> File
window.makeFileFromSeq = async function (seq, filenameFallback) {
    const blob = await window.idbLoadRecording?.(window.STORAGE_KEY, seq);
    if (!blob || !blob.size) return null;

    const mime = blob.type || 'audio/webm';
    const ext = mime.includes('ogg') ? 'ogg' : 'webm';
    const filename = (filenameFallback || `rec_${seq}.${ext}`);

    return new File([blob], filename, { type: mime });
};

// ✅ timeout + retry bilan upload.
//    - timeoutMs: shu vaqt ichida javob kelmasa AbortController uzadi (spinner abadiy qolmaydi)
//    - retries: faqat tarmoq xatosi / 5xx da qayta urinadi (client 4xx da urinmaydi)
window.__spUploadWithRetry = async function (url, formData, { timeoutMs = 120000, retries = 1 } = {}) {
    let lastErr;

    for (let attempt = 0; attempt <= retries; attempt++) {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), timeoutMs);

        try {
            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller.signal,
            });
            clearTimeout(timer);

            // server xatosi (5xx) — qayta urinishga arziydi; 4xx — urinmaymiz
            if (res.status >= 500 && attempt < retries) {
                lastErr = new Error(`Server error ${res.status}`);
                continue;
            }

            return res;
        } catch (e) {
            clearTimeout(timer);
            lastErr = e; // tarmoq uzilishi yoki timeout (abort)
            // oxirgi urinish bo'lsa — tashqariga otamiz
            if (attempt >= retries) break;
        }
    }

    throw lastErr || new Error('Upload failed');
};

window.submitSpeakingFinishWithAudio = async function () {
    const form = document.getElementById('speaking-form');
    if (!form) return;

    const acceptBtn = document.getElementById('finishAcceptBtn');
    const err = document.getElementById('finishErrorText');
    if (err) err.classList.add('hidden');

    try {
        // UI lock
        if (acceptBtn) {
            acceptBtn.disabled = true;
            acceptBtn.classList.add('opacity-60', 'cursor-not-allowed');
        }

        window.showFinishLoadingOverlay?.();
        try { window.closeSpeakingFinishModal?.(); } catch (_) {}

        // ✅ draft + with_ai hidden inputlar
        const draft = window.ensureSpeakingDraftBase?.() || window.getSpeakingDraft?.() || {};

        const withAiToggle = document.getElementById('finishWithAiToggle');
        const withAi = withAiToggle?.checked ? 1 : 0;

        const draftInput = document.getElementById('speaking-draft-input');
        const withAiInput = document.getElementById('speaking-with-ai-input');

        if (draftInput) draftInput.value = JSON.stringify(draft || {});
        if (withAiInput) withAiInput.value = String(withAi);

        // ✅ old dynamic files cleanup
        window.clearSpeakingDynamicFileInputs?.();

        // ✅ Part 1 first
        for (const x of (draft?.part_one?.one_first_question || [])) {
            const qid = Number(x?.id || 0);
            const seq = Number(x?.seq || 0);
            if (!qid || !seq) continue;

            const file = await window.makeFileFromSeq(seq, `p1_first_q${qid}.webm`);
            if (!file) throw new Error(`Missing/empty audio (Part1 first) qid=${qid} seq=${seq}`);

            window.appendHiddenFileInputToForm?.(`recordings[p1_first][${qid}]`, file);
        }

        // ✅ Part 1 second
        for (const x of (draft?.part_one?.one_second_question || [])) {
            const qid = Number(x?.id || 0);
            const seq = Number(x?.seq || 0);
            if (!qid || !seq) continue;

            const file = await window.makeFileFromSeq(seq, `p1_second_q${qid}.webm`);
            if (!file) throw new Error(`Missing/empty audio (Part1 second) qid=${qid} seq=${seq}`);

            window.appendHiddenFileInputToForm?.(`recordings[p1_second][${qid}]`, file);
        }

        // ✅ Part 2
        if (draft?.part_two?.question?.id && draft?.part_two?.question?.seq) {
            const qid = Number(draft.part_two.question.id);
            const seq = Number(draft.part_two.question.seq);

            const file = await window.makeFileFromSeq(seq, `p2_q${qid}.webm`);
            if (!file) throw new Error(`Missing/empty audio (Part2) qid=${qid} seq=${seq}`);

            window.appendHiddenFileInputToForm?.(`recordings[p2][${qid}]`, file);
        }

        // ✅ Part 3
        if (draft?.part_three?.id && draft?.part_three?.seq) {
            const qid = Number(draft.part_three.id);
            const seq = Number(draft.part_three.seq);

            const file = await window.makeFileFromSeq(seq, `p3_${qid}.webm`);
            if (!file) throw new Error(`Missing/empty audio (Part3) id=${qid} seq=${seq}`);

            window.appendHiddenFileInputToForm?.(`recordings[p3][${qid}]`, file);
        }

        // ✅ upload boshlandi — reload/chiqishni bloklaymiz (beforeunload guard)
        window.__SP_UPLOADING__ = true;

        // ✅ native submit o'rniga fetch — sahifa navigatsiya qilmaydi,
        //    upload tugaguncha spinner + "uploading" turadi, keyin redirect qilamiz.
        //    timeout (osilib qolmasin) + retry (bitta uzilishda darrov fail bo'lmasin)
        const res = await window.__spUploadWithRetry(form.action, new FormData(form), {
            timeoutMs: 120000, // 2 daqiqa — shu vaqtda javob kelmasa uziladi
            retries: 1,        // network/server xatosida 1 marta qayta urinadi
        });

        if (!res.ok) {
            throw new Error(`Upload failed (HTTP ${res.status})`);
        }

        // ✅ upload muvaffaqiyatli — endi o'zimiz navigatsiya qilamiz
        window.__SP_UPLOADING__ = false;

        // local cleanup
        try { window.clearSpeakingDynamicFileInputs?.(); } catch (_) {}

        // backend redirect qilgan bo'lsa o'sha sahifaga o'tamiz
        window.location.href = res.url;

    } catch (e) {
        console.error(e);

        window.__SP_UPLOADING__ = false;
        window.hideFinishLoadingOverlay?.();

        if (err) {
            err.textContent = 'Upload failed. Please try again.';
            err.classList.remove('hidden');
        }

        if (acceptBtn) {
            acceptBtn.disabled = false;
            acceptBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // ✅ With AI default: tanlangan (1)
    const withAiToggle = document.getElementById('finishWithAiToggle');
    if (withAiToggle) withAiToggle.checked = true;
    const withAiInput = document.getElementById('speaking-with-ai-input');
    if (withAiInput) withAiInput.value = '1';

    const accept = document.getElementById('finishAcceptBtn');
    const closeX = document.getElementById('finishCloseX');
    const cancel = document.getElementById('finishCancelBtn');

    if (accept && !accept.dataset.bound) {
        accept.dataset.bound = '1';
        accept.addEventListener('click', (e) => {
            e.preventDefault();
            window.submitSpeakingFinishWithAudio?.();
        });
    }

    if (closeX && !closeX.dataset.bound) {
        closeX.dataset.bound = '1';
        closeX.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSpeakingFinishModal?.();
        });
    }

    if (cancel && !cancel.dataset.bound) {
        cancel.dataset.bound = '1';
        cancel.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSpeakingFinishModal?.();
        });
    }
});

