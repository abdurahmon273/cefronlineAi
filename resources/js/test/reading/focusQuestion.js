// ========= focusQuestion.js =========

// active focus pulse color (questionni topib berishda border pulse)
window.FOCUS_QUESTION_COLOR = window.FOCUS_QUESTION_COLOR || '#2563eb'; // blue-600 default

// filled state color (javob berilgan indikator rangi)
window.FOCUS_FILLED_COLOR = window.FOCUS_FILLED_COLOR || '#00ff00'; // pen green

// ✅ active (tanlangan) button border rangi
window.FOCUS_ACTIVE_BORDER_COLOR = window.FOCUS_ACTIVE_BORDER_COLOR || '#2563eb'; // cyan-500

window.getFocusQuestionWrap = function () {
    return document.getElementById('focusQuestionWrap');
};

window.getQuestionOrderFromEl = function (el) {
    const v = el?.dataset?.questionorder;
    const n = v ? parseInt(v, 10) : NaN;
    return Number.isFinite(n) ? n : null;
};

window.findQuestionTargetsByOrder = function (order) {
    const direct = document.querySelector(
        `[data-questionorder="${order}"][data-questiontype="input"],` +
        `[data-questionorder="${order}"][data-questiontype="select"]`
    );
    if (direct) return { type: direct.dataset.questiontype, el: direct };

    const radioCard = document.querySelector(
        `[data-questionorder="${order}"][data-questiontype^="radio"]`
    );
    if (radioCard) return { type: radioCard.dataset.questiontype, el: radioCard };

    const any = document.querySelector(`[data-questionorder="${order}"]`);
    if (any) return { type: any.dataset.questiontype || 'unknown', el: any };

    return null;
};

// =========================
// CUSTOM SELECT focus helpers (READING)
// =========================

// native select (sr-only) dan custom button'ni topib beradi
window.getCustomSelectBtnFromNative = function (nativeSelect) {
    if (!nativeSelect) return null;

    // 1) eng yaqin cs-root ichidan button
    const root = nativeSelect.closest?.('[data-cs-root="1"]');
    if (root) {
        const btn = root.querySelector?.('[data-cs-btn="1"]');
        if (btn) return btn;
    }

    // 2) fallback: select parent ichidan qidirish
    const btn2 = nativeSelect.parentElement?.querySelector?.('[data-cs-btn="1"]');
    return btn2 || null;
};

window.getFocusableTargetEl = function (found) {
    if (!found?.el) return null;

    // ✅ select -> custom button (ko'rinadigan control)
    if (found.type === 'select') {
        const nativeSelect = found.el;
        return window.getCustomSelectBtnFromNative(nativeSelect) || nativeSelect;
    }

    // ✅ input/radio -> o'zi
    return found.el;
};

window.getPartIndexFromFound = function (found) {
    if (!found?.el) return null;

    if (found.type === 'select') {
        return window.getPartIndexFromElement(found.el); // native select id: part-2-... yoki part-3-...
    }

    const target = window.getFocusableTargetEl(found);
    return window.getPartIndexFromElement(target);
};

window.getPartIndexFromElement = function (el) {
    const id = el?.id || '';
    const m = id.match(/^part-(\d+)-/);
    if (!m) return null;
    const n = parseInt(m[1], 10);
    return Number.isFinite(n) ? n : null;
};

window.openPartByNumber = function (partNum) {
    const av = window.availableParts || [];
    const btnEls = window.btnElements || [];

    const idx = av.findIndex((d) => d?.num === partNum);
    if (idx >= 0) window.setActive(idx, av, btnEls);
};

window.injectFocusKeyframesOnce = function () {
    if (document.getElementById('focusQuestionKeyframes')) return;

    const style = document.createElement('style');
    style.id = 'focusQuestionKeyframes';
    style.textContent = `
    @keyframes fqPulseBorder {
      0%   { box-shadow: 0 0 0 0 var(--fqColor); border-color: var(--fqColor); }
      70%  { box-shadow: 0 0 0 8px transparent; border-color: var(--fqColor); }
      100% { box-shadow: 0 0 0 0 transparent; border-color: inherit; }
    }
    .fq-pulse {
      animation: fqPulseBorder 3s ease-out 1;
      border-color: var(--fqColor) !important;
    }
  `;
    document.head.appendChild(style);
};

window.injectFocusActiveBtnStylesOnce = function () {
    if (document.getElementById('focusActiveBtnStyles')) return;

    const style = document.createElement('style');
    style.id = 'focusActiveBtnStyles';
    style.textContent = `
      .fq-active {
        border-color: var(--fqActiveBorder) !important;
      }
      /* active bo'lganda filled bo'lsa ham border shu rang bo'lsin */
      .fq-filled.fq-active {
        border-color: var(--fqActiveBorder) !important;
      }
    `;
    document.head.appendChild(style);
};

window.clearFocusPulse = function () {
    document.querySelectorAll('.fq-pulse').forEach((el) => {
        el.classList.remove('fq-pulse');
        el.style.removeProperty('--fqColor');
    });
};

window.applyFocusPulse = function (targetEl) {
    if (!targetEl) return;

    window.injectFocusKeyframesOnce();
    window.clearFocusPulse();

    targetEl.style.setProperty('--fqColor', window.FOCUS_QUESTION_COLOR);
    targetEl.classList.add('fq-pulse');

    // ✅ scroll
    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // ✅ focus (button/custom select ham)
    try { targetEl.focus?.({ preventScroll: true }); } catch (_) {}

    setTimeout(() => {
        targetEl.classList.remove('fq-pulse');
        targetEl.style.removeProperty('--fqColor');
    }, 3100);
};

window.setActiveOrderButton = function (activeOrder) {
    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return;

    window.injectFocusActiveBtnStylesOnce();

    wrap.querySelectorAll('[data-focus-order]').forEach((btn) => {
        const order = parseInt(btn.dataset.focusOrder, 10);
        const isActive = order === activeOrder;

        btn.style.setProperty('--fqActiveBorder', window.FOCUS_ACTIVE_BORDER_COLOR);
        btn.classList.toggle('fq-active', isActive);

        // bg doim oq qoladi (siz xohlaganidek)
        btn.classList.add('bg-white');
    });
};

window.buildFocusNumbersUI = function (orders) {
    const wrap = window.getFocusQuestionWrap();
    if (!wrap) return;

    wrap.innerHTML = '';

    const card = document.createElement('div');
    card.className = 'bg-white rounded-2xl p-4';

    const title = document.createElement('div');
    title.className = 'text-gray-900 font-semibold text-sm';
    title.textContent = 'Question numbers';

    const row = document.createElement('div');
    row.className = 'mt-4 flex flex-wrap gap-2';

    orders.forEach((order) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.dataset.focusOrder = String(order);

        // ✅ dizayn aynan siz berganidek
        btn.className = [
            'w-[56px]',
            'h-9',
            'border-2',
            'rounded-2xl',
            'bg-white',
            'hover:bg-gray-50',
            'active:scale-[0.98]',
            'transition',
            'flex',
            'items-center',
            'gap-[6px]',
            'px-[2px]',
            'relative'
        ].join(' ');

        const dot = document.createElement('span');
        dot.className = [
            'fq-dot',
            'w-5', 'h-5',
            'rounded-full',
            'border-2',
            'flex', 'items-center', 'justify-center',
            'text-[11px]', 'font-extrabold',
            'shrink-0'
        ].join(' ');
        dot.textContent = '';

        const num = document.createElement('span');
        num.className = [
            'fq-num',
            'font-extrabold',
            'text-sm',
            'text-gray-900',
            'tabular-nums',
            'leading-none',
            'pr-1'
        ].join(' ');
        num.textContent = String(order);

        btn.appendChild(dot);
        btn.appendChild(num);

        btn.addEventListener('click', () => {
            window.focusQuestionByOrder(order);
        });

        row.appendChild(btn);
    });

    card.appendChild(title);
    card.appendChild(row);
    wrap.appendChild(card);
};

window.collectAllQuestionOrders = function () {
    const nodes = document.querySelectorAll('[data-questionorder]');
    const set = new Set();

    nodes.forEach((el) => {
        const n = window.getQuestionOrderFromEl(el);
        if (n != null) set.add(n);
    });

    return Array.from(set).sort((a, b) => a - b);
};

window.focusQuestionByOrder = function (order) {
    const found = window.findQuestionTargetsByOrder(order);
    if (!found) return;

    const partNum = window.getPartIndexFromFound(found);
    if (partNum != null) window.openPartByNumber(partNum);

    const target = window.getFocusableTargetEl(found);
    window.applyFocusPulse(target);

    // ✅ active border
    window.setActiveOrderButton(order);
};


window.generateFocusQuestions = function () {
    const orders = window.collectAllQuestionOrders();
    if (!orders.length) return;

    window.buildFocusNumbersUI(orders);

    // default active = 1-chi
    window.focusQuestionByOrder(orders[0]);
};

// =============================
// FILLED state (yashil ✓)
// =============================
window.injectFocusFilledBtnStylesOnce = function () {
    if (document.getElementById('focusFilledBtnStyles')) return;

    const style = document.createElement('style');
    style.id = 'focusFilledBtnStyles';
    style.textContent = `
      .fq-filled {
        border-color: var(--fqFillColor) !important;
        background: rgba(0, 255, 0, 0.10) !important;
      }
      .fq-filled:hover {
        background: rgba(0, 255, 0, 0.14) !important;
      }
      .fq-filled .fq-dot {
        border-color: var(--fqFillColor) !important;
        background: rgba(0, 255, 0, 0.22) !important;
        color: #065f46 !important;
      }
    `;
    document.head.appendChild(style);
};

window.isFilledValue = function (value) {
    if (value == null) return false;
    if (typeof value === 'number') return !Number.isNaN(value);

    const s = String(value).trim();
    return s !== '' && s !== 'null' && s !== 'undefined';
};

window.setOrderButtonFilledState = function (btn, isFilled) {
    if (!btn) return;

    window.injectFocusFilledBtnStylesOnce();

    btn.style.setProperty('--fqFillColor', window.FOCUS_FILLED_COLOR);
    btn.classList.toggle('fq-filled', !!isFilled);

    const dot = btn.querySelector('.fq-dot');
    if (dot) dot.textContent = isFilled ? '✓' : '';
};

window.getOrderButtonEl = function (order) {
    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return null;
    return wrap.querySelector(`[data-focus-order="${order}"]`);
};

window.generateThisOrderButton = function (order, value) {
    const btn = window.getOrderButtonEl(order);
    if (!btn) return;

    const filled = window.isFilledValue(value);
    window.setOrderButtonFilledState(btn, filled);
};

window.generateAllOrderButtons = function (draft) {
    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return;

    // reset all empty
    wrap.querySelectorAll('[data-focus-order]').forEach(btn => {
        window.setOrderButtonFilledState(btn, false);
    });

    if (!draft || typeof draft !== 'object') return;

    const parts = ['part_one', 'part_two', 'part_three', 'part_four', 'part_five'];

    parts.forEach(pk => {
        const qd = draft?.[pk]?.question_data;
        if (!qd || typeof qd !== 'object') return;

        Object.keys(qd).forEach(elId => {
            const item = qd[elId];
            if (!item) return;

            const order = Number(item.order || item.questionorder || item.questionOrder || 0);
            if (!order) return;

            const filled = window.isFilledValue(item.value);
            window.generateThisOrderButton(order, filled ? item.value : '');
        });
    });

    // agar hozir active bo'lib turgan bo'lsa border rangi qolishi uchun
    // (class allaqachon turadi, shunchaki var ni qo'yib ketamiz)
    const activeBtn = wrap.querySelector('.fq-active');
    if (activeBtn) {
        activeBtn.style.setProperty('--fqActiveBorder', window.FOCUS_ACTIVE_BORDER_COLOR);
    }
};
