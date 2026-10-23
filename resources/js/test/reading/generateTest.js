


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
    if (available.length === 0) return;

    const btnEls = available.map((d, idx) => {
        const btn = window.createBtn(d, idx === 0);
        navWrap.appendChild(btn);
        return btn;
    });
    window.availableParts=available
    window.btnElements=btnEls

    btnEls.forEach((btn, idx) => {
        btn.addEventListener('click', () => window.setActive(idx, available, btnEls));
    });

    window.generateQuestionDiv(available, partsData);

    window.setActive(0, available, btnEls);
});

window.getHidden = window.getHidden || function (id, fallback = null) {
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
    // Panels
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

        // 🔑 hover faqat inactive holatda
        btn.classList.toggle('hover:bg-gray-100', !isActive);
    });

    // there
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

    generateFocusQuestions()

    // reGenerateStorageDataToHtml()
};

window.GeneratePartOneQuestionDiv = function (data) {
    const root = window.ensurePartShell({
        rootId: 'partOneQuestionDiv',
        partLabel: 'PART 1',
        adviceId: 'partOneAdviceP',
        titleId: 'partOneTitleH2',
        bodyId: 'partOneBodyDiv'
    });
    if (!root) return;

    const adviceEl = document.getElementById('partOneAdviceP');
    const titleEl  = document.getElementById('partOneTitleH2');
    const bodyEl   = document.getElementById('partOneBodyDiv');

    window.renderAdvice(adviceEl, data?.advice ?? '');

    const title = (data?.title ?? '').toString().trim() || window.pickTitleFromText(data?.text ?? '');
    window.renderTitle(titleEl, title);

    const cleanedText = window.removeTitleLineFromText(data?.text ?? '', title);

    window.renderTextWithInputsDOM(bodyEl, 1, cleanedText, data?.answers ?? []);

    window.reRenderTextP?.();
};



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

    // ✅ bitta baseOrder (7,8,9...) ni chap/o'ng ikkalasiga bir xil ishlatamiz
    const baseOrder = window.getNextQuestionOrderStart();

    window.renderPartTwoTexts(textsWrap, data?.texts ?? [], baseOrder);
    window.renderPartTwoRightSide(rightWrap, data, baseOrder);
};


window.GeneratePartThreeQuestionDiv = function (data) {
    const root = document.getElementById('partThreeQuestionDiv');
    if (!root) return;

    const textsWrap    = document.getElementById('partThreeTextsWrap');
    const selectsWrap  = document.getElementById('partThreeSelectsWrap');

    window.renderPartTwoAdvice('partThreeAdviceP', data?.advice ?? '');

    if (textsWrap) textsWrap.innerHTML = '';
    if (selectsWrap) selectsWrap.innerHTML = '';

    const texts   = Array.isArray(data?.texts) ? data.texts : [];
    const answers = Array.isArray(data?.answers) ? data.answers : [];

    // LEFT: rim raqamli textlar (sizdagi funksiyalar bilan)
    texts.forEach((t, idx) => {
        const roman = window.toRoman(idx + 1);
        const item = window.buildPart3TextItem(roman, t?.text ?? '');
        textsWrap?.appendChild(item);
    });

    // RIGHT: select options (headings)
    const options = window.buildPart3OptionsFromAnswers(answers, data?.letter_array);

    // ✅ ordering endi avtomatik: sahifada bor orderlarning max+1
    const baseOrder = window.getNextQuestionOrderBase(1);

    texts.forEach((t, idx) => {
        const paragraphIndex = idx + 1;

        // ✅ answerId endi text.id bo‘ladi
        const textId = t?.id ?? paragraphIndex;

        const questionOrder = baseOrder + idx;

        const card = window.buildPart3SelectCard(
            paragraphIndex,
            3,
            textId, // ✅ textId
            options,
            questionOrder
        );

        selectsWrap?.appendChild(card);
    });
};


window.GeneratePartFourQuestionDiv = function (data) {
    const root = document.getElementById('partFourQuestionDiv');
    if (!root) return;

    const adviceEl = document.getElementById('partFourAdviceP');
    const titleEl = document.getElementById('partFourTitleH3');
    const textEl = document.getElementById('partFourTextDiv');
    const qWrap = document.getElementById('partFourQuestionsDiv');

    if (adviceEl) adviceEl.textContent = (data?.advice ?? '').toString();

    const title = window.pickTitleFromText(data?.text ?? '');
    if (titleEl) titleEl.innerHTML = `<p class="font-bold text-center ">${title}</p>`;

    const cleanedText = window.removeTitleLineFromText(data?.text ?? '', title);

    window.renderTextAsParagraphsNoBr(textEl, cleanedText ?? '');

    if (!qWrap) return;
    qWrap.innerHTML = '';

    const baseOrder = window.getNextQuestionOrderBase(1);

    const tfng = Array.isArray(data?.TFNg_tests) ? data.TFNg_tests : [];
    const mchq = Array.isArray(data?.MCHQ_tests) ? data.MCHQ_tests : [];

    let order = baseOrder;

    // 1) TFNG (3 ta)
    tfng.forEach(test => {
        const card = window.buildRadioQuestionCard(4, test, 'radio', order,'radio_tfng');
        qWrap.appendChild(card);
        order += 1;
    });

    // 2) MCHQ (4 ta)
    mchq.forEach(test => {
        const card = window.buildRadioQuestionCard(4, test, 'radio', order,'radio_mchq');
        qWrap.appendChild(card);
        order += 1;
    });
};

window.GeneratePartFiveQuestionDiv = function (data) {
    const adviceEl   = document.getElementById('partFiveAdviceP');
    const titleEl    = document.getElementById('partFiveTitleH3');   // ✅ yangi
    const readingWrap= document.getElementById('partFiveReadingWrap');
    const fillWrap   = document.getElementById('partFiveFillGapsWrap');
    const mchqWrap   = document.getElementById('partFiveMchqWrap');

    if (adviceEl) adviceEl.textContent = (data?.advice ?? '').toString();

    // ✅ Part 5 title = reading_text birinchi qatori
    const rawReading = data?.reading_text ?? '';
    const title = window.pickTitleFromText(rawReading);

    if (titleEl) titleEl.textContent = (title ?? '').toString();

    // ✅ reading_text ichidan title qatorini olib tashlaymiz (takrorlanmasin)
    const cleanedReading = window.removeTitleLineFromText(rawReading, title);

    window.renderTextAsParagraphsNoBr?.(readingWrap, cleanedReading);

    if (fillWrap) {
        fillWrap.innerHTML = '';
        const fillCard = window.buildFillGapsCard(5, data?.question_text ?? '', data?.answers ?? []);
        fillWrap.appendChild(fillCard);
    }

    const radioBaseOrder = window.getNextQuestionOrderStart(34);

    if (mchqWrap) {
        mchqWrap.innerHTML = '';
        const tests = Array.isArray(data?.tests) ? data.tests : [];

        tests.forEach((t, idx) => {
            const order = radioBaseOrder + idx;
            mchqWrap.appendChild(window.buildMchqRadioCardP(5, t, order));
        });
    }
};



