

// part one

window.ensurePartShell = function (opts) {
    const root = document.getElementById(opts.rootId);
    if (!root) return null;

    // Root: flex col, scroll ichki wrapda bo'ladi
    root.className = 'rounded-lg h-full w-full flex flex-col bg-white overflow-hidden';

    root.innerHTML = `
        <div class="flex-1 min-h-0 overflow-y-auto p-2 sm:p-4">

            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="text-sm sm:text-base font-bold text-gray-900">${opts.partLabel}</div>
            </div>

            <!-- Advice -->
            <p id="${opts.adviceId}" class="mt-2 text-black leading-relaxed"></p>

            <!-- (optional) audio -->
            <div class="mt-3 hidden">
                <audio class="w-full" controls></audio>
            </div>

            <!-- Title -->
            <h2 id="${opts.titleId}" class="mt-4 text-center font-bold text-xl sm:text-2xl text-gray-900 tracking-wide"></h2>

            <!-- Body -->
            <div id="${opts.bodyId}" class="mt-6 space-y-5 text-gray-900 text-base sm:text-lg leading-relaxed"></div>

        </div>
    `;

    return root;
};



window.renderAdvice = function (adviceEl, adviceText) {
    if (!adviceEl) return;
    adviceEl.textContent = (adviceText ?? '').toString();
};

window.renderTitle = function (titleEl, titleText) {
    if (!titleEl) return;
    titleEl.textContent = (titleText ?? '').toString();
};


window.buildInputEl = function (partNum, answer, idx, order) {
    const answerId = answer?.id ?? (idx + 1);
    const qOrder = order ?? (idx + 1);

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'inline-block h-7 sm:h-8 w-[160px] sm:w-[220px] border-2 rounded-lg border-gray-900 bg-transparent mt-1 px-1 outline-none align-baseline';
    input.dataset.answerid = String(answerId);
    input.dataset.questiontype = 'input';
    input.dataset.questionorder = String(qOrder);
    input.id = `part-${partNum}-${answerId}-input`;

    input.addEventListener('input', () => {
        window.changeInputGenerate(input, partNum);
    });

    return input;
};

window.buildOrderSpan = function (order) {
    const span = document.createElement('span');
    span.className = 'font-semibold mr-2';
    span.textContent = `${order})`;
    return span;
};

window.renderTextWithInputsDOM = function (bodyEl, partNum, rawText, answers) {
    if (!bodyEl) return;

    const text = window.normalizeText(rawText);
    const arr = Array.isArray(answers) ? answers : [];

    bodyEl.innerHTML = '';

    // faqat bo‘sh qatordan keyin paragraph
    const paragraphs = text
        .split(/\n{2,}/g)
        .map(s => s.trim())
        .filter(Boolean);

    const tokenRe = /\[(\d+)\]/g;

    paragraphs.forEach(par => {
        const p = document.createElement('p');

        let thisSize = 5;
        try {
            const draft = window.getReadingTextSizeDraft?.();
            if (draft?.textSize) thisSize = draft.textSize;
        } catch (_) {}

        p.className = 'text-' + thisSize;

        // bitta \n larni space ga aylantiramiz
        par = par.replace(/\n+/g, ' ');

        let lastIndex = 0;
        let m;

        while ((m = tokenRe.exec(par)) !== null) {
            const start = m.index;
            const end = tokenRe.lastIndex;

            const before = par.slice(lastIndex, start);
            if (before) {
                p.appendChild(document.createTextNode(before));
            }

            const n = parseInt(m[1], 10);
            const answerObj = arr[n - 1] ?? { id: n };

            p.appendChild(window.buildOrderSpan(n));
            p.appendChild(window.buildInputEl(partNum, answerObj, n - 1, n));

            lastIndex = end;
        }

        const after = par.slice(lastIndex);
        if (after) {
            p.appendChild(document.createTextNode(after));
        }

        bodyEl.appendChild(p);
    });
};

window.pickTitleFromText = function (text) {
    const t = window.normalizeText(text ?? '');
    const firstLine = t
        .split('\n')
        .map(x => x.trim())
        .find(Boolean) ?? '';

    if (firstLine.length > 50) return '';

    return firstLine;
};

window.removeTitleLineFromText = function (rawText, title) {
    const text = window.normalizeText(rawText ?? '');
    const t = (title ?? '').toString().trim();

    // Title bo'lmasa (yoki bo'sh bo'lsa) umuman qirqmaymiz
    if (!t) return text;

    const lines = text.split('\n');

    // birinchi meaningful line topamiz
    let firstIdx = -1;
    for (let i = 0; i < lines.length; i++) {
        if (lines[i].trim()) { firstIdx = i; break; }
    }
    if (firstIdx === -1) return text;

    // agar birinchi meaningful line title bilan bir xil bo'lsa, o'shani olib tashlaymiz
    if (lines[firstIdx].trim() === t) {
        lines.splice(firstIdx, 1);

        // keyingi bo'sh qatordan keyin boshlansin (optional)
        while (lines[firstIdx] !== undefined && lines[firstIdx].trim() === '') {
            lines.splice(firstIdx, 1);
        }
    }

    return lines.join('\n');
};


// =========================
// PART 2 (select) helpers - FIXED
// =========================

window.ensurePartTwoShell = function (opts) {
    const root = document.getElementById(opts.rootId);
    if (!root) return null;

    root.className = ' rounded-lg h-full w-full bg-gray-100';

    root.innerHTML = `
        <div class="h-full p-2 sm:p-2  ">
            <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%] gap-x-1 ">
                <div id="${opts.leftId}"
                     class="pr-2 border-r-2  rounded-lg bg-white shadow-md border-gray-300 text-sm sm:text-base p-2 text-gray-900 leading-relaxed overflow-y-auto">
                    <div class="mb-4">
                        <div class="font-bold text-gray-900">${opts.partLabel}</div>
                        <p id="${opts.adviceId}" class="mt-1  text-black"></p>
                    </div>
                    <div id="${opts.textsId}" class="space-y-4 py-4"></div>
                </div>

                <div id="${opts.rightId}" class="pl-2 p-2 space-y-3 text-sm sm:text-base overflow-y-auto "></div>
            </div>
        </div>
    `;

    return root;
};
 window.escapeHtml = function (s) {
    return s.replace(/[&<>"']/g, c => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
    }[c]));
}

window.renderPartTwoAdvice = function (adviceId, adviceText) {
    const el = document.getElementById(adviceId);
    if (!el) return;

    const safe = escapeHtml((adviceText ?? '').toString().replace(/\r\n?/g, '\n'));
    el.innerHTML = safe.replace(/\n/g, '<br>');
};
window.renderPartThreeAdvice = function (adviceId, adviceText) {
    const el = document.getElementById(adviceId);
    if (!el) return;

    const safe = escapeHtml((adviceText ?? '').toString().replace(/\r\n?/g, '\n'));
    el.innerHTML = safe.replace(/\n/g, '<br>');
};

window.renderPartTwoTexts = function (textsWrapEl, texts, baseOrder = 1) {
    if (!textsWrapEl) return;
    textsWrapEl.innerHTML = '';

    const size = window.getReadingTextSize?.() ?? 5;

    (Array.isArray(texts) ? texts : []).forEach((t, idx) => {
        const p = document.createElement('p');
        p.className = `text-${size}`;

        // textni tozalash (BR yo'q)
        const txt = (window.normalizeText ? window.normalizeText(t?.text ?? '') : (t?.text ?? ''))
            .toString()
            .replace(/\r\n/g, '\n')
            .replace(/\n+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        // ✅ Chap tomonda ham: "Question 7. Text 1" (bold)
        const order = baseOrder + idx;
        const label = document.createElement('span');
        label.className = 'font-bold block text-gray-900 mr-2';
        label.textContent = `Question ${order}`;

        const body = document.createElement('span');
        body.className = 'text-gray-900';
        body.textContent = txt ? ` ${txt}` : '';

        p.appendChild(label);
        p.appendChild(body);

        textsWrapEl.appendChild(p);
    });
};


window.buildAnswerLetterLabel = function (ans, letterArray) {
    const arr = Array.isArray(letterArray) ? letterArray : [];
    const idx = Number(ans?.letter_key);
    const letter = (Number.isFinite(idx) && arr[idx] != null) ? arr[idx] : '';
    const text = (ans?.answer ?? '').toString();
    return letter ? `${letter}) ${text}` : text;
};

 window.buildPartTwoSelect = function (opts) {
    const options = (Array.isArray(opts.answers) ? opts.answers : []).map(ans => ({
        value: ans?.id ?? '',
        label: window.buildAnswerLetterLabel(ans, opts.letterArray)
    }));

    const id = `part-2-${opts.textId}-select`;

    return window.buildCustomSelect({
        partNum: 2,
        id,
        answerid: opts.textId,
        order: opts.questionOrder,
        options,
        placeholder: 'Select'
    });
};

window.buildPartTwoRightCard = function (opts) {
    const card = document.createElement('div');
    card.className = 'bg-white shadow-md rounded-2xl p-3 sm:p-4';

    const wrap = document.createElement('div');
    wrap.className = 'flex flex-col gap-2';

    const title = document.createElement('span');
    title.className = 'font-bold text-gray-900';

    title.textContent = `Question ${opts.questionOrder}`;

    const cs = window.buildPartTwoSelect({
        textId: opts.textId,
        questionOrder: opts.questionOrder,
        answers: opts.answers,
        letterArray: opts.letterArray
    });

    wrap.appendChild(title);
    wrap.appendChild(cs);
    card.appendChild(wrap);

    return card;
};

window.renderPartTwoRightSide = function (rightWrapEl, data, baseOrder = null) {
    if (!rightWrapEl) return;
    rightWrapEl.innerHTML = '';

    const answers = Array.isArray(data?.answers) ? data.answers : [];
    const texts   = Array.isArray(data?.texts) ? data.texts : [];

    // ✅ Agar tashqaridan baseOrder kelmasa, o'zi topadi
    const startOrder = (typeof baseOrder === 'number' && Number.isFinite(baseOrder))
        ? baseOrder
        : window.getNextQuestionOrderStart();

    for (let i = 0; i < texts.length; i++) {
        const textObj = texts[i] ?? {};
        const textId = textObj?.id ?? (i + 1); // ✅ text.id

        const card = window.buildPartTwoRightCard({
            indexLabel: (i + 1),
            textId,
            questionOrder: startOrder + i, // ✅ chap bilan bir xil order
            answers,
            letterArray: Array.isArray(data?.letter_array) ? data.letter_array : []
        });

        rightWrapEl.appendChild(card);
    }
};



// =========================
// PART 3 (select) helpers
// =========================


window.toRoman = function (n) {
    const map = [
        [1000,'M'],[900,'CM'],[500,'D'],[400,'CD'],
        [100,'C'],[90,'XC'],[50,'L'],[40,'XL'],
        [10,'X'],[9,'IX'],[5,'V'],[4,'IV'],[1,'I'],
    ];
    let out = '';
    let num = n;
    for (const [val, sym] of map) {
        while (num >= val) { out += sym; num -= val; }
    }
    return out;
};

window.buildPart3TextItem = function (roman, text) {
    const p = document.createElement('p');
    p.className = 'text-' + window.getReadingTextSize();

    const label = document.createElement('span');
    label.className = 'font-bold mr-2';
    label.textContent = roman + '.';

    const body = document.createElement('span');
    body.textContent = window.normalizeText(text).replace(/\n+/g, ' '); // br qilmaymiz

    p.appendChild(label);
    p.appendChild(body);
    return p;
};

window.buildPart3SelectCard = function (paragraphIndex, partNum, textId, options, questionOrder) {
    const size = window.getReadingTextSize?.() ?? 7;

    const card = document.createElement('div');
    card.className = 'bg-white shadow-md rounded-2xl p-3 sm:p-4';

    const row = document.createElement('div');
    row.className = 'flex flex-col gap-2';

    const titleP = document.createElement('p');
    titleP.className = `text-${size} font-semibold text-gray-900`;
    titleP.textContent = `${questionOrder}. Paragraph ${window.toRoman(paragraphIndex)}`;

    const id = `part-${partNum}-${textId}-select`;

    const cs = window.buildCustomSelect({
        partNum,
        id,
        answerid: textId,
        order: questionOrder,
        options,
        placeholder: 'Select'
    });

    row.appendChild(titleP);
    row.appendChild(cs);
    card.appendChild(row);

    return card;
};


window.buildPart3OptionsFromAnswers = function (answers, letterArray) {
    const arr = Array.isArray(answers) ? answers : [];
    return arr.map(a => ({
        value: a?.id ?? '',      // ✅ heading id
        label: window.buildAnswerLetterLabel(a, letterArray)   // ✅ "A) ...."
    }));
};


// ----------------------
// part four
// ----------------------

// =========================
// PART 4: Question/Option cleanup helpers (NEW)
// =========================

window.stripLeadingOrderFromQuestion = function (text) {
    const s = (text ?? '').toString().trim();
    return s.replace(/^\s*\d+\s*[\.\)\:\-]\s*/g, '').trim();
};

window.stripLeadingLetterFromOption = function (text) {
    const s = (text ?? '').toString().trim();
    return s.replace(/^\s*\(?[A-D]\)?\s*[\)\.\:\-]\s*/i, '').trim();
};

window.formatQuestionWithOrder = function (order, questionText) {
    const clean = window.stripLeadingOrderFromQuestion(questionText);
    const o = Number(order);
    if (Number.isFinite(o) && o > 0) return `${o}. ${clean}`;
    return clean;
};

window.renderTextAsParagraphsNoBr = function (wrapEl, rawText) {
    if (!wrapEl) return;

    const text = window.normalizeText(rawText);
    wrapEl.innerHTML = '';

    const size = window.getReadingTextSize();

    const paragraphs = text
        .split(/\n{2,}/g)
        .map(s => s.trim())
        .filter(Boolean)
        .map(p => p.replace(/\n+/g, ' '));

    paragraphs.forEach(pTxt => {
        const p = document.createElement('p');
        p.className = 'text-' + size;
        p.textContent = pTxt;
        wrapEl.appendChild(p);
    });
};

window.buildRadioQuestionCard = function (partNum, test, questionType, order, Type) {
    const size = window.getReadingTextSize();

    const card = document.createElement('div');
    const answerId = test?.id;
    const qOrder = order;

    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-4';
    card.dataset.answerid = String(answerId);
    card.dataset.questiontype = 'radio';
    card.dataset.questionorder = String(qOrder);
    card.id = `part-${partNum}-${answerId}-radio`;

    const qTitle = document.createElement('p');
    qTitle.className = `text-${size} text-gray-900`;

    const rawQ = (test?.question ?? '').toString();
    qTitle.textContent = window.formatQuestionWithOrder(qOrder, rawQ);

    const answersWrap = document.createElement('div');
    const isMchq = Type === 'radio_mchq';

    const keys = isMchq
        ? ['answer_1', 'answer_2', 'answer_3', 'answer_4']
        : ['answer_1', 'answer_2', 'answer_3'];

    if (isMchq) {
        answersWrap.className = 'mt-4 space-y-3 text-gray-900';

        keys.forEach((k, i) => {
            const p = document.createElement('p');
            p.className = `text-${size}`;

            const label = document.createElement('label');
            label.className = 'flex items-start gap-3 cursor-pointer';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `part-${partNum}-${answerId}-radio`;
            input.value = k;
            input.className = 'mt-1 accent-gray-900';

            input.addEventListener('change', () => {
                if (input.checked) window.changeRadioInputGenerate(input, partNum);
            });

            const span = document.createElement('span');
            const letter = String.fromCharCode(65 + i); // A,B,C,D

            const optTextRaw = (test?.[k] ?? '').toString();
            const optTextClean = window.stripLeadingLetterFromOption(optTextRaw);

            span.innerHTML = `<span class=" ">${letter})</span> ${optTextClean}`;

            label.appendChild(input);
            label.appendChild(span);
            p.appendChild(label);
            answersWrap.appendChild(p);
        });
    } else {
        // TFNG (3 variant) - horizontal
        answersWrap.className = 'mt-4 flex flex-wrap items-center gap-x-10 gap-y-3 text-gray-900';

        keys.forEach((k, i) => {
            const p = document.createElement('p');
            p.className = `text-${size}`;

            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 cursor-pointer';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `part-${partNum}-${answerId}-radio`;
            input.value = k;
            input.className = 'accent-gray-900';

            input.addEventListener('change', () => {
                if (input.checked) window.changeRadioInputGenerate(input, partNum);
            });

            const span = document.createElement('span');
            const letter = String.fromCharCode(65 + i); // A,B,C
            const optTextRaw = (test?.[k] ?? '').toString();
            const optTextClean = window.stripLeadingLetterFromOption(optTextRaw);

            // ✅ TFNG ham xohlasangiz "A) True" ko‘rinishida qoladi
            span.textContent = `${letter}) ${optTextClean}`;

            label.appendChild(input);
            label.appendChild(span);
            p.appendChild(label);
            answersWrap.appendChild(p);
        });
    }

    card.appendChild(qTitle);
    card.appendChild(answersWrap);
    return card;
};


// -----------------------------
// part five -------
// -----------------------------


window.buildOrderSpan = function (order) {
    const span = document.createElement('span');
    span.className = 'font-semibold mr-2';
    span.textContent = `${order})`;
    return span;
};

window.renderTextWithInputsDOM_NoSingleBr = function (wrapEl, partNum, rawText, answers) {
    if (!wrapEl) return;

    const size = window.getReadingTextSize();
    const text = window.normalizeText(rawText);
    const arr = Array.isArray(answers) ? answers : [];

    wrapEl.innerHTML = '';

    const paragraphs = text
        .split(/\n{2,}/g)
        .map(s => s.trim())
        .filter(Boolean);

    const tokenRe = /\[(\d+)\]/g;

    paragraphs.forEach(par => {
        const p = document.createElement('p');
        p.className = 'text-' + size;

        let lastIndex = 0;
        let m;

        while ((m = tokenRe.exec(par)) !== null) {
            const start = m.index;
            const end = tokenRe.lastIndex;

            const before = par.slice(lastIndex, start);
            if (before) {
                p.appendChild(document.createTextNode(before.replace(/\n+/g, ' ')));
            }

            const n = parseInt(m[1], 10);
            const answerObj = arr.find(a => String(a?.count_number) === String(n)) || arr[n - 1] || { id: n };

            p.appendChild(window.buildOrderSpan(n));
            p.appendChild(window.buildInputEl(partNum, answerObj, n - 1, n));

            lastIndex = end;
        }

        const after = par.slice(lastIndex);
        if (after) {
            p.appendChild(document.createTextNode(after.replace(/\n+/g, ' ')));
        }

        wrapEl.appendChild(p);
    });
};
window.buildMchqRadioCardP = function (partNum, test, order) {
    const size = window.getReadingTextSize();
    const card = document.createElement('div');
    const answerId = test?.id;

    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-4';
    card.dataset.answerid = String(answerId);
    card.dataset.questiontype = 'radio';
    card.dataset.questionorder = String(order);
    card.id = `part-${partNum}-${answerId}-radio`;

    const qP = document.createElement('p');
    // ✅ bold emas
    qP.className = 'text-' + size + ' text-gray-900';

    // ✅ order raqami qo'shildi (oddiy, bold emas)
    const qText = (test?.question ?? '').toString();
    qP.textContent = `${order}. ${qText}`;

    const answersWrap = document.createElement('div');
    answersWrap.className = 'mt-4 space-y-3 text-gray-900';

    ['answer_1', 'answer_2', 'answer_3', 'answer_4'].forEach((k, i) => {
        const label = document.createElement('label');
        label.className = 'flex items-center gap-3 cursor-pointer';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `part-${partNum}-${answerId}-radio`;
        input.value = k;
        input.className = 'mt-1 accent-gray-900';

        input.addEventListener('change', () => {
            if (input.checked) window.changeRadioInputGenerate(input, partNum);
        });

        const span = document.createElement('span');
        // sizda oldin letter ishlatilmagan, xuddi shunday qoldirdim
        span.innerHTML = `${(test?.[k] ?? '').toString()}`;

        label.appendChild(input);
        label.appendChild(span);
        answersWrap.appendChild(label);
    });

    card.appendChild(qP);
    card.appendChild(answersWrap);
    return card;
};


window.buildFillGapsCard = function (partNum, questionText, answers) {
    const card = document.createElement('div');
    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-6 text-gray-900 leading-relaxed';

    const inner = document.createElement('div');
    card.appendChild(inner);

    window.renderTextWithInputsDOM_NoSingleBr(inner, partNum, questionText, answers);

    return card;
};


// ------------------------------
//  general
// ------------------------------
window.getNextQuestionOrderBase = function (fallbackStart = 1) {
    const nodes = document.querySelectorAll('[data-questionorder]');
    let maxOrder = 0;

    nodes.forEach(el => {
        const n = parseInt(el.dataset.questionorder, 10);
        if (Number.isFinite(n) && n > maxOrder) maxOrder = n;
    });

    return maxOrder > 0 ? (maxOrder + 1) : fallbackStart;
};

window.getNextQuestionOrderStart = function () {
    const nodes = document.querySelectorAll('[data-questionorder]');
    let maxOrder = 0;

    nodes.forEach(el => {
        const n = parseInt(el.dataset.questionorder, 10);
        if (Number.isFinite(n) && n > maxOrder) maxOrder = n;
    });

    return maxOrder + 1; // ✅ keyingisi
};

window.normalizeText = function (s) {
    return (s ?? '').toString().replace(/\r\n/g, '\n');
};

window.getReadingTextSize = function () {
    let thisSize = 5;
    try {
        const draft = window.getReadingTextSizeDraft?.();
        if (draft?.textSize) thisSize = draft.textSize;
    } catch (_) {}
    return thisSize;
};

// ---------- STORAGE HELPERS (NEW) ----------

window.setReadingDraft = function (draft) {
    try { localStorage.setItem(window.STORAGE_KEY, JSON.stringify(draft || {})); }
    catch (_) {}
};

window.detectPartKeyFromPartNum = function (partNum) {
    const map = {
        1: 'part_one',
        2: 'part_two',
        3: 'part_three',
        4: 'part_four',
        5: 'part_five',
    };
    return map[partNum] || null;
};

window.ensurePartDraft = function (draft, partKey, partIdFallback) {
    if (!partKey) return null;
    if (!draft[partKey]) draft[partKey] = { id: partIdFallback || 0, question_data: {} };
    if (!draft[partKey].question_data) draft[partKey].question_data = {};
    if (!draft[partKey].id) draft[partKey].id = partIdFallback || 0;
    return draft[partKey];
};

window.writeQuestionToDraft = function (partNum, elId, payload) {
    const partKey = window.detectPartKeyFromPartNum(partNum);
    if (!partKey) return;

    const draft = window.getReadingDraft();
    const partId =
        window.__READING__?.partsData?.[Object.keys(window.__READING__?.partsData || {}).find(k => {
            const pn = ({ partOne:1, partTwo:2, partThree:3, partFour:4, partFive:5 })[k];
            return pn === partNum;
        })]?.id || 0;

    const part = window.ensurePartDraft(draft, partKey, partId);
    if (!part) return;

    part.question_data[elId] = {
        order: Number(payload.order),
        id: Number(payload.id),
        type: String(payload.type),
        value: payload.value
    };

    window.setReadingDraft(draft);
};


//------------------------------------------------
// finish helper
//--------------------------------------------------


// ===============================
// HELPERS: Answer checks + labels
// ===============================

window.isAnsweredValue = function (type, value) {
    if (type === 'input') return (value ?? '').toString().trim() !== '';
    if (type === 'select') return !(value === '' || value === null || value === undefined);
    if (type === 'radio') return (value ?? '').toString().trim() !== '';
    return (value ?? '').toString().trim() !== '';
};

window.countAnsweredInDraftPart = function (draftPart) {
    const qd = draftPart?.question_data;
    if (!qd) return 0;

    let answered = 0;
    Object.keys(qd).forEach(elId => {
        const item = qd[elId];
        if (!item) return;
        if (window.isAnsweredValue(item.type, item.value)) answered += 1;
    });
    return answered;
};

// DOM’dan real ko‘rinadigan textni olish (select option text / radio label text)
window.getDisplayValueFromDOM = function (order) {
    const el = document.querySelector(`[data-questionorder="${order}"]`);
    if (!el) return null;

    const type = el.dataset.questiontype;

    // input (cardning o‘zi data-questionorder bo‘lishi mumkin, ichidan input topamiz)
    if (type === 'input') {
        const inp = el.matches('input') ? el : el.querySelector('input[type="text"]');
        if (!inp) return null;
        const v = (inp.value ?? '').toString().trim();
        return v ? v : null;
    }

    // select
    if (type === 'select') {
        const sel = el.matches('select') ? el : el.querySelector('select');
        if (!sel) return null;
        const opt = sel.options?.[sel.selectedIndex];
        const txt = (opt?.textContent ?? '').toString().trim();
        // "Select" bo‘lsa answered emas
        if (!txt || txt.toLowerCase() === 'select') return null;
        return txt;
    }

    // radio (cardda data-questionorder bo‘ladi, ichidan checkedni topamiz)
    if (type === 'radio') {
        const checked = el.querySelector('input[type="radio"]:checked');
        if (!checked) return null;

        // label textni olamiz: <label> ichidagi text
        const label = checked.closest('label');
        if (label) {
            const t = (label.textContent ?? '').replace(/\s+/g, ' ').trim();
            return t || null;
        }

        return null;
    }

    return null;
};

// Draftdan value bor bo‘lsa, DOM orqali chiroyli textga map qilib berish (fallback)
window.getDisplayValueFromDraftAndDOM = function ({ order, type, value }) {
    // Avval DOM (eng ishonchli UI text)
    const dom = window.getDisplayValueFromDOM(order);
    if (dom) return dom;

    // Keyin fallbacklar:
    if (type === 'input') {
        const v = (value ?? '').toString().trim();
        return v || '';
    }

    // select: value id bo‘lsa (65...) option textga map qilamiz
    if (type === 'select') {
        const el = document.querySelector(`[data-questionorder="${order}"]`);
        const sel = el?.matches('select') ? el : el?.querySelector?.('select');
        if (sel && value != null && value !== '') {
            const opt = Array.from(sel.options || []).find(o => String(o.value) === String(value));
            const txt = (opt?.textContent ?? '').toString().trim();
            return txt || String(value);
        }
        return '';
    }

    // radio: value "answer_1" bo‘lsa label textni topamiz
    if (type === 'radio') {
        const el = document.querySelector(`[data-questionorder="${order}"]`);
        const input = el?.querySelector?.(`input[type="radio"][value="${CSS.escape(String(value))}"]`);
        const label = input?.closest?.('label');
        const txt = (label?.textContent ?? '').replace(/\s+/g, ' ').trim();
        return txt || '';
    }

    return (value ?? '').toString();
};

// ===============================
// ACCORDION ANIMATION
// ===============================

window.closeFinishAccordionBody = function (body) {
    if (!body) return;
    body.style.maxHeight = '0px';
    body.classList.add('opacity-0', '-translate-y-1');
    setTimeout(() => {
        body.classList.add('hidden');
    }, 180);
};

window.openFinishAccordionBody = function (body) {
    if (!body) return;
    body.classList.remove('hidden');
    // first set to 0 then expand (smooth)
    body.style.maxHeight = '0px';
    body.classList.add('opacity-0', '-translate-y-1');

    requestAnimationFrame(() => {
        const h = body.scrollHeight;
        body.style.maxHeight = h + 'px';
        body.classList.remove('opacity-0', '-translate-y-1');
        body.classList.add('opacity-100', 'translate-y-0');
    });
};

// ===============================
// UI BUILDERS
// ===============================

window.makeFinishQuestionRow = function ({ order, answered, displayValue }) {
    const row = document.createElement('div');

    // ✅ unanswered highlight
    row.className = [
        'flex items-center justify-between rounded-xl px-3 py-2 border',
        answered
            ? 'border-gray-200 bg-white'
            : 'border-amber-200 bg-amber-50'
    ].join(' ');

    const left = document.createElement('div');
    left.className = 'flex items-center gap-3 min-w-0';

    const ord = document.createElement('span');
    ord.className = 'font-semibold text-gray-700';
    ord.textContent = `${order}.`;

    const val = document.createElement('span');
    val.className = [
        'text-sm truncate',
        answered ? 'text-gray-900' : 'text-amber-700'
    ].join(' ');
    val.textContent = answered ? (displayValue || 'Answered') : 'Not answered';

    left.appendChild(ord);
    left.appendChild(val);

    // ✅ button styling:
    // answered => rangsiz/neutral
    // unanswered => rangli (cyan) va ko‘proq ko‘rinsin
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = [
        'text-xs px-2.5 py-1.5 rounded-lg font-semibold transition',
        answered
            ? 'border border-gray-300 text-gray-700 hover:bg-gray-50'
            : 'bg-cyan-500 text-white hover:bg-cyan-600 shadow-sm'
    ].join(' ');
    btn.textContent = 'Show';

    btn.addEventListener('click', () => {
        window.cancelFinishModal();
        setTimeout(() => window.focusQuestionByOrder?.(order), 220);
    });

    row.appendChild(left);
    row.appendChild(btn);
    return row;
};

window.makeFinishAccordionPart = function ({ partNum, total, answered, items, openByDefault }) {
    const wrap = document.createElement('div');
    wrap.className = 'border border-gray-200 rounded-2xl overflow-hidden bg-white';

    const header = document.createElement('button');
    header.type = 'button';
    header.className =
        'w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100';

    const status =
        total === 0 ? 'No questions' : (answered === 0 ? 'Empty' : (answered >= total ? 'Done' : 'In progress'));

    const statusCls =
        total === 0 ? 'border-gray-300 text-gray-600' :
            answered === 0 ? 'border-gray-300 text-gray-700' :
                answered >= total ? 'border-green-400 text-green-700' :
                    'border-blue-400 text-blue-700';

    header.innerHTML = `
        <div class="flex items-center gap-3 font-semibold text-gray-900">
            <span class="finish-chevron transition-transform duration-200">&gt;</span>
            <span>Part ${partNum}</span>
            <span class="text-sm font-medium text-gray-500">${answered} / ${total}</span>
        </div>
        <span class="text-xs px-2 py-0.5 rounded-full border ${statusCls}">${status}</span>
    `;

    const body = document.createElement('div');
    body.className =
        'finish-accordion-body hidden px-4 py-3 space-y-2 bg-white ' +
        'transition-all duration-200 overflow-hidden opacity-0 -translate-y-1';
    body.style.maxHeight = '0px';

    items.forEach(it => body.appendChild(it));

    function closeAllOthers() {
        document.querySelectorAll('.finish-accordion-body').forEach(b => {
            if (b !== body && !b.classList.contains('hidden')) window.closeFinishAccordionBody(b);
        });
        document.querySelectorAll('.finish-chevron').forEach(ch => {
            // faqat shu wrapper ichidagi chevronni boshqarish uchun quyida alohida qilamiz
        });
    }

    header.addEventListener('click', () => {
        const chevron = header.querySelector('.finish-chevron');

        if (body.classList.contains('hidden')) {
            closeAllOthers();

            // boshqa chevronlarni reset
            document.querySelectorAll('#finishModalBody .finish-chevron').forEach(ch => {
                ch.classList.remove('rotate-90');
            });

            window.openFinishAccordionBody(body);
            chevron?.classList.add('rotate-90');
        } else {
            window.closeFinishAccordionBody(body);
            chevron?.classList.remove('rotate-90');
        }
    });

    wrap.appendChild(header);
    wrap.appendChild(body);

    // optional default open
    if (openByDefault) {
        requestAnimationFrame(() => header.click());
    }

    return wrap;
};

// ===============================
// regenerateFinishModal (FULL)
// ===============================

window.getUniqueOrdersInPanel = function (panelEl) {
    if (!panelEl) return [];
    const nodes = panelEl.querySelectorAll('[data-questionorder]');
    const set = new Set();

    nodes.forEach(el => {
        const v = el.dataset.questionorder;
        const n = v ? parseInt(v, 10) : NaN;
        if (Number.isFinite(n)) set.add(n);
    });

    return Array.from(set).sort((a, b) => a - b);
};

window.regenerateFinishModal = function (availableParts) {
    const body = document.getElementById('finishModalBody');
    if (!body) return;

    body.innerHTML = '';

    const draft = window.getReadingDraft?.() || {};
    const keyMap = { 1:'part_one', 2:'part_two', 3:'part_three', 4:'part_four', 5:'part_five' };

    (availableParts || []).forEach((p, idx) => {
        const panel = document.getElementById(p.panelId);

        // total: panel ichidagi unique orderlar
        const orders = window.getUniqueOrdersInPanel(panel);
        const total = orders.length;

        const partDraft = draft?.[keyMap[p.num]];
        const answeredCount = window.countAnsweredInDraftPart(partDraft);

        // orderlar bo‘yicha rows
        const items = orders.map(order => {
            const q = Object.values(partDraft?.question_data || {}).find(x => Number(x?.order) === Number(order));

            const type = q?.type || (document.querySelector(`[data-questionorder="${order}"]`)?.dataset?.questiontype ?? '');
            const val  = q?.value;

            const answered = !!q && window.isAnsweredValue(type, val);

            // ✅ display label fix (select/radio => option/label text)
            const displayValue = answered
                ? window.getDisplayValueFromDraftAndDOM({ order, type, value: val })
                : '';

            return window.makeFinishQuestionRow({
                order,
                answered,
                displayValue
            });
        });

        const partEl = window.makeFinishAccordionPart({
            partNum: p.num,
            total,
            answered: answeredCount,
            items,
            openByDefault: idx === 0 // birinchi part default ochiq bo‘lsin (xohlasangiz olib tashlang)
        });

        body.appendChild(partEl);
    });
};


