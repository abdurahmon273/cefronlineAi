window.getHidden = function (id, fallback = null) {
    const el = document.getElementById(id);
    if (!el) return fallback;

    const raw = (el.value ?? '').trim();
    if (raw === '' || raw === 'null' || raw === 'undefined') return fallback;

    try { return JSON.parse(raw); } catch (_) { return raw; }
};

window.generatePartData = function () {
    const exerciseId = window.getHidden('exercise_id', 0);
    const timeLimit = window.getHidden('time_limit', 60);

    const partOne = window.getHidden('part_one', null);
    const partTwo = window.getHidden('part_two', null);
    const partThree = window.getHidden('part_three', null);
    const partFour = window.getHidden('part_four', null);
    const partFive = window.getHidden('part_five', null);

    const partsData = { partOne, partTwo, partThree, partFour, partFive };
    return { exerciseId, timeLimit, partsData };
};

window.createBtn = function (d, isActive) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = d.btnId;

    btn.className = [
        'w-12', 'h-10',
        isActive ? 'bg-white' : 'bg-gray-200',
        'border', 'border-gray-400',
        'text-gray-900', 'font-semibold',
        'hover:bg-gray-100', 'active:scale-[0.99]'
    ].join(' ');

    btn.innerHTML = `
        <span class="relative inline-block font-bold text-gray-900">
            ${d.num}
            <span class="part-underline ${isActive ? '' : 'hidden'} absolute -bottom-1 left-1/2 -translate-x-1/2 h-[2px] bg-gray-900 w-6"></span>
        </span>
    `;
    return btn;
};

window.setActive = function (activeIndex, available, btnEls) {
    available.forEach((d, i) => {
        const panel = document.getElementById(d.panelId);
        if (!panel) return;
        panel.classList.toggle('hidden', i !== activeIndex);
    });

    btnEls.forEach((btn, i) => {
        const underline = btn.querySelector('.part-underline');
        if (underline) underline.classList.toggle('hidden', i !== activeIndex);
        btn.classList.toggle('bg-white', i === activeIndex);
        btn.classList.toggle('bg-gray-200', i !== activeIndex);
    });
};

window.generateQuestionDiv = function (available, partsData) {
    const map = {
        partOne: window.GeneratePartOneQuestionDiv,
        partTwo: window.GeneratePartTwoQuestionDiv,
        partThree: window.GeneratePartThreeQuestionDiv,
        partFour: window.GeneratePartFourQuestionDiv,
        partFive: window.GeneratePartFiveQuestionDiv,
    };

    available.forEach(def => {
        const fn = map[def.key];
        if (typeof fn === 'function') fn(partsData[def.key]);
    });
};

// PART 1
window.GeneratePartOneQuestionDiv = function (data) {
    const root = window.ensurePartShell({
        rootId: 'partOneQuestionDiv',
        partLabel: 'PART 1',
        adviceId: 'partOneAdviceP',
        titleId: 'partOneTitleH2',
        bodyId: 'partOneBodyDiv'
    });
    if (!root) return;

    window.renderAdvice(document.getElementById('partOneAdviceP'), data?.advice ?? '');
    const title = (data?.title ?? '').toString().trim() || window.pickTitleFromText(data?.text ?? '');
    window.renderTitle(document.getElementById('partOneTitleH2'), title);
    const cleanedText = window.removeTitleLineFromText(data?.text ?? '', title);

    window.renderTextWithInputsDOM(document.getElementById('partOneBodyDiv'), 1, cleanedText ?? '', data?.answers ?? []);
};

// PART 2
window.GeneratePartTwoQuestionDiv = function (data) {
    const root = window.ensurePartTwoShell({
        rootId: 'partTwoQuestionDiv',
        partLabel: 'PART 2',
        adviceId: 'partTwoAdviceP',
        leftId: 'partTwoLeft',
        rightId: 'partTwoRight',
        textsId: 'partTwoTextsWrap'
    });
    if (!root) return;

    window.renderPartTwoAdvice('partTwoAdviceP', data?.advice ?? '');

    const textsWrap = document.getElementById('partTwoTextsWrap');
    const rightWrap = document.getElementById('partTwoRight');

    // ✅ result page ham reading kabi bitta baseOrder ishlatadi
    const baseOrder = window.getNextQuestionOrderStart();

    window.renderPartTwoTexts(textsWrap, data?.texts ?? [], baseOrder);
    window.renderPartTwoRightSide(rightWrap, data, baseOrder);
};


// PART 3
window.GeneratePartThreeQuestionDiv = function (data) {
    const adviceEl = document.getElementById('partThreeAdviceP');
    const textsWrap = document.getElementById('partThreeTextsWrap');
    const selectsWrap = document.getElementById('partThreeSelectsWrap');
    if (!textsWrap || !selectsWrap) return;

    if (adviceEl) adviceEl.textContent = (data?.advice ?? '').toString();
    textsWrap.innerHTML = '';
    selectsWrap.innerHTML = '';

    const texts = Array.isArray(data?.texts) ? data.texts : [];
    const answers = Array.isArray(data?.answers) ? data.answers : [];

    texts.forEach((t, idx) => {
        textsWrap.appendChild(window.buildPart3TextItem(window.toRoman(idx + 1), t?.text ?? ''));
    });

    const opts = window.buildPart3OptionsFromAnswers(answers);

    // ✅ order shu yerdan boshlanadi
    const baseOrder = window.getNextQuestionOrderBase(1);

    texts.forEach((t, idx) => {
        const textId = t?.id ?? (idx + 1);
        const order = baseOrder + idx;

        // ✅ title: "15. Paragraph I"
        selectsWrap.appendChild(
            window.buildPart3SelectCard(idx + 1, 3, textId, opts, order, true)
        );
    });
};


// PART 4
window.GeneratePartFourQuestionDiv = function (data) {
    const adviceEl = document.getElementById('partFourAdviceP');
    const titleEl = document.getElementById('partFourTitleH3');
    const textEl = document.getElementById('partFourTextDiv');
    const qWrap = document.getElementById('partFourQuestionsDiv');
    if (!qWrap) return;

    if (adviceEl) adviceEl.textContent = (data?.advice ?? '').toString();
    const title = window.pickTitleFromText(data?.text ?? '');
    if (titleEl) titleEl.textContent = title;
    const cleanedText = window.removeTitleLineFromText(data?.text ?? '', title);

    window.renderTextAsParagraphsNoBr(textEl, cleanedText ?? '');
    qWrap.innerHTML = '';

    const baseOrder = window.getNextQuestionOrderBase(1);
    let order = baseOrder;

    const tfng = Array.isArray(data?.TFNg_tests) ? data.TFNg_tests : [];
    const mchq = Array.isArray(data?.MCHQ_tests) ? data.MCHQ_tests : [];

    tfng.forEach(test => qWrap.appendChild(window.buildRadioQuestionCard(4, test, 'radio', order++)));
    mchq.forEach(test => qWrap.appendChild(window.buildRadioQuestionCard(4, test, 'radio_mchq', order++)));
};

window.GeneratePartFiveQuestionDiv = function (data) {
    const adviceEl = document.getElementById('partFiveAdviceP');
    const titleEl    = document.getElementById('partFiveTitleH3');   // ✅ yangi
    const readingWrap = document.getElementById('partFiveReadingWrap');
    const fillWrap = document.getElementById('partFiveFillGapsWrap');
    const mchqWrap = document.getElementById('partFiveMchqWrap');
    const rawReading = data?.reading_text ?? '';
    const title = window.pickTitleFromText(rawReading);

    if (titleEl) titleEl.textContent = (title ?? '').toString();

    const cleanedReading = window.removeTitleLineFromText(rawReading, title);

    if (adviceEl) adviceEl.textContent = (data?.advice ?? '').toString();
    window.renderTextAsParagraphsNoBr(readingWrap, cleanedReading ?? '');

    if (fillWrap) {
        fillWrap.innerHTML = '';
        fillWrap.appendChild(window.buildFillGapsCard(5, data?.question_text ?? '', data?.answers ?? []));
    }

    const radioBaseOrder = window.getNextQuestionOrderStart();
    if (mchqWrap) {
        mchqWrap.innerHTML = '';
        const tests = Array.isArray(data?.tests) ? data.tests : [];
        tests.forEach((t, idx) => mchqWrap.appendChild(window.buildMchqRadioCardP(5, t, radioBaseOrder + idx)));
    }
};

// resources/js/test/reading_result/generateTest.js

window.getHidden = function (id, fallback = null) {
    const el = document.getElementById(id);
    if (!el) return fallback;

    const raw = (el.value ?? '').trim();
    if (raw === '' || raw === 'null' || raw === 'undefined') return fallback;

    try {
        return JSON.parse(raw);
    } catch (_) {
        return raw;
    }
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

window.setActive = function (activeIndex, available, btnEls) {
    available.forEach((d, i) => {
        const panel = document.getElementById(d.panelId);
        if (!panel) return;
        panel.classList.toggle('hidden', i !== activeIndex);
    });

    // Buttons
    btnEls.forEach((btn, i) => {
        const isActive = i === activeIndex;

        btn.classList.toggle('part-btn-active', isActive);
        btn.classList.toggle('part-btn', !isActive);

        btn.classList.toggle('hover:bg-gray-100', !isActive);
    });

    window.reRenderTextP()
};

window.generatePartData = function () {
    const exerciseId = window.getHidden('exercise_id', 0);
    const timeLimit = window.getHidden('time_limit', 60);

    const partOne = window.getHidden('part_one', null);
    const partTwo = window.getHidden('part_two', null);
    const partThree = window.getHidden('part_three', null);
    const partFour = window.getHidden('part_four', null);
    const partFive = window.getHidden('part_five', null);

    const partsData = { partOne, partTwo, partThree, partFour, partFive };

    return { exerciseId, timeLimit, partsData };
};

window.generateQuestionDiv = function (available, partsData) {
    const map = {
        partOne: window.GeneratePartOneQuestionDiv,
        partTwo: window.GeneratePartTwoQuestionDiv,
        partThree: window.GeneratePartThreeQuestionDiv,
        partFour: window.GeneratePartFourQuestionDiv,
        partFive: window.GeneratePartFiveQuestionDiv,
    };

    available.forEach(data => {
        const func = map[data.key];
        if (typeof func !== 'function') return;
        func(partsData[data.key]);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const { exerciseId, timeLimit, partsData } = window.generatePartData();
    window.__READING__ = { exerciseId, timeLimit, partsData };

    const navWrap = document.getElementById('partsNav');
    if (!navWrap) return;

    const defs = [
        { key: 'partOne', num: 1, btnId: 'partOneButton', panelId: 'partOneQuestionDiv' },
        { key: 'partTwo', num: 2, btnId: 'partTwoButton', panelId: 'partTwoQuestionDiv' },
        { key: 'partThree', num: 3, btnId: 'partThreeButton', panelId: 'partThreeQuestionDiv' },
        { key: 'partFour', num: 4, btnId: 'partFourButton', panelId: 'partFourQuestionDiv' },
        { key: 'partFive', num: 5, btnId: 'partFiveButton', panelId: 'partFiveQuestionDiv' },
    ];

    const available = defs.filter(d => partsData[d.key] != null);

    navWrap.innerHTML = '';
    if (!available.length) return;

    const btnEls = available.map((d, idx) => {
        const btn = window.createBtn(d, idx === 0);
        navWrap.appendChild(btn);
        return btn;
    });

    window.availableParts = available;
    window.btnElements = btnEls;

    btnEls.forEach((btn, idx) => {
        btn.addEventListener('click', () => window.setActive(idx, available, btnEls));
    });

    window.generateQuestionDiv(available, partsData);

    // default first part
    window.setActive(0, available, btnEls);

    // ✅ RESULT APPLY shu yerda ham chaqiramiz (reading_result.js ham chaqiradi)
    if (typeof window.applyResultModeAll === 'function') window.applyResultModeAll(partsData);

    // ✅ number ranglari uchun state
    if (typeof window.buildResultStateByOrder === 'function') {
        window.__RESULT_STATE_BY_ORDER__ = window.buildResultStateByOrder(partsData);
    } else {
        window.__RESULT_STATE_BY_ORDER__ = {};
    }

    // ✅ focus numbers UI
    if (typeof window.generateFocusQuestions === 'function') window.generateFocusQuestions();
});
