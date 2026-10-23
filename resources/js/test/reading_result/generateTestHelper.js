window.getReadingDraft = function () {
    const el = document.getElementById('reading_draft');
    if (!el) return {};
    const raw = (el.value ?? '').trim();
    if (!raw || raw === 'null' || raw === 'undefined') return {};
    try { return JSON.parse(raw); } catch (_) { return {}; }
};

window.normalizeText = function (s) {
    return (s ?? '').toString().replace(/\r\n/g, '\n');
};

window.getReadingTextSize = window.getReadingTextSize || function () {
    return 7;
};

window.escapeHtml = function (s) {
    return String(s ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
};
// =========================
// CUSTOM SELECT helpers (sync UI + border/ring to visible button)
// =========================

window.getCustomSelectRootFromNative = function (nativeSelect) {
    if (!nativeSelect) return null;
    return nativeSelect.closest('[data-cs-root="1"]');
};

window.getCustomSelectBtnFromNative = function (nativeSelect) {
    const root = window.getCustomSelectRootFromNative(nativeSelect);
    if (!root) return null;
    return root.querySelector('[data-cs-btn="1"]');
};

window.getCustomSelectLabelFromNative = function (nativeSelect) {
    const root = window.getCustomSelectRootFromNative(nativeSelect);
    if (!root) return null;
    return root.querySelector('[data-cs-label="1"]');
};

window.syncCustomSelectLabelFromNative = function (nativeSelect, placeholder = 'Select') {
    if (!nativeSelect) return;

    const label = window.getCustomSelectLabelFromNative(nativeSelect);
    if (!label) return;

    const opt = nativeSelect.options?.[nativeSelect.selectedIndex];
    const txt = (opt?.textContent ?? '').toString().trim();

    if (!txt || txt.toLowerCase() === 'select') label.textContent = placeholder;
    else label.textContent = txt;
};

// 🔥 select natijasidagi border/ring ni ko‘rinadigan custom btn’ga ham qo‘llash
window.applyResultClassToCustomSelectBtn = function (nativeSelect, status, isCorrect) {
    const btn = window.getCustomSelectBtnFromNative(nativeSelect);
    if (!btn) return;

    btn.classList.remove('border-green-500','border-red-500','border-gray-300','border-gray-400');
    btn.classList.remove('ring-1','ring-green-300','ring-red-300','ring-gray-200');

    if (status !== 'answered') {
        btn.classList.add('border-gray-300','ring-1','ring-gray-200');
        return;
    }

    if (Number(isCorrect) === 1) btn.classList.add('border-green-500','ring-1','ring-green-300');
    else if (Number(isCorrect) === 0) btn.classList.add('border-red-500','ring-1','ring-red-300');
    else btn.classList.add('border-gray-300','ring-1','ring-gray-200');
};


window.clearAnswerTextClasses = function (el) {
    if (!el) return;
    for (let i = 1; i <= 9; i++) el.classList.remove(`answer-text-${i}`);
};

window.applyAnswerTextSizeClass = function (el, size) {
    if (!el) return;
    window.clearAnswerTextClasses(el);
    el.classList.add('answer-text');
    el.classList.add(`answer-text-${size}`);
};

window.__CUSTOM_SELECT_RESULT_BOUND__ = window.__CUSTOM_SELECT_RESULT_BOUND__ || false;

window.bindCustomSelectGlobal_Result = function () {
    if (window.__CUSTOM_SELECT_RESULT_BOUND__) return;
    window.__CUSTOM_SELECT_RESULT_BOUND__ = true;

    document.addEventListener('click', (e) => {
        document.querySelectorAll('[data-cs-root="1"]').forEach(root => {
            if (!root.contains(e.target)) {
                root.classList.remove('cs-open');
                const menu = root.querySelector('[data-cs-menu="1"]');
                if (menu) menu.classList.add('hidden');
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-cs-root="1"]').forEach(root => {
                root.classList.remove('cs-open');
                const menu = root.querySelector('[data-cs-menu="1"]');
                if (menu) menu.classList.add('hidden');
            });
        }
    });
};

window.buildHiddenSelect_Result = function ({ id, answerid, order, options }) {
    const select = document.createElement('select');
    select.id = id;

    // 🔑 backend/result funksiyalar uchun shu datasetlar kerak
    select.dataset.answerid = String(answerid);
    select.dataset.questiontype = 'select';
    select.dataset.questionorder = String(order);

    // DOM’da bo‘ladi, lekin ko‘rinmaydi
    select.className = 'sr-only';
    select.tabIndex = -1;

    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = 'Select';
    select.appendChild(ph);

    (Array.isArray(options) ? options : []).forEach(opt => {
        const o = document.createElement('option');
        o.value = String(opt.value ?? '');
        o.textContent = String(opt.label ?? '');
        select.appendChild(o);
    });

    // ✅ result page: native select disabled bo‘lishi kerak
    select.disabled = true;
    select.setAttribute('disabled', 'disabled');

    return select;
};

// 🔥 result-mode custom select (disabled UI, wrap, answer-text sizing)
window.buildCustomSelect_Result = function ({
                                                id,
                                                answerid,
                                                order,
                                                options,
                                                placeholder = 'Select',
                                            }) {
    window.bindCustomSelectGlobal_Result();

    const root = document.createElement('div');
    root.dataset.csRoot = '1';
    root.dataset.csFor = String(id);
    root.className = 'relative';

    const nativeSelect = window.buildHiddenSelect_Result({ id, answerid, order, options });

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.dataset.csBtn = '1';

    // ✅ disabled look (result page)
    btn.className = [
        'w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2',
        'text-left flex items-center justify-between gap-3',
        'shadow-sm',
        'cursor-not-allowed opacity-95',
        'transition'
    ].join(' ');
    btn.disabled = true;
    btn.setAttribute('disabled', 'disabled');

    const left = document.createElement('div');
    left.className = 'min-w-0 flex-1';

    // ❗ div qildik (textSize.js barcha span’larni text-7 qilib yuboradi)
    const labelEl = document.createElement('div');
    labelEl.dataset.csLabel = '1';
    labelEl.className = 'block whitespace-normal break-words';

    // default label
    labelEl.textContent = placeholder;

    // answer-text sizing
    window.applyAnswerTextSizeClass(labelEl, window.getReadingTextSize?.() ?? 7);

    left.appendChild(labelEl);

    const chev = document.createElement('span');
    chev.className = 'shrink-0 text-gray-400';
    chev.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6"/>
      </svg>
    `;

    btn.appendChild(left);
    btn.appendChild(chev);

    // menu (resultda ishlamaydi)
    const menu = document.createElement('div');
    menu.dataset.csMenu = '1';
    menu.className = 'hidden';

    // ✅ native select value o‘zgarsa label yangilansin
    nativeSelect.addEventListener('change', () => {
        window.syncCustomSelectLabelFromNative(nativeSelect, placeholder);
    });

    root.appendChild(nativeSelect);
    root.appendChild(btn);
    root.appendChild(menu);

    return root;
};


// ORDER helpers
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
    return maxOrder + 1;
};

// Result borders
window.applyResultClassToEl = function (el, status, isCorrect) {
    if (!el) return;

    el.classList.remove('border-green-500','border-red-500','border-gray-300','border-gray-400');
    el.classList.remove('ring-1','ring-green-300','ring-red-300','ring-gray-200');

    if (status !== 'answered') {
        el.classList.add('border-gray-300','ring-1','ring-gray-200');

        // ✅ custom select btn ham
        if (el.tagName === 'SELECT') {
            window.applyResultClassToCustomSelectBtn(el, status, isCorrect);
        }
        return;
    }

    if (Number(isCorrect) === 1) el.classList.add('border-green-500','ring-1','ring-green-300');
    else if (Number(isCorrect) === 0) el.classList.add('border-red-500','ring-1','ring-red-300');
    else el.classList.add('border-gray-300','ring-1','ring-gray-200');

    // ✅ custom select btn ham
    if (el.tagName === 'SELECT') {
        window.applyResultClassToCustomSelectBtn(el, status, isCorrect);
    }
};

window.removeOldFeedback = function (wrap) {
    if (!wrap) return;
    wrap.querySelectorAll('[data-feedback="1"]').forEach(el => el.remove());
};

window.buildFeedbackEl = function ({ status, is_correct, correctText, inline = false }) {
    const box = document.createElement('span');
    box.dataset.feedback = '1';

    box.className = inline
        ? 'ml-2 inline-flex flex-wrap items-center gap-2 text-[11px] sm:text-xs align-middle'
        : 'mt-2 text-xs sm:text-sm';

    const safeCorrect = window.escapeHtml(correctText ?? '');

    if (status !== 'answered') {
        box.innerHTML = `
          <span class="text-gray-500 font-semibold">Not answered</span>
          <span class="text-gray-600">|</span>
          <span class="text-gray-700 font-semibold">Correct answer:</span>
          <span class="text-green-600 font-bold">${safeCorrect}</span>
        `;
        return box;
    }

    if (Number(is_correct) === 1) {
        box.innerHTML = `<span class="text-green-600 font-bold">Correct</span>`;
        return box;
    }

    box.innerHTML = `
      <span class="text-red-600 font-bold">Incorrect</span>
      <span class="text-gray-600">|</span>
      <span class="text-gray-700 font-semibold">Correct answer:</span>
      <span class="text-green-600 font-bold">${safeCorrect}</span>
    `;
    return box;
};

// Part 1 helpers

window.ensurePartShell = function (opts) {
    const root = document.getElementById(opts.rootId);
    if (!root) return null;

    root.className = 'rounded-lg h-full w-full flex flex-col bg-white overflow-hidden';

    root.innerHTML = `
        <div class="flex-1 min-h-0 overflow-y-auto p-2 sm:p-4">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="text-sm sm:text-base font-bold text-gray-900">${opts.partLabel}</div>
            </div>

            <p id="${opts.adviceId}" class="mt-2 text-xs sm:text-sm text-black leading-relaxed"></p>

            <h2 id="${opts.titleId}" class="mt-4 text-center font-bold text-xl sm:text-2xl text-gray-900 tracking-wide"></h2>

            <div id="${opts.bodyId}" class="mt-6 space-y-5 text-gray-900 text-base sm:text-lg leading-relaxed"></div>
        </div>
    `;

    return root;
};


window.renderAdvice = function (adviceEl, adviceText) {
    if (adviceEl) adviceEl.textContent = (adviceText ?? '').toString();
};

window.pickTitleFromText = function (text) {
    const t = window.normalizeText(text ?? '');
    const firstLine = t
        .split('\n')
        .map(x => x.trim())
        .find(Boolean) ?? '';

    // Agar birinchi meaningful qator 50 dan uzun bo'lsa title bo'lmaydi
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



window.renderTitle = function (titleEl, titleText) {
    if (titleEl) titleEl.textContent = (titleText ?? '').toString();
};

window.buildOrderSpan = function (order) {
    const span = document.createElement('span');
    span.className = 'font-semibold mr-2';
    span.textContent = `${order})`;
    return span;
};

window.buildInputEl = function (partNum, answer, idx, order) {
    const answerId = answer?.id ?? (idx + 1);
    const qOrder = order ?? (idx + 1);

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'inline-block h-7 sm:h-8 w-[140px] sm:w-[200px] border-2 rounded-lg border-gray-900 bg-transparent px-1 outline-none align-baseline';
    input.dataset.answerid = String(answerId);
    input.dataset.questiontype = 'input';
    input.dataset.questionorder = String(qOrder);
    input.id = `part-${partNum}-${answerId}-input`;
    input.disabled = true;
    input.setAttribute('disabled', 'disabled');
    return input;
};

window.renderTextWithInputsDOM = function (bodyEl, partNum, rawText, answers) {
    if (!bodyEl) return;

    const text = window.normalizeText(rawText);
    const arr = Array.isArray(answers) ? answers : [];
    bodyEl.innerHTML = '';

    const paragraphs = text.split(/\n{2,}/g).map(s => s.trim()).filter(Boolean);
    const tokenRe = /\[(\d+)\]/g;

    paragraphs.forEach(par => {
        const p = document.createElement('p');
        p.className = 'text-' + window.getReadingTextSize();
        par = par.replace(/\n+/g, ' ');

        let lastIndex = 0, m;
        while ((m = tokenRe.exec(par)) !== null) {
            const start = m.index;
            const end = tokenRe.lastIndex;

            const before = par.slice(lastIndex, start);
            if (before) p.appendChild(document.createTextNode(before));

            const n = parseInt(m[1], 10);
            const answerObj = arr.find(x => String(x?.count_number) === String(n)) || arr[n - 1] || { id: n };

            p.appendChild(window.buildOrderSpan(n));
            p.appendChild(window.buildInputEl(partNum, answerObj, n - 1, n));

            lastIndex = end;
        }

        const after = par.slice(lastIndex);
        if (after) p.appendChild(document.createTextNode(after));
        bodyEl.appendChild(p);
    });
};

// Part 2 shell + select
window.ensurePartTwoShell = function (opts) {
    const root = document.getElementById(opts.rootId);
    if (!root) return null;

    root.className = 'rounded-lg h-full w-full ';
    root.innerHTML = `
        <div class="  h-full p-2 sm:p-2 ">
            <div class="grid h-full grid-cols-1   lg:grid-cols-[60%_40%] gap-x-2 ">
                <div id="${opts.leftId}"
                     class="pr-2 shadow-md bg-white rounded-lg text-sm sm:text-base p-2 text-gray-900 leading-relaxed overflow-y-auto">
                    <div class="mb-4">
                        <div class="font-bold text-gray-900">${opts.partLabel}</div>
                        <p id="${opts.adviceId}" class="mt-1 text-xs sm:text-sm text-black"></p>
                    </div>
                    <div id="${opts.textsId}" class="space-y-4 py-4"></div>
                </div>

                <div id="${opts.rightId}" class="pl-2 p-2 space-y-3 text-sm sm:text-base overflow-y-auto"></div>
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

window.renderPartTwoTexts = function (textsWrapEl, texts, baseOrder = 1) {
    if (!textsWrapEl) return;
    textsWrapEl.innerHTML = '';

    const size = window.getReadingTextSize();

    (Array.isArray(texts) ? texts : []).forEach((t, idx) => {
        const p = document.createElement('p');
        p.className = `text-${size}`;

        const txt = window.normalizeText(t?.text ?? '')
            .replace(/\n+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        const order = baseOrder + idx;

        // ✅ faqat "Question 7." (Text yo'q)
        const label = document.createElement('span');
        label.className = 'font-bold block  text-gray-900 mr-2';
        label.textContent = `Question ${order}.`;

        const body = document.createElement('span');
        body.className = 'text-gray-900';
        body.textContent = txt ? ` ${txt}` : '';

        p.appendChild(label);
        p.appendChild(body);

        textsWrapEl.appendChild(p);
    });
};


window.buildPartTwoSelect = function ({ textId, questionOrder, answers }) {
    const options = (Array.isArray(answers) ? answers : []).map(ans => ({
        value: ans?.id ?? '',
        label: (ans?.answer ?? '').toString()
    }));

    const id = `part-2-${textId}-select`; // 🔑 old id saqlanadi

    return window.buildCustomSelect_Result({
        id,
        answerid: textId,
        order: questionOrder,
        options,
        placeholder: 'Select'
    });
};
window.buildPartTwoRightCard = function (opts) {
    const card = document.createElement('div');
    card.className = 'shadow-md bg-white rounded-2xl p-3 sm:p-4';

    const wrap = document.createElement('div');
    wrap.className = 'flex flex-col gap-2';

    const title = document.createElement('div');
    title.className = 'font-bold text-gray-900';

    // ✅ faqat Question N.
    title.textContent = `Question ${opts.questionOrder}.`;

    const cs = window.buildPartTwoSelect({
        textId: opts.textId,
        questionOrder: opts.questionOrder,
        answers: opts.answers
    });

    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-2';
    fbWrap.dataset.feedbackWrap = '1';

    wrap.appendChild(title);
    wrap.appendChild(cs);
    wrap.appendChild(fbWrap);

    card.appendChild(wrap);
    return card;
};

window.renderPartTwoRightSide = function (rightWrapEl, data, baseOrder = null) {
    if (!rightWrapEl) return;
    rightWrapEl.innerHTML = '';

    const answers = Array.isArray(data?.answers) ? data.answers : [];
    const texts = Array.isArray(data?.texts) ? data.texts : [];

    const startOrder = (typeof baseOrder === 'number' && Number.isFinite(baseOrder))
        ? baseOrder
        : window.getNextQuestionOrderStart();

    texts.forEach((t, idx) => {
        const textId = t?.id ?? (idx + 1);

        rightWrapEl.appendChild(window.buildPartTwoRightCard({
            indexLabel: idx + 1,     // kerak bo‘lsa ham endi title’da ishlatilmaydi
            textId,
            questionOrder: startOrder + idx,
            answers
        }));
    });
};


// Part 3 helpers
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
    body.textContent = window.normalizeText(text).replace(/\n+/g, ' ');

    p.appendChild(label);
    p.appendChild(body);
    return p;
};

window.buildPart3OptionsFromAnswers = function (answers) {
    const arr = Array.isArray(answers) ? answers : [];
    return arr.map(a => ({ value: a?.id ?? '', label: a?.answer ?? '' }));
};

window.buildPart3SelectCard = function (paragraphIndex, partNum, textId, options, questionOrder, useOrderTitle = false) {
    const size = window.getReadingTextSize();

    const card = document.createElement('div');
    card.className = 'shadow-md bg-white rounded-2xl p-3 sm:p-4';

    const row = document.createElement('div');
    row.className = 'flex flex-col gap-2';

    const titleP = document.createElement('p');
    titleP.className = `text-${size} font-bold text-gray-900`;

    // ✅ result uchun: "15. Paragraph I"
    // (agar useOrderTitle false bo‘lsa eski "Paragraph 1" qoladi)
    if (useOrderTitle) {
        titleP.textContent = `${questionOrder}. Paragraph ${window.toRoman(paragraphIndex)}`;
    } else {
        titleP.textContent = `Paragraph ${paragraphIndex}`;
    }

    const id = `part-${partNum}-${textId}-select`;

    const cs = window.buildCustomSelect_Result({
        id,
        answerid: textId,
        order: questionOrder,
        options,
        placeholder: 'Select'
    });

    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-1';
    fbWrap.dataset.feedbackWrap = '1';

    row.appendChild(titleP);
    row.appendChild(cs);
    row.appendChild(fbWrap);

    card.appendChild(row);
    return card;
};

// Part 4/5 text


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

// Radio card

window.buildRadioQuestionCard = function (partNum, test, questionType, order) {
    const size = window.getReadingTextSize();
    const card = document.createElement('div');
    const answerId = test?.id;

    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-4';
    card.dataset.answerid = String(answerId);

    // ✅ result logikangizga mos: TFNG => radio, MCHQ => radio_mchq
    const isMchq = (questionType === 'radio_mchq');
    card.dataset.questiontype = isMchq ? 'radio_mchq' : 'radio';

    // ✅ order har doim datasetda bor bo‘lsin (focus/result map uchun)
    card.dataset.questionorder = String(order);
    card.id = `part-${partNum}-${answerId}-radio`;

    // ✅ Question: doim UI order bilan
    const qTitle = document.createElement('p');
    qTitle.className = `text-${size} text-gray-900`;
    qTitle.textContent = window.formatQuestionWithOrder(order, (test?.question ?? '').toString());

    const answersWrap = document.createElement('div');

    const keys = isMchq
        ? ['answer_1','answer_2','answer_3','answer_4']
        : ['answer_1','answer_2','answer_3'];

    answersWrap.className = isMchq
        ? 'mt-4 space-y-3 text-gray-900'
        : 'mt-4 flex flex-wrap items-center gap-x-10 gap-y-3 text-gray-900';

    keys.forEach((k, i) => {
        const label = document.createElement('label');
        label.className = isMchq ? 'flex items-start gap-3' : 'flex items-center gap-2';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `part-${partNum}-${answerId}-radio`;
        input.value = k;

        // ✅ result page: disabled
        input.disabled = true;
        input.setAttribute('disabled', 'disabled');
        input.className = isMchq ? 'mt-1 accent-gray-900' : 'accent-gray-900';

        const span = document.createElement('span');
        const letter = String.fromCharCode(65 + i); // A,B,C,D

        // ✅ option ichida "A) ..." kelgan bo'lsa ham dubl bo'lmasin
        const optTextRaw = (test?.[k] ?? '').toString();
        const optTextClean = window.stripLeadingLetterFromOption(optTextRaw);

        if (isMchq) {
            span.innerHTML = `<span class="font-bold">${letter})</span> ${optTextClean}`;
        } else {
            span.textContent = `${letter}) ${optTextClean}`;
        }

        label.appendChild(input);
        label.appendChild(span);
        answersWrap.appendChild(label);
    });

    // feedback wrap (sizning applyRadioFeedbackPart4 shu elementni qidiradi)
    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-2';
    fbWrap.dataset.feedbackWrap = '1';

    card.appendChild(qTitle);
    card.appendChild(answersWrap);
    card.appendChild(fbWrap);
    return card;
};


window.buildMchqRadioCardP = function (partNum, test, order) {
    const size = window.getReadingTextSize();
    const card = document.createElement('div');
    const answerId = test?.id;

    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-4';
    card.dataset.answerid = String(answerId);
    card.dataset.questiontype = 'radio_mchq';
    card.dataset.questionorder = String(order);
    card.id = `part-${partNum}-${answerId}-radio`;

    const qTitle = document.createElement('p');
    qTitle.className = `text-${size} text-gray-900`;

    // ✅ backend "23." bo'lsa ham dubl bo'lmasin
    qTitle.textContent = window.formatQuestionWithOrder(order, (test?.question ?? '').toString());

    const answersWrap = document.createElement('div');
    answersWrap.className = 'mt-4 space-y-3 text-gray-900';

    ['answer_1','answer_2','answer_3','answer_4'].forEach((k, i) => {
        const label = document.createElement('label');
        label.className = 'flex items-start gap-3';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `part-${partNum}-${answerId}-radio`;
        input.value = k;
        input.disabled = true;
        input.setAttribute('disabled', 'disabled');
        input.className = 'mt-1 accent-gray-900';

        const span = document.createElement('span');
        const letter = String.fromCharCode(65 + i);

        const optTextRaw = (test?.[k] ?? '').toString();
        const optTextClean = window.stripLeadingLetterFromOption(optTextRaw);

        span.innerHTML = `<span class="font-bold">${letter})</span> ${optTextClean}`;

        label.appendChild(input);
        label.appendChild(span);
        answersWrap.appendChild(label);
    });

    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-2';
    fbWrap.dataset.feedbackWrap = '1';

    card.appendChild(qTitle);
    card.appendChild(answersWrap);
    card.appendChild(fbWrap);
    return card;
};



// Part 5 fill gaps
window.renderTextWithInputsDOM_NoSingleBr = function (wrapEl, partNum, rawText, answers) {
    if (!wrapEl) return;

    const size = window.getReadingTextSize();
    const text = window.normalizeText(rawText);
    const arr = Array.isArray(answers) ? answers : [];

    wrapEl.innerHTML = '';

    const paragraphs = text.split(/\n{2,}/g).map(s => s.trim()).filter(Boolean);
    const tokenRe = /\[(\d+)\]/g;

    paragraphs.forEach(par => {
        const p = document.createElement('p');
        p.className = 'text-' + size;

        let lastIndex = 0, m;

        while ((m = tokenRe.exec(par)) !== null) {
            const start = m.index;
            const end = tokenRe.lastIndex;

            const before = par.slice(lastIndex, start);
            if (before) p.appendChild(document.createTextNode(before.replace(/\n+/g, ' ')));

            const n = parseInt(m[1], 10);
            const answerObj = arr.find(a => String(a?.count_number) === String(n)) || arr[n - 1] || { id: n };

            p.appendChild(window.buildOrderSpan(n));

            const input = window.buildInputEl(partNum, answerObj, n - 1, n);
            p.appendChild(input);

            const holder = document.createElement('span');
            holder.dataset.inlineFeedbackFor = input.id;
            holder.className = 'inline-flex items-center';
            p.appendChild(holder);

            lastIndex = end;
        }

        const after = par.slice(lastIndex);
        if (after) p.appendChild(document.createTextNode(after.replace(/\n+/g, ' ')));

        wrapEl.appendChild(p);
    });
};

window.buildFillGapsCard = function (partNum, questionText, answers) {
    const card = document.createElement('div');
    card.className = 'bg-white shadow-md rounded-[28px] p-4 sm:p-6 text-gray-900 leading-relaxed';
    const inner = document.createElement('div');
    card.appendChild(inner);
    window.renderTextWithInputsDOM_NoSingleBr(inner, partNum, questionText, answers);
    return card;
};

// =========================
// RESULT APPLY
// =========================
window.findAnswerTextById = function (answersArr, id) {
    const arr = Array.isArray(answersArr) ? answersArr : [];
    const found = arr.find(a => String(a?.id) === String(id));
    return found?.answer != null ? String(found.answer) : '';
};

window.applyResultModeAll = function (partsData) {
    window.prefillUserInputsFromBackend(partsData);
    window.prefillSelectUserAnswers(partsData);
    window.prefillRadioUserAnswers(partsData);

    window.applyFeedbackAll(partsData);

    // build result map from BACKEND (reliable)
    window.__RESULT_STATE_BY_ORDER__ = window.buildResultStateByOrder(partsData);

    window.updateFocusButtonsByResult?.();
    window.updateFocusStatsUI?.();
};

window.prefillUserInputsFromBackend = function (partsData) {
    const p1Answers = Array.isArray(partsData?.partOne?.answers) ? partsData.partOne.answers : [];
    p1Answers.forEach(a => {
        const id = a?.id;
        if (!id) return;
        const input = document.getElementById(`part-1-${id}-input`);
        if (input) input.value = (a?.user_answer ?? '').toString();
    });

    const p5Answers = Array.isArray(partsData?.partFive?.answers) ? partsData.partFive.answers : [];
    p5Answers.forEach(a => {
        const id = a?.id;
        if (!id) return;
        const input = document.getElementById(`part-5-${id}-input`);
        if (input) input.value = (a?.user_answer ?? '').toString();
    });
};

window.prefillSelectUserAnswers = function (partsData) {
    const apply = (partNum, partData) => {
        if (!partData) return;

        const texts = Array.isArray(partData?.texts) ? partData.texts : [];
        const rows  = Array.isArray(partData?.result_rows) ? partData.result_rows : [];

        const rowByTextId = {};
        rows.forEach(r => {
            const tid = r?.text_id;
            if (tid != null) rowByTextId[String(tid)] = r;
        });

        texts.forEach(t => {
            const textId = t?.id;
            if (!textId) return;

            const select = document.getElementById(`part-${partNum}-${textId}-select`);
            if (!select) return;

            select.value = '';

            const row = rowByTextId[String(textId)] || null;
            const status = row?.status ?? 'not_answered';
            if (status !== 'answered') {
                // ✅ label ham to‘g‘ri turishi uchun
                window.syncCustomSelectLabelFromNative(select, 'Select');
                return;
            }

            const userAnswerId = row?.answer_id ?? null;
            if (userAnswerId) {
                select.value = String(userAnswerId);

                // 🔥 MUHIM: custom label yangilansin
                select.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
                window.syncCustomSelectLabelFromNative(select, 'Select');
            }
        });
    };

    apply(2, partsData?.partTwo);
    apply(3, partsData?.partThree);
};



window.prefillRadioUserAnswers = function (partsData) {
    const p4 = partsData?.partFour;
    const p4All = [
        ...(Array.isArray(p4?.TFNg_tests) ? p4.TFNg_tests : []),
        ...(Array.isArray(p4?.MCHQ_tests) ? p4.MCHQ_tests : []),
    ];

    p4All.forEach(test => {
        const id = test?.id;
        if (!id) return;

        const card = document.getElementById(`part-4-${id}-radio`);
        if (!card) return;

        const status = test?.status ?? 'not_answered';
        if (status !== 'answered') return;

        const key = (test?.user_answer_key ?? '').toString().trim();
        if (!key) return;

        const input = card.querySelector(`input[type="radio"][value="${CSS.escape(key)}"]`);
        if (input) input.checked = true;
    });

    const p5Tests = Array.isArray(partsData?.partFive?.tests) ? partsData.partFive.tests : [];
    p5Tests.forEach(test => {
        const id = test?.id;
        if (!id) return;

        const card = document.getElementById(`part-5-${id}-radio`);
        if (!card) return;

        const status = test?.status ?? 'not_answered';
        if (status !== 'answered') return;

        const key = (test?.user_answer_key ?? '').toString().trim();
        if (!key) return;

        const input = card.querySelector(`input[type="radio"][value="${CSS.escape(key)}"]`);
        if (input) input.checked = true;
    });
};

window.applyFeedbackAll = function (partsData) {
    window.applyInputFeedback(1, partsData?.partOne?.answers || []);
    window.applySelectFeedbackFromTexts(2, partsData?.partTwo);
    window.applySelectFeedbackFromTexts(3, partsData?.partThree);
    window.applyRadioFeedbackPart4(partsData?.partFour);
    window.applyInputFeedback(5, partsData?.partFive?.answers || []);
    window.applyRadioFeedbackPart5(partsData?.partFive);
};

window.applyInputFeedback = function (partNum, answersArr) {
    const arr = Array.isArray(answersArr) ? answersArr : [];

    arr.forEach(a => {
        const id = a?.id;
        if (!id) return;

        const input = document.getElementById(`part-${partNum}-${id}-input`);
        if (!input) return;

        const status = a?.status ?? 'not_answered';
        const is_correct = (a?.is_correct ?? null);
        const correctText = (a?.correct_answer != null) ? String(a.correct_answer) : '';

        window.applyResultClassToEl(input, status, is_correct);

        const holder = document.querySelector(`[data-inline-feedback-for="${CSS.escape(input.id)}"]`);
        if (holder) {
            window.removeOldFeedback(holder);
            holder.appendChild(window.buildFeedbackEl({ status, is_correct, correctText, inline: true }));
        } else {
            const next = input.nextElementSibling;
            if (next && next.dataset && next.dataset.feedback === '1') next.remove();
            input.insertAdjacentElement('afterend', window.buildFeedbackEl({ status, is_correct, correctText, inline: false }));
        }
    });
};

window.applySelectFeedbackFromTexts = function (partNum, partData) {
    if (!partData) return;

    const texts = Array.isArray(partData?.texts) ? partData.texts : [];
    const answers = Array.isArray(partData?.answers) ? partData.answers : [];
    const rows = Array.isArray(partData?.result_rows) ? partData.result_rows : [];

    const rowByTextId = {};
    rows.forEach(r => {
        const tid = r?.text_id;
        if (tid != null) rowByTextId[String(tid)] = r;
    });

    texts.forEach(t => {
        const textId = t?.id;
        if (!textId) return;

        const select = document.getElementById(`part-${partNum}-${textId}-select`);
        if (!select) return;

        const row = rowByTextId[String(textId)] || null;
        const status = row?.status ?? 'not_answered';
        const is_correct = row?.is_correct ?? null;

        const correctId = t?.correct_answer_id ?? row?.correct_answer_id ?? null;
        const correctText = correctId
            ? window.findAnswerTextById(answers, correctId)
            : (row?.correct_answer ?? '');

        // ✅ native selectga ham, custom btn ga ham border/ring qo‘yadi
        window.applyResultClassToEl(select, status, is_correct);

        // ✅ feedback wrap: custom select root parent (wrap) ichidan izlaymiz
        const csRoot = window.getCustomSelectRootFromNative(select);
        const wrap = csRoot?.parentElement || select.parentElement;

        const fbWrap =
            wrap?.querySelector?.('[data-feedbackWrap="1"]') ||
            select.parentElement?.querySelector?.('[data-feedbackWrap="1"]') ||
            wrap ||
            select.parentElement;

        window.removeOldFeedback(fbWrap);
        fbWrap.appendChild(window.buildFeedbackEl({ status, is_correct, correctText, inline: false }));
    });
};



window.applyRadioFeedbackPart4 = function (partData) {
    if (!partData) return;

    const tfng = Array.isArray(partData?.TFNg_tests) ? partData.TFNg_tests : [];
    const mchq = Array.isArray(partData?.MCHQ_tests) ? partData.MCHQ_tests : [];
    const all = [...tfng, ...mchq];

    all.forEach(test => {
        const id = test?.id;
        if (!id) return;

        const card = document.getElementById(`part-4-${id}-radio`);
        if (!card) return;

        const status = test?.status ?? 'not_answered';
        const is_correct = test?.is_correct ?? null;
        const correctText = test?.correct_answer_key != null ? String(test[test.correct_answer_key]) : '';

        window.applyResultClassToEl(card, status, is_correct);

        const fbWrap = card.querySelector('[data-feedbackWrap="1"]') || card;
        window.removeOldFeedback(fbWrap);
        fbWrap.appendChild(window.buildFeedbackEl({ status, is_correct, correctText, inline: false }));
    });
};

window.applyRadioFeedbackPart5 = function (partData) {
    if (!partData) return;

    const tests = Array.isArray(partData?.tests) ? partData.tests : [];

    tests.forEach(test => {
        const id = test?.id;
        if (!id) return;

        const card = document.getElementById(`part-5-${id}-radio`);
        if (!card) return;

        const status = test?.status ?? 'not_answered';
        const is_correct = test?.is_correct ?? null;

        const correctText =
            test?.correct_answer_text != null ? String(test.correct_answer_text)
                : (test?.correct_answer_key != null ? String(test[test.correct_answer_key]) : '');

        window.applyResultClassToEl(card, status, is_correct);

        const fbWrap = card.querySelector('[data-feedbackWrap="1"]') || card;
        window.removeOldFeedback(fbWrap);
        fbWrap.appendChild(window.buildFeedbackEl({ status, is_correct, correctText, inline: false }));
    });
};

// =========================
// RESULT STATE BY ORDER (BACKEND-based, RETURN MAP)
// =========================
window.__RESULT_STATE_BY_ORDER__ = window.__RESULT_STATE_BY_ORDER__ || {};

window.buildResultStateByOrder = function (partsData) {
    const map = {};

    // init all orders from DOM
    document.querySelectorAll('[data-questionorder]').forEach(el => {
        const o = parseInt(el.dataset.questionorder, 10);
        if (!Number.isFinite(o)) return;
        if (!map[o]) map[o] = { status: 'not_answered', is_correct: null };
    });

    const setByDomId = (domId, status, is_correct) => {
        const el = document.getElementById(domId);
        if (!el) return;
        const o = parseInt(el.dataset.questionorder, 10);
        if (!Number.isFinite(o)) return;
        map[o] = {
            status: status === 'answered' ? 'answered' : 'not_answered',
            is_correct: (is_correct === 0 || is_correct === 1) ? Number(is_correct) : null
        };
    };

    // part 1 inputs
    (Array.isArray(partsData?.partOne?.answers) ? partsData.partOne.answers : []).forEach(a => {
        if (!a?.id) return;
        setByDomId(`part-1-${a.id}-input`, a?.status, a?.is_correct);
    });

    // ✅ part 2 selects (text_id-based rows)
    {
        const texts = Array.isArray(partsData?.partTwo?.texts) ? partsData.partTwo.texts : [];
        const rows  = Array.isArray(partsData?.partTwo?.result_rows) ? partsData.partTwo.result_rows : [];
        const rowByTextId = {};
        rows.forEach(r => {
            const tid = r?.text_id;
            if (tid != null) rowByTextId[String(tid)] = r;
        });

        texts.forEach(t => {
            if (!t?.id) return;
            const row = rowByTextId[String(t.id)] || {};
            setByDomId(`part-2-${t.id}-select`, row?.status, row?.is_correct);
        });
    }

    // ✅ part 3 selects (text_id-based rows)
    {
        const texts = Array.isArray(partsData?.partThree?.texts) ? partsData.partThree.texts : [];
        const rows  = Array.isArray(partsData?.partThree?.result_rows) ? partsData.partThree.result_rows : [];
        const rowByTextId = {};
        rows.forEach(r => {
            const tid = r?.text_id;
            if (tid != null) rowByTextId[String(tid)] = r;
        });

        texts.forEach(t => {
            if (!t?.id) return;
            const row = rowByTextId[String(t.id)] || {};
            setByDomId(`part-3-${t.id}-select`, row?.status, row?.is_correct);
        });
    }

    // part 4 radios
    const p4 = partsData?.partFour || {};
    const p4All = [
        ...(Array.isArray(p4?.TFNg_tests) ? p4.TFNg_tests : []),
        ...(Array.isArray(p4?.MCHQ_tests) ? p4.MCHQ_tests : []),
    ];
    p4All.forEach(test => {
        if (!test?.id) return;
        setByDomId(`part-4-${test.id}-radio`, test?.status, test?.is_correct);
    });

    // part 5 inputs
    (Array.isArray(partsData?.partFive?.answers) ? partsData.partFive.answers : []).forEach(a => {
        if (!a?.id) return;
        setByDomId(`part-5-${a.id}-input`, a?.status, a?.is_correct);
    });

    // part 5 radios
    (Array.isArray(partsData?.partFive?.tests) ? partsData.partFive.tests : []).forEach(test => {
        if (!test?.id) return;
        setByDomId(`part-5-${test.id}-radio`, test?.status, test?.is_correct);
    });

    return map;
};

