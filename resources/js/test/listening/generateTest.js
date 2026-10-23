// resources/js/test/listening/generateTest.js

document.addEventListener('DOMContentLoaded', async () => {
    window.LISTENING_STORAGE_KEY = window.LISTENING_STORAGE_KEY || 'cefr-listening-draft';
    window.LISTENING_TEXT_SIZE_KEY = window.LISTENING_TEXT_SIZE_KEY || 'cefr-listening-text-size-draft';
    window.LISTENING_AUDIO_DRAFT_KEY = window.LISTENING_AUDIO_DRAFT_KEY || 'cefr-listening-audio-draft';

    const { exerciseId, timeLimit, partsData } = window.generatePartData();
    window.__LISTENING__ = { exerciseId, timeLimit, partsData };

    window.IS_MOCK = Number(window.getHidden('is_mock', 0)) === 1;

    if (window.IS_MOCK) {
        // 1) Keyboard reload'ni bloklash
        window.addEventListener('keydown', (e) => {
            const key = (e.key || '').toLowerCase();

            const isF5 = e.key === 'F5';
            const isCtrlR = (e.ctrlKey || e.metaKey) && key === 'r';

            if (isF5 || isCtrlR) {
                e.preventDefault();
                e.stopPropagation();

                // custom alert (oddiy alert)
                alert("Mock test: Reload qilish mumkin emas. Testni yakunlash uchun Finish -> Accept bosing.");
                return false;
            }
        }, { capture: true });

        // 2) Browser refresh button / tab close uchun (browser generic confirm chiqaradi)

        window.addEventListener('beforeunload', (e) => {
            // ✅ agar form submit bo'layotgan bo'lsa - hech narsa qilmaymiz
            if (window.__LISTENING_ALLOW_UNLOAD__ === true) return;

            e.preventDefault();
            e.returnValue = '';
            return '';
        });
    }

    const audioControlRaw = Number(window.getHidden('audio_control', 0)) === 1 ? 1 : 0;
    window.AUDIO_CONTROL = window.IS_MOCK ? 1 : audioControlRaw;

    const navWrap = document.getElementById('partsNav');
    if (!navWrap) return;


    const defs = [
        { key: 'partOne', num: 1, btnId: 'partOneButton', panelId: 'partOneQuestionDiv' },
        { key: 'partTwo', num: 2, btnId: 'partTwoButton', panelId: 'partTwoQuestionDiv' },
        { key: 'partThree', num: 3, btnId: 'partThreeButton', panelId: 'partThreeQuestionDiv' },
        { key: 'partFour', num: 4, btnId: 'partFourButton', panelId: 'partFourQuestionDiv' },
        { key: 'partFive', num: 5, btnId: 'partFiveButton', panelId: 'partFiveQuestionDiv' },
        { key: 'partSix', num: 6, btnId: 'partSixButton', panelId: 'partSixQuestionDiv' },
    ];

    const available = defs.filter(d => partsData[d.key] != null);

    navWrap.innerHTML = '';
    if (available.length === 0) return;

    const btnEls = available.map((d, idx) => {
        const btn = window.createBtn(d, idx === 0);
        navWrap.appendChild(btn);
        return btn;
    });

    window.availableParts = available;
    window.btnElements = btnEls;

    // nav click (locked bo‘lsa user part o‘zgartira olmaydi)
    btnEls.forEach((btn, idx) => {
        btn.addEventListener('click', () => window.setActive(idx, available, btnEls, { force: false }));
    });

    // questions render
    window.generateQuestionDiv(available, partsData);

    // focus question UI (reading style) — focusQuestion.js ichida build qilinadi
    if (typeof window.generateFocusQuestions === 'function') {
        window.generateFocusQuestions();
    }

    // audio init + saved volume
    window.PartAudio.bindControls();

    let savedVol = null;
    if (typeof window.getListeningAudioDraft === 'function') {
        const d = window.getListeningAudioDraft();
        if (typeof d?.volume === 'number') savedVol = d.volume;
    }

    await window.PartAudio.initPartAudios(available, partsData, {
        audioControl: window.AUDIO_CONTROL,
        available,
        volume: savedVol,
    });

    // open first part (audio start here)
    window.setActive(0, available, btnEls, { force: true });


});


// =======================
// helpers
// =======================
window.getHidden = window.getHidden || function (id, fallback = null) {
    const el = document.getElementById(id);
    if (!el) return fallback;

    const raw = (el.value ?? '').trim();
    if (raw === '' || raw === 'null' || raw === 'undefined') return fallback;

    try { return JSON.parse(raw); } catch (_) { return raw; }
};

window.createBtn = function (d, isActive) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = d.btnId;

    btn.className = [
        'w-16',
        'h-10',
        'border',
        'rounded-xl',
        'font-semibold',
        'flex',
        'items-center',
        'justify-center',
        'gap-1',
        'transition',
        'active:scale-[0.99]',
        isActive
            ? 'part-btn-active'
            : 'part-btn hover:bg-gray-100'
    ].join(' ');

    btn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg"
             width="20" height="20"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round"
             class="shrink-0">
            <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>
            <path d="M2 10h20"/>
        </svg>
        <span>${d.num}</span>
    `;

    return btn;
};

window.setActive = function (activeIndex, available, btnEls, opts = { force: false }) {
    const force = !!opts.force;

    // ✅ MOCK: user part almashtira olmaydi (faqat force=true auto-next/focus uchun)
    if (window.IS_MOCK && !force) return;

    // ✅ audio_control lock (mock bo'lsa ham bu 1 bo'lib turadi)
    if (window.AUDIO_CONTROL === 1 && !force) return;

    const prevIdx = Number.isFinite(window.__ACTIVE_PART_INDEX__) ? window.__ACTIVE_PART_INDEX__ : null;
    const prevKey = prevIdx != null ? available?.[prevIdx]?.key : null;

    window.__ACTIVE_PART_INDEX__ = activeIndex;

    available.forEach((d, i) => {
        const panel = document.getElementById(d.panelId);
        if (!panel) return;
        panel.classList.toggle('hidden', i !== activeIndex);
    });

    btnEls.forEach((btn, i) => {
        const isActive = i === activeIndex;

        btn.classList.toggle('part-btn-active', isActive);
        btn.classList.toggle('part-btn', !isActive);
        btn.classList.toggle('hover:bg-gray-100', !isActive);

        // ✅ MOCK yoki AUDIO_CONTROL=1 bo'lsa: nav disabled
        if (window.AUDIO_CONTROL === 1 || window.IS_MOCK) {
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    });

    const activeKey = available?.[activeIndex]?.key;

    // ✅ Part audio faqat part o‘zgarsa
    if (activeKey && activeKey !== prevKey) {
        window.PartAudio.setActivePart(activeKey, true, {
            audioControl: window.AUDIO_CONTROL,
            activeIndex,
            available,
            btnEls,
        });
    }

    // ✅ Part4 map refresh
    if (activeKey === 'partFour') {
        setTimeout(() => {
            window.ListeningMapEditor?.refresh?.();
        }, 0);
    }

    if (typeof window.updateFocusButtonsLockState === 'function') {
        window.updateFocusButtonsLockState();
    }

    window.reRenderTextP?.();
};


// =======================
// generatePartData & questions
// =======================
window.generatePartData = function () {
    const exerciseId = window.getHidden('exercise_id', 0);
    const timeLimit = window.getHidden('time_limit', 60);

    const partOne = window.getHidden('part_one', null);
    const partTwo = window.getHidden('part_two', null);
    const partThree = window.getHidden('part_three', null);
    const partFour = window.getHidden('part_four', null);
    const partFive = window.getHidden('part_five', null);
    const partSix = window.getHidden('part_six', null);

    const partsData = { partOne, partTwo, partThree, partFour, partFive, partSix };
    return { exerciseId, timeLimit, partsData };
};

window.generateQuestionDiv = function (available, partsData) {
    window.resetListeningQuestionOrderCounter?.(1);

    const map = {
        partOne: window.GeneratePartOneQuestionDiv,
        partTwo: window.GeneratePartTwoQuestionDiv,
        partThree: window.GeneratePartThreeQuestionDiv,
        partFour: window.GeneratePartFourQuestionDiv,
        partFive: window.GeneratePartFiveQuestionDiv,
        partSix: window.GeneratePartSixQuestionDiv,
    };

    available.forEach(data => {
        const func = map[data.key];
        if (typeof func !== 'function') return;
        func(partsData[data.key]);
    });
};


// =======================
// PART renderers (sizniki, o‘zgarmagan)
// =======================
window.GeneratePartOneQuestionDiv = function (data) {
    if (!data) return;

    const grid = document.getElementById('partOneGrid');
    const adviceEl = document.getElementById('partOneAdvice');
    if (!grid) return;

    if (adviceEl) adviceEl.textContent = data.advice ?? '';
    grid.innerHTML = '';

    const tests = Array.isArray(data.tests) ? data.tests : [];
    tests.forEach((t, idx) => {
        const card = window.createPartOneCard({
            order: window.getNextListeningQuestionOrder(),
            testId: t.id,
            a1: t.answer_1,
            a2: t.answer_2,
            a3: t.answer_3
        });
        grid.appendChild(card);
    });
};

window.GeneratePartTwoQuestionDiv = function (data) {
    if (!data) return;

    const adviceEl = document.getElementById('partTwoAdvice');
    const titleEl = document.getElementById('partTwoTitle');
    const wrapEl = document.getElementById('partTwoTextWrap');
    if (!wrapEl) return;

    if (adviceEl) adviceEl.textContent = data.advice ?? '';

    let text = String(data.text ?? '').replace(/\r/g, '').trim();
    let title = data.title;

    if (!title) {
        const lines = text.split('\n').map(l => l.trim()).filter(Boolean);
        title = lines[0] ?? '';
        text = lines.length > 1 ? lines.slice(1).join('\n') : '';
    }

    if (titleEl) titleEl.textContent = title ?? '';

    const orderMap = window.buildListeningSequentialOrderMap(text);
    window.renderPartTwoTextInto(wrapEl, text, orderMap);

    const answers = Array.isArray(data.answers) ? data.answers : [];
    answers.forEach((a) => {
        const originalOrder = String(a.count_number ?? '').trim();
        if (!originalOrder) return;

        const input = wrapEl.querySelector(`input[data-count="${originalOrder}"]`);
        if (!input) return;

        input.dataset.answerid = String(a.id);
        input.dataset.questiontype = 'input';
        input.dataset.questionorder = String(orderMap[originalOrder] ?? originalOrder);
        input.id = `part-2-${a.id}-input`;
    });
};
// resources/js/test/listening/generateTest.js

window.GeneratePartThreeQuestionDiv = function (data) {
    if (!data) return;

    const adviceEl = document.getElementById('partThreeAdvice');
    const grid = document.getElementById('partThreeGrid');
    if (!grid) return;

    if (adviceEl) adviceEl.textContent = data.advice ?? '';
    grid.innerHTML = '';

    const questions = Array.isArray(data.questions) ? data.questions : [];
    const answers = Array.isArray(data.answers) ? data.answers : [];
    const letterArray = Array.isArray(data.letter_array) ? data.letter_array : [];

    questions.forEach((q, idx) => {
        const title = q.title ?? `Speaker ${idx + 1}`;

        const order = window.getNextListeningQuestionOrder();

        const card = window.createPartThreeCard({
            questionId: q.id,
            title,
            order,
            answers,
            letterArray
        });

        grid.appendChild(card);
    });
};

// resources/js/test/listening/generateTest.js

window.GeneratePartFourQuestionDiv = function (data) {
    if (!data) return;

    const adviceEl = document.getElementById('partFourAdvice');
    const listEl   = document.getElementById('partFourList');
    const imgEl    = document.getElementById('partFourMapImg');
    const phEl     = document.getElementById('partFourMapPlaceholder');

    if (!listEl) return;
    if (adviceEl) adviceEl.textContent = data.advice ?? '';

    const rawMap = String(data.map ?? '').trim();
    const url = rawMap.startsWith('/storage/')
        ? rawMap
        : rawMap
            ? ('/storage/' + rawMap.replace(/^public\//, ''))
            : '';

    // MAP
    if (imgEl) {
        if (url) {
            window.ListeningMapEditor?.destroy?.();

            const doInit = () => {
                window.ListeningMapEditor?.init?.({
                    boxId: 'partFourMapBox',
                    imgId: 'partFourMapImg',
                    canvasLayerId: 'partFourMapCanvasLayer',
                    pencilHintId: 'partFourMapPencilHint',

                    resetBtnId: 'partFourMapResetBtn',
                    fullscreenBtnId: 'partFourMapFullscreenBtn',

                    // dropdown
                    penBoxId: 'partFourMapPenColorBox',
                    penDropdownId: 'partFourMapPenColorDropdown',
                    penPreviewId: 'partFourMapPenColorPreview',
                    penSwatchSelector: '#partFourMapPenColorDropdown .pen-swatch',

                    // modal
                    modalId: 'mapModal',
                    modalImgId: 'mapModalImg',
                    modalCloseId: 'mapModalClose',
                });

                window.ListeningMapEditor?.refresh?.();
            };

            imgEl.onload = () => doInit();

            imgEl.src = url;
            imgEl.classList.remove('hidden');
            imgEl.setAttribute('draggable', 'false');
            if (phEl) phEl.classList.add('hidden');

            if (imgEl.complete) {
                setTimeout(doInit, 0);
            }
        } else {
            imgEl.src = '';
            imgEl.classList.add('hidden');
            if (phEl) phEl.classList.remove('hidden');

            window.ListeningMapEditor?.destroy?.();
        }
    }

    // QUESTIONS
    listEl.innerHTML = '';

    const questions = Array.isArray(data.questions) ? data.questions : [];
    const answers = Array.isArray(data.answers) ? data.answers : [];
    const letterArray = Array.isArray(data.letter_array) ? data.letter_array : [];

    questions.forEach((q, idx) => {
        const title = q.title ?? `Question ${idx + 1}`;

        // ✅ Part 4 => 19..23 (title’dan emas)
        const order = window.getNextListeningQuestionOrder();

        const card = window.createPartFourQuestionCard({
            questionId: q.id,
            title,
            order,
            answers,
            letterArray
        });

        listEl.appendChild(card);
    });
};


// resources/js/test/listening/generateTest.js

window.GeneratePartFiveQuestionDiv = function (data) {
    if (!data) return;

    const adviceEl = document.getElementById('partFiveAdvice');
    const grid = document.getElementById('partFiveGrid');
    if (!grid) return;

    if (adviceEl) adviceEl.textContent = data.advice ?? '';
    grid.innerHTML = '';

    const tests = Array.isArray(data.tests) ? data.tests : [];

    tests.forEach((t, idx) => {
        // ✅ Part 5 => 24..29 (question textdan emas)
        const order = window.getNextListeningQuestionOrder();

        const card = window.createPartFiveCard({
            order,
            testId: t.id,
            question: t.question ?? '',
            a1: t.answer_1,
            a2: t.answer_2,
            a3: t.answer_3
        });

        grid.appendChild(card);
    });
};


window.GeneratePartSixQuestionDiv = function (data) {
    if (!data) return;

    const adviceEl = document.getElementById('partSixAdvice');
    const titleEl = document.getElementById('partSixTitle');
    const wrapEl = document.getElementById('partSixTextWrap');
    if (!wrapEl) return;

    if (adviceEl) adviceEl.textContent = data.advice ?? '';

    let text = String(data.text ?? '').replace(/\r/g, '').trim();
    let title = data.title;

    if (!title) {
        const lines = text.split('\n').map(l => l.trim()).filter(Boolean);
        title = lines[0] ?? '';
        text = lines.length > 1 ? lines.slice(1).join('\n') : '';
    }

    if (titleEl) titleEl.textContent = title ?? '';

    const orderMap = window.buildListeningSequentialOrderMap(text);
    window.renderPartSixTextInto(wrapEl, text, orderMap);

    const answers = Array.isArray(data.answers) ? data.answers : [];
    answers.forEach((a) => {
        const originalOrder = String(a.count_number ?? '').trim();
        if (!originalOrder) return;

        const input = wrapEl.querySelector(`input[data-count="${originalOrder}"]`);
        if (!input) return;

        input.dataset.answerid = String(a.id);
        input.dataset.questiontype = 'input';
        input.dataset.questionorder = String(orderMap[originalOrder] ?? originalOrder);
        input.id = `part-6-${a.id}-input`;
    });
};
