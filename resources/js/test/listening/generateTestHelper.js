// resources/js/test/listening/generateTestHelper.js

// resources/js/test/listening/generateTestHelper.js

window.escapeHtml = window.escapeHtml || function (str) {
    return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
};

window.resetListeningQuestionOrderCounter = window.resetListeningQuestionOrderCounter || function (start = 1) {
    window.__LISTENING_QUESTION_ORDER_COUNTER__ = Number(start) || 1;
};

window.getNextListeningQuestionOrder = window.getNextListeningQuestionOrder || function () {
    const current = Number(window.__LISTENING_QUESTION_ORDER_COUNTER__ || 1);
    window.__LISTENING_QUESTION_ORDER_COUNTER__ = current + 1;
    return String(current);
};

window.extractListeningTextQuestionNumbers = window.extractListeningTextQuestionNumbers || function (rawText) {
    const text = String(rawText ?? '').replace(/\r/g, '');
    const matches = text.match(/\[(\d+)\]/g) || [];
    const seen = new Set();
    const nums = [];

    matches.forEach((token) => {
        const m = token.match(/\[(\d+)\]/);
        const original = m?.[1];
        if (!original || seen.has(original)) return;
        seen.add(original);
        nums.push(String(original));
    });

    return nums;
};

window.buildListeningSequentialOrderMap = window.buildListeningSequentialOrderMap || function (rawText) {
    const map = {};
    const nums = window.extractListeningTextQuestionNumbers(rawText);

    nums.forEach((original) => {
        map[String(original)] = window.getNextListeningQuestionOrder();
    });

    return map;
};

// ... qolgan kodlar o‘z holicha


window.stripLeadingOrderPrefix = window.stripLeadingOrderPrefix || function (text) {
    const s = String(text ?? '').trim();
    return s.replace(/^\s*\d+\s*[\.\)\:\-]\s*/, '').trim();
};

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

// ===============================
// PART FIVE ORDER RESOLVER (NEW)
// ===============================
window.resolveSequentialOrdersFromQuestions = window.resolveSequentialOrdersFromQuestions || function (tests) {
    const arr = Array.isArray(tests) ? tests : [];
    if (arr.length === 0) return [];

    // 1) known orders from "25. ..." style
    const known = arr
        .map((t, idx) => {
            const n = Number(window.getLeadingNumber(t?.question));
            return Number.isFinite(n) && n > 0 ? { idx, n } : null;
        })
        .filter(Boolean);

    // If nothing known -> simple 1..N
    if (known.length === 0) {
        return arr.map((_, i) => String(i + 1));
    }

    // 2) anchor: smallest known number (e.g., 25) and its index
    let anchor = known[0];
    known.forEach(k => {
        if (k.n < anchor.n) anchor = k;
    });

    const anchorN = anchor.n;
    const anchorIdx = anchor.idx;

    // 3) build sequential orders around anchor
    return arr.map((_, idx) => String(anchorN - (anchorIdx - idx)));
};

window.makeOrderPrefixSpan = window.makeOrderPrefixSpan || function (orderNumber) {
    const span = document.createElement('span');
    span.className = 'font-semibold text-gray-900';
    span.textContent = `${String(orderNumber)})`;
    return span;
};

window.hasExpandedSelectionInside = window.hasExpandedSelectionInside || function (container) {
    if (!container) return false;

    const sel = window.getSelection?.();
    if (!sel || sel.rangeCount === 0 || sel.isCollapsed) return false;

    const selectedText = (sel.toString() ?? '').trim();
    if (!selectedText) return false;

    const range = sel.getRangeAt(0);
    const common = range.commonAncestorContainer;
    const commonNode = common?.nodeType === 1 ? common : common?.parentNode;

    const anchorNode = sel.anchorNode?.nodeType === 1 ? sel.anchorNode : sel.anchorNode?.parentNode;
    const focusNode = sel.focusNode?.nodeType === 1 ? sel.focusNode : sel.focusNode?.parentNode;

    return !!(
        (commonNode && container.contains(commonNode)) ||
        (anchorNode && container.contains(anchorNode)) ||
        (focusNode && container.contains(focusNode))
    );
};

window.bindSelectionSafeRadioOption = window.bindSelectionSafeRadioOption || function (labelEl, inputEl) {
    if (!labelEl || !inputEl) return;

    let blockNextClick = false;

    labelEl.addEventListener('mouseup', () => {
        blockNextClick = window.hasExpandedSelectionInside(labelEl);
    });

    labelEl.addEventListener('click', (e) => {
        if (!blockNextClick) return;

        e.preventDefault();
        e.stopPropagation();
        blockNextClick = false;
    });

    inputEl.addEventListener('click', (e) => {
        if (!blockNextClick) return;

        e.preventDefault();
        e.stopPropagation();
        blockNextClick = false;
    });
};


// ===============================
// PART ONE
// ===============================
window.createPartOneCard = function ({ order, testId, a1, a2, a3 }) {
    const wrap = document.createElement('div');
    wrap.className = 'bg-white shadow-md rounded-[28px] p-3';

    wrap.dataset.answerid = String(testId);
    wrap.dataset.questiontype = 'radio';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-1-${testId}-radio`;

    const name = `part-1-${testId}-radio`;

    wrap.innerHTML = `
        <div class="text-gray-900 font-semibold">${order}.</div>

        <div class="mt-2 text-gray-900 leading-relaxed space-y-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_1" class="mt-1 accent-blue-900">
                <p>A) ${window.escapeHtml(a1)}</p>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_2" class="mt-1 accent-blue-900">
                <p>B) ${window.escapeHtml(a2)}</p>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_3" class="mt-1 accent-blue-900">
                <p>C) ${window.escapeHtml(a3)}</p>
            </label>
        </div>
    `;

    wrap.querySelectorAll('input[type="radio"]').forEach((r) => {
        const label = r.closest('label');
        window.bindSelectionSafeRadioOption(label, r);
        r.addEventListener('change', () => window.changeRadioInputGenerate(r, 1));
    });

    return wrap;
};

// ===============================
// PART TWO
// ===============================
window.createPartTwoInput = function (orderNumber) {
    const input = document.createElement('input');
    input.type = 'text';

    input.className = [
        'inline-block',
        'h-9', 'sm:h-10',
        'w-[220px]', 'sm:w-[260px]',
        'border-2', 'border-gray-400', 'rounded-lg',
        'bg-white', 'px-2',
        'text-base', 'sm:text-lg',
        'outline-none',
        'focus:border-gray-900',
        'transition'
    ].join(' ');

    input.dataset.count = String(orderNumber);

    input.addEventListener('input', () => window.changeInputGenerate(input, 2));

    return input;
};

window.renderPartTwoTextInto = function (wrapEl, rawText, orderMap = {}) {
    if (!wrapEl) return;

    wrapEl.innerHTML = '';
    const text = String(rawText ?? '').replace(/\r/g, '').trim();
    if (!text) return;

    const lines = text.split('\n').map(l => l.trim()).filter(Boolean);

    lines.forEach((line) => {
        const row = document.createElement('div');
        row.className = 'flex flex-wrap items-center gap-2';

        const parts = line.split(/(\[\d+\])/g).filter(Boolean);

        parts.forEach((chunk) => {
            const m = chunk.match(/^\[(\d+)\]$/);
            if (m) {
                const originalOrder = m[1];
                const displayOrder = String(orderMap[originalOrder] ?? originalOrder);

                // 1) prefix: "1)"
                row.appendChild(window.makeOrderPrefixSpan(displayOrder));

                // 2) input
                row.appendChild(window.createPartTwoInput(originalOrder));
            } else {
                const span = document.createElement('span');
                span.textContent = chunk;
                row.appendChild(span);
            }
        });

        wrapEl.appendChild(row);
    });
};

// ✅ NEW: title has leading "15." / "15)" / "15:" / "15-" etc
window.hasLeadingOrderPrefix = window.hasLeadingOrderPrefix || function (text) {
    const s = String(text ?? '').trim();
    return /^\s*\d+\s*[\.\)\:\-]\s*/.test(s);
};

// ===============================
// PART THREE
// ===============================
window.buildPartThreeOptionLabel = function ({ letterArray, letterKey, answerText }) {
    const txt = String(answerText ?? '').trim();
    return ` ${txt}`.trim();
};

window.createPartThreeCard = function ({ questionId, title, order, answers, letterArray }) {
    const wrap = document.createElement('div');

    wrap.className = 'bg-white shadow-md rounded-[28px] p-5 sm:p-6';

    wrap.dataset.answerid = String(questionId);
    wrap.dataset.questiontype = 'select';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-3-${questionId}-select`;

    const titleDiv = document.createElement('div');
    titleDiv.className = 'text-gray-900 font-bold text-lg';

    const safeTitle = String(title ?? '').trim();

    // ✅ If title already starts with number (e.g., "15. Speaker 1") => do NOT add order.
    // ✅ Otherwise => add global order (15..18)
    const showTitle = window.hasLeadingOrderPrefix(safeTitle)
        ? safeTitle
        : `${String(order)}. ${safeTitle || ''}`.trim();

    titleDiv.innerHTML = `<p>${window.escapeHtml(showTitle)}</p>`;
    wrap.appendChild(titleDiv);

    const mt = document.createElement('div');
    mt.className = 'mt-4';
    wrap.appendChild(mt);

    const arr = Array.isArray(answers) ? answers : [];
    const options = arr.map(a => ({
        value: String(a.letter_key ?? ''),
        label: window.buildPartThreeOptionLabel({
            letterArray,
            letterKey: a.letter_key,
            answerText: a.answer
        })
    }));

    const cs = window.buildCustomSelect({
        partNum: 3,
        id: `part-3-${questionId}-select-input`,
        answerid: questionId,
        order: order,
        options,
        placeholder: 'Select',
        answerDivW: 1
    });

    mt.appendChild(cs);
    return wrap;
};

// ===============================
// PART FOUR
// ===============================
window.createPartFourOptionText = function ({ letterArray, letterKey }) {
    const letters = Array.isArray(letterArray) ? letterArray : [];
    const idx = Number(letterKey);
    return String(letters[idx] ?? '').trim();
};

window.createPartFourQuestionCard = function ({ questionId, title, order, answers, letterArray }) {
    const wrap = document.createElement('div');

    wrap.className = 'bg-white shadow-md rounded-[22px] p-4 sm:p-5';

    wrap.dataset.answerid = String(questionId);
    wrap.dataset.questiontype = 'select';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-4-${questionId}-select`;

    const row = document.createElement('div');
    row.className = 'flex flex-col sm:flex-row items-center sm:items-center gap-3';
    wrap.appendChild(row);

    const left = document.createElement('div');
    left.className = 'w-full sm:w-1/2 text-gray-900 font-bold text-lg';

    const safeTitle = String(title ?? '').trim();

    // ✅ same rule
    const showTitle = window.hasLeadingOrderPrefix(safeTitle)
        ? safeTitle
        : `${String(order)}. ${safeTitle || ''}`.trim();

    const p = document.createElement('p');
    p.textContent = showTitle;
    left.appendChild(p);

    row.appendChild(left);

    const right = document.createElement('div');
    right.className = 'w-full sm:w-1/2';
    row.appendChild(right);

    const arr = Array.isArray(answers) ? answers : [];
    const options = arr.map(a => ({
        value: String(a.letter_key ?? ''),
        label: window.createPartFourOptionText({
            letterArray,
            letterKey: a.letter_key
        })
    }));

    const cs = window.buildCustomSelect({
        partNum: 4,
        id: `part-4-${questionId}-select-input`,
        answerid: questionId,
        order: order,
        options,
        placeholder: 'Select',
        answerDivW: 1
    });

    right.appendChild(cs);
    return wrap;
};

// ===============================
// PART FIVE
// ===============================

window.getLeadingNumber = window.getLeadingNumber || function (text) {
    const m = String(text ?? '').match(/^(\d+)/);
    return m ? m[1] : '';
};


// ===============================
// PART FIVE (UPDATED)
// ===============================

window.createPartFiveCard = function ({ order, testId, question, a1, a2, a3 }) {
    const wrap = document.createElement('div');

    wrap.className = 'bg-white shadow-md rounded-[28px] p-4';

    wrap.dataset.answerid = String(testId);
    wrap.dataset.questiontype = 'radio';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-5-${testId}-radio`;

    const name = `part-5-${testId}-radio`;

    // question matnidan boshidagi "24." kabi prefikslarni olib tashlaymiz
    const cleanQuestion = window.stripLeadingOrderPrefix(question);

    wrap.innerHTML = `
        <div class="text-gray-900 flex items-start gap-2">
            <span class="font-semibold shrink-0">${window.escapeHtml(order)}.</span>
            <span class="font-medium">${window.escapeHtml(cleanQuestion)}</span>
        </div>

        <div class="mt-3 text-gray-900 leading-relaxed space-y-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_1" class="mt-1 accent-blue-900">
                <p class="text-3">A) ${window.escapeHtml(a1)}</p>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_2" class="mt-1 accent-blue-900">
                <p class="text-3">B) ${window.escapeHtml(a2)}</p>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_3" class="mt-1 accent-blue-900">
                <p class="text-3">C) ${window.escapeHtml(a3)}</p>
            </label>
        </div>
    `;

    wrap.querySelectorAll('input[type="radio"]').forEach((r) => {
        const label = r.closest('label');
        window.bindSelectionSafeRadioOption(label, r);
        r.addEventListener('change', () => window.changeRadioInputGenerate(r, 5));
    });

    return wrap;
};

// ===============================
// PART SIX
// ===============================
window.createPartSixInput = function (orderNumber) {
    const input = document.createElement('input');
    input.type = 'text';

    input.className = [
        'inline-block',
        'h-9', 'sm:h-10',
        'w-[220px]', 'sm:w-[260px]',
        'border-2', 'border-gray-400', 'rounded-lg',
        'bg-white', 'px-2',
        'text-base', 'sm:text-lg',
        'outline-none',
        'focus:border-gray-900',
        'transition'
    ].join(' ');

    input.dataset.count = String(orderNumber);

    input.addEventListener('input', () => window.changeInputGenerate(input, 6));

    return input;
};

window.renderPartSixTextInto = function (wrapEl, rawText, orderMap = {}) {
    if (!wrapEl) return;

    wrapEl.innerHTML = '';
    const text = String(rawText ?? '').replace(/\r/g, '').trim();
    if (!text) return;

    const lines = text.split('\n').map(l => l.trim()).filter(Boolean);

    lines.forEach((line) => {
        const row = document.createElement('div');

        // ✅ PART 2 ga o‘xshab: bitta chiziq, center
        row.className = 'flex flex-wrap items-center gap-2';

        const parts = line.split(/(\[\d+\])/g).filter(Boolean);

        parts.forEach((chunk) => {
            const m = chunk.match(/^\[(\d+)\]$/);
            if (m) {
                const originalOrder = m[1];
                const displayOrder = String(orderMap[originalOrder] ?? originalOrder);

                // 1) prefix: "1)"
                row.appendChild(window.makeOrderPrefixSpan(displayOrder));

                // 2) input
                row.appendChild(window.createPartSixInput(originalOrder));
            } else {
                const span = document.createElement('span');
                span.textContent = chunk;
                row.appendChild(span);
            }
        });

        wrapEl.appendChild(row);
    });
};



// ===============================
// LISTENING PART 4 MAP EDITOR (DRAW ON MAP)
window.ListeningMapEditor = (function () {
    let state = {
        inited: false,
        color: '#000000',
        lineWidth: 3,
        isDown: false,

        box: null,
        img: null,
        canvasLayer: null,
        canvas: null,
        ctx: null,

        pencilHint: null,

        resetBtn: null,
        fullscreenBtn: null,

        // dropdown
        penBox: null,
        penDropdown: null,
        penPreview: null,
        penSwatches: [],
        isDropdownOpen: false,

        // modal
        modal: null,
        modalImg: null,
        modalClose: null,
        modalCanvas: null,
        modalCtx: null,
        modalLayer: null,

        // handlers
        onResize: null,
        ro: null,

        onPointerDown: null,
        onPointerMove: null,
        onPointerUpWin: null,

        onCanvasEnter: null,
        onCanvasLeave: null,
        onBoxEnter: null,
        onBoxLeave: null,

        onReset: null,
        onFullscreen: null,

        onPenBoxClick: null,
        onDocClick: null,
        onEsc: null,
        onSwatchClick: null,

        // modal handlers
        onModalCloseClick: null,
        onModalBgClick: null,
        onModalEsc: null,
    };

    const $ = (id) => document.getElementById(id);

    function createCanvas() {
        const c = document.createElement('canvas');
        c.className = 'absolute inset-0 w-full h-full';
        c.style.touchAction = 'none';
        c.style.pointerEvents = 'auto';
        return c;
    }

    function setupCanvasToBox(canvas, boxEl) {
        const rect = boxEl.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        const w = Math.max(1, Math.floor(rect.width * dpr));
        const h = Math.max(1, Math.floor(rect.height * dpr));

        canvas.width = w;
        canvas.height = h;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';

        const ctx = canvas.getContext('2d');
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        return ctx;
    }

    function getPos(canvas, evt) {
        const r = canvas.getBoundingClientRect();
        return { x: evt.clientX - r.left, y: evt.clientY - r.top };
    }

    function begin(ctx, canvas, evt) {
        const p = getPos(canvas, evt);
        state.isDown = true;
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
    }

    function move(ctx, canvas, evt) {
        if (!state.isDown) return;
        const p = getPos(canvas, evt);

        ctx.strokeStyle = state.color;
        ctx.lineWidth = state.lineWidth;

        ctx.lineTo(p.x, p.y);
        ctx.stroke();
    }

    function end(ctx) {
        state.isDown = false;
        ctx.closePath();
    }

    function clear(ctx, canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function rebuildMainKeepDrawing() {
        if (!state.canvas || !state.ctx || !state.box) return;

        const rect = state.box.getBoundingClientRect();
        if (rect.width < 2 || rect.height < 2) return; // hali hidden bo‘lishi mumkin

        const snap = document.createElement('canvas');
        snap.width = state.canvas.width;
        snap.height = state.canvas.height;
        snap.getContext('2d').drawImage(state.canvas, 0, 0);

        state.ctx = setupCanvasToBox(state.canvas, state.box);

        state.ctx.drawImage(
            snap,
            0, 0, snap.width, snap.height,
            0, 0, state.canvas.width, state.canvas.height
        );
    }

    function showPencil() { state.pencilHint?.classList?.remove('hidden'); }
    function hidePencil() { state.pencilHint?.classList?.add('hidden'); }

    function openDropdown() {
        if (!state.penDropdown) return;
        state.penDropdown.classList.remove('hidden');
        state.isDropdownOpen = true;
    }

    function closeDropdown() {
        if (!state.penDropdown) return;
        state.penDropdown.classList.add('hidden');
        state.isDropdownOpen = false;
    }

    function toggleDropdown() {
        state.isDropdownOpen ? closeDropdown() : openDropdown();
    }

    function setColor(c) {
        state.color = c || '#000000';
        if (state.penPreview) state.penPreview.style.backgroundColor = state.color;
    }

    function bindMainDrawing() {
        state.onPointerDown = (e) => {
            if (!state.canvas || !state.ctx) return;
            e.preventDefault();
            state.canvas.setPointerCapture?.(e.pointerId);
            begin(state.ctx, state.canvas, e);
        };

        state.onPointerMove = (e) => {
            if (!state.canvas || !state.ctx) return;
            e.preventDefault();
            move(state.ctx, state.canvas, e);
        };

        state.onPointerUpWin = () => {
            if (!state.ctx) return;
            end(state.ctx);
        };

        state.canvas.addEventListener('pointerdown', state.onPointerDown);
        state.canvas.addEventListener('pointermove', state.onPointerMove);
        window.addEventListener('pointerup', state.onPointerUpWin);
        window.addEventListener('pointercancel', state.onPointerUpWin);

        state.onBoxEnter = showPencil;
        state.onBoxLeave = hidePencil;
        state.onCanvasEnter = showPencil;
        state.onCanvasLeave = hidePencil;

        state.box?.addEventListener('mouseenter', state.onBoxEnter);
        state.box?.addEventListener('mouseleave', state.onBoxLeave);
        state.canvas?.addEventListener('pointerenter', state.onCanvasEnter);
        state.canvas?.addEventListener('pointerleave', state.onCanvasLeave);
    }

    function unbindMainDrawing() {
        if (state.canvas && state.onPointerDown) state.canvas.removeEventListener('pointerdown', state.onPointerDown);
        if (state.canvas && state.onPointerMove) state.canvas.removeEventListener('pointermove', state.onPointerMove);
        if (state.onPointerUpWin) {
            window.removeEventListener('pointerup', state.onPointerUpWin);
            window.removeEventListener('pointercancel', state.onPointerUpWin);
        }

        if (state.box && state.onBoxEnter) state.box.removeEventListener('mouseenter', state.onBoxEnter);
        if (state.box && state.onBoxLeave) state.box.removeEventListener('mouseleave', state.onBoxLeave);
        if (state.canvas && state.onCanvasEnter) state.canvas.removeEventListener('pointerenter', state.onCanvasEnter);
        if (state.canvas && state.onCanvasLeave) state.canvas.removeEventListener('pointerleave', state.onCanvasLeave);
    }

    function bindTools() {
        state.onReset = () => {
            if (state.ctx && state.canvas) clear(state.ctx, state.canvas);
            if (state.modalCtx && state.modalCanvas) clear(state.modalCtx, state.modalCanvas);
        };
        state.resetBtn?.addEventListener('click', state.onReset);

        state.onFullscreen = () => openFullscreen();
        state.fullscreenBtn?.addEventListener('click', state.onFullscreen);

        state.onPenBoxClick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown();
        };
        state.penBox?.addEventListener('click', state.onPenBoxClick);

        state.onSwatchClick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            const c = e.currentTarget?.dataset?.color || '#000000';
            setColor(c);

            state.penSwatches.forEach(b => b.classList.remove('ring-2', 'ring-gray-900', 'ring-offset-2'));
            e.currentTarget.classList.add('ring-2', 'ring-gray-900', 'ring-offset-2');

            closeDropdown();
        };
        state.penSwatches.forEach(btn => btn.addEventListener('click', state.onSwatchClick));

        state.onDocClick = (e) => {
            if (!state.isDropdownOpen) return;
            const t = e.target;
            if (state.penBox && state.penBox.contains(t)) return;
            closeDropdown();
        };
        document.addEventListener('click', state.onDocClick);

        state.onEsc = (e) => { if (e.key === 'Escape') closeDropdown(); };
        window.addEventListener('keydown', state.onEsc);
    }

    function unbindTools() {
        if (state.resetBtn && state.onReset) state.resetBtn.removeEventListener('click', state.onReset);
        if (state.fullscreenBtn && state.onFullscreen) state.fullscreenBtn.removeEventListener('click', state.onFullscreen);

        if (state.penBox && state.onPenBoxClick) state.penBox.removeEventListener('click', state.onPenBoxClick);
        state.penSwatches.forEach(btn => {
            if (state.onSwatchClick) btn.removeEventListener('click', state.onSwatchClick);
        });

        if (state.onDocClick) document.removeEventListener('click', state.onDocClick);
        if (state.onEsc) window.removeEventListener('keydown', state.onEsc);
    }

    function openFullscreen() {
        if (!state.modal || !state.modalImg || !state.img) return;

        state.modalImg.src = state.img.src;
        state.modal.classList.remove('hidden');
        state.modal.classList.add('flex');
        document.documentElement.classList.add('overflow-hidden');

        const wrapper = state.modalImg.parentElement;
        if (!wrapper) return;
        wrapper.classList.add('relative');

        if (!state.modalLayer) {
            state.modalLayer = document.createElement('div');
            state.modalLayer.className = 'absolute inset-0';
            state.modalLayer.style.pointerEvents = 'none';
            wrapper.appendChild(state.modalLayer);

            state.modalCanvas = createCanvas();
            state.modalCanvas.style.pointerEvents = 'auto';
            state.modalLayer.appendChild(state.modalCanvas);
        }

        const rect = wrapper.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        state.modalCanvas.width = Math.max(1, Math.floor(rect.width * dpr));
        state.modalCanvas.height = Math.max(1, Math.floor(rect.height * dpr));
        state.modalCanvas.style.width = rect.width + 'px';
        state.modalCanvas.style.height = rect.height + 'px';

        state.modalCtx = state.modalCanvas.getContext('2d');
        state.modalCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
        state.modalCtx.lineCap = 'round';
        state.modalCtx.lineJoin = 'round';

        if (state.canvas) {
            state.modalCtx.drawImage(
                state.canvas,
                0, 0, state.canvas.width, state.canvas.height,
                0, 0, state.modalCanvas.width, state.modalCanvas.height
            );
        }

        const md = (e) => { e.preventDefault(); state.modalCanvas.setPointerCapture?.(e.pointerId); begin(state.modalCtx, state.modalCanvas, e); };
        const mm = (e) => { e.preventDefault(); move(state.modalCtx, state.modalCanvas, e); };
        const mu = () => end(state.modalCtx);

        state.modalCanvas.addEventListener('pointerdown', md);
        state.modalCanvas.addEventListener('pointermove', mm);
        window.addEventListener('pointerup', mu);
        window.addEventListener('pointercancel', mu);

        state.modalCanvas.__md = md;
        state.modalCanvas.__mm = mm;
        state.modalCanvas.__mu = mu;

        state.onModalCloseClick = () => closeFullscreen(true);
        state.onModalBgClick = () => closeFullscreen(true);
        state.onModalEsc = (e) => { if (e.key === 'Escape') closeFullscreen(true); };

        state.modalClose?.addEventListener('click', state.onModalCloseClick);
        state.modal.addEventListener('click', state.onModalBgClick);
        window.addEventListener('keydown', state.onModalEsc);
    }

    function closeFullscreen(syncBack) {
        if (!state.modal) return;

        if (syncBack && state.modalCanvas && state.canvas && state.ctx) {
            clear(state.ctx, state.canvas);
            state.ctx.drawImage(
                state.modalCanvas,
                0, 0, state.modalCanvas.width, state.modalCanvas.height,
                0, 0, state.canvas.width, state.canvas.height
            );
        }

        if (state.modalCanvas?.__md) state.modalCanvas.removeEventListener('pointerdown', state.modalCanvas.__md);
        if (state.modalCanvas?.__mm) state.modalCanvas.removeEventListener('pointermove', state.modalCanvas.__mm);
        if (state.modalCanvas?.__mu) {
            window.removeEventListener('pointerup', state.modalCanvas.__mu);
            window.removeEventListener('pointercancel', state.modalCanvas.__mu);
        }
        if (state.modalCanvas) {
            state.modalCanvas.__md = null;
            state.modalCanvas.__mm = null;
            state.modalCanvas.__mu = null;
        }

        if (state.modalClose && state.onModalCloseClick) state.modalClose.removeEventListener('click', state.onModalCloseClick);
        if (state.modal && state.onModalBgClick) state.modal.removeEventListener('click', state.onModalBgClick);
        if (state.onModalEsc) window.removeEventListener('keydown', state.onModalEsc);

        state.modalImg && (state.modalImg.src = '');
        state.modal.classList.add('hidden');
        state.modal.classList.remove('flex');
        document.documentElement.classList.remove('overflow-hidden');
    }

    function refresh() {
        // Part4 ochilganda (hidden => visible) canvasni normal size qilish uchun
        rebuildMainKeepDrawing();
    }

    function init(opts) {
        destroy();

        state.box = $(opts.boxId);
        state.img = $(opts.imgId);
        state.canvasLayer = $(opts.canvasLayerId);
        state.pencilHint = $(opts.pencilHintId);

        state.resetBtn = $(opts.resetBtnId);
        state.fullscreenBtn = $(opts.fullscreenBtnId);

        state.penBox = $(opts.penBoxId);
        state.penDropdown = $(opts.penDropdownId);
        state.penPreview = $(opts.penPreviewId);
        state.penSwatches = Array.from(document.querySelectorAll(opts.penSwatchSelector || '.pen-swatch'));

        state.modal = $(opts.modalId);
        state.modalImg = $(opts.modalImgId);
        state.modalClose = $(opts.modalCloseId);

        if (!state.box || !state.canvasLayer) return;

        setColor('#000000');
        const blackBtn = state.penSwatches.find(b => (b.dataset.color || '').toLowerCase() === '#000000');
        if (blackBtn) blackBtn.classList.add('ring-2', 'ring-gray-900', 'ring-offset-2');

        state.canvasLayer.innerHTML = '';
        state.canvas = createCanvas();
        state.canvasLayer.appendChild(state.canvas);

        state.ctx = setupCanvasToBox(state.canvas, state.box);

        bindMainDrawing();
        bindTools();

        // window resize
        state.onResize = () => rebuildMainKeepDrawing();
        window.addEventListener('resize', state.onResize);

        // ✅ hidden -> visible bo‘lganda ham auto resize
        if (window.ResizeObserver) {
            state.ro = new ResizeObserver(() => rebuildMainKeepDrawing());
            state.ro.observe(state.box);
        }

        state.inited = true;
    }

    function destroy() {
        if (!state.inited) {
            if (state.canvasLayer) state.canvasLayer.innerHTML = '';
            return;
        }

        try { closeFullscreen(false); } catch (_) {}

        unbindMainDrawing();
        unbindTools();

        if (state.onResize) window.removeEventListener('resize', state.onResize);

        if (state.ro) {
            try { state.ro.disconnect(); } catch (_) {}
            state.ro = null;
        }

        if (state.canvasLayer) state.canvasLayer.innerHTML = '';
        state.inited = false;
    }

    return { init, destroy, refresh };
})();

// ============================
// Finish modal helpers
// ============================

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

window.isAnsweredValue = function (type, value) {
    // input: "" -> false, "a" -> true
    // select: ""/null -> false, number -> true
    // radio: "answer_1" -> true
    if (type === 'input') {
        return (value ?? '').toString().trim() !== '';
    }
    if (type === 'select') {
        return !(value === '' || value === null || value === undefined);
    }
    if (type === 'radio') {
        return (value ?? '').toString().trim() !== '';
    }
    // fallback
    return (value ?? '').toString().trim() !== '';
};
