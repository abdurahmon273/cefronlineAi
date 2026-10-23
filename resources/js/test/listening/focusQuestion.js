// active focus pulse color
window.FOCUS_QUESTION_COLOR = window.FOCUS_QUESTION_COLOR || '#2563eb';

// filled state color (javob berilgan)
window.FOCUS_FILLED_COLOR = window.FOCUS_FILLED_COLOR || '#00ff00';

// active button border rangi
window.FOCUS_ACTIVE_BORDER_COLOR = window.FOCUS_ACTIVE_BORDER_COLOR || '#2563eb';

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

window.getPartIndexFromElement = function (el) {
    const id = el?.id || '';
    const m = id.match(/^part-(\d+)-/);
    if (!m) return null;
    const n = parseInt(m[1], 10);
    return Number.isFinite(n) ? n : null;
};

window.openPartByNumber = function (partNum, opts = { force: false }) {
    const av = window.availableParts || [];
    const btnEls = window.btnElements || [];
    const idx = av.findIndex((d) => d?.num === partNum);
    if (idx >= 0) window.setActive(idx, av, btnEls, opts);
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

    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

    if (targetEl.tagName === 'INPUT' || targetEl.tagName === 'SELECT') {
        try { targetEl.focus({ preventScroll: true }); }
        catch (_) { try { targetEl.focus(); } catch (_) { } }
    }

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

        btn.classList.add('bg-white');
    });
};

window.updateFocusNumberUnderline = function (wrapEl, activeOrder) {
    window.setActiveOrderButton(activeOrder);
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

// listening part is resolved from rendered DOM, so subset tests stay dynamic
window.getPartNumByOrder = function (order) {
    const found = window.findQuestionTargetsByOrder(order);
    if (!found?.el) return null;
    return window.getPartIndexFromElement(found.el);
};

window.getCurrentActivePartNum = function () {
    const idx = Number.isFinite(window.__ACTIVE_PART_INDEX__) ? window.__ACTIVE_PART_INDEX__ : 0;
    const av = window.availableParts || [];
    return av?.[idx]?.num ?? null;
};

window.focusQuestionByOrder = function (order) {
    const partNum = window.getPartNumByOrder(order);
    if (partNum == null) return;

    const currentPartNum = window.getCurrentActivePartNum();

    if (window.AUDIO_CONTROL === 1) {
        if (currentPartNum !== partNum) return;
    } else {
        if (currentPartNum !== partNum) {
            // PART o‘zgarsa audio o‘zgaradi, bir xil part ichida audio tegmaydi
            window.openPartByNumber(partNum, { force: true });
        }
    }

    const found = window.findQuestionTargetsByOrder(order);
    if (!found) return;

    window.applyFocusPulse(found.el);

    const wrap = window.getFocusQuestionWrap();
    window.updateFocusNumberUnderline(wrap, order);
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

        // dizayn aynan reading page dek
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

        btn.addEventListener('click', () => window.focusQuestionByOrder(order));

        row.appendChild(btn);
    });

    card.appendChild(title);
    card.appendChild(row);
    wrap.appendChild(card);
};

// =============================
// filled state (same as reading)
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

window.FOCUS_FILLED_COLOR = window.FOCUS_FILLED_COLOR || '#00ff00';

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

    wrap.querySelectorAll('[data-focus-order]').forEach(btn => {
        window.setOrderButtonFilledState(btn, false);
    });

    if (!draft || typeof draft !== 'object') return;

    const parts = ['part_one', 'part_two', 'part_three', 'part_four', 'part_five', 'part_six'];

    parts.forEach(pk => {
        const qd = draft?.[pk]?.question_data;
        if (!qd || typeof qd !== 'object') return;

        Object.keys(qd).forEach(elId => {
            const item = qd[elId];
            if (!item) return;

            const order = Number(item.order || item.questionorder || item.questionOrder || 0);
            if (!order) return;

            const filled = window.isFilledValue(item.value);
            if (filled) window.generateThisOrderButton(order, item.value);
            else window.generateThisOrderButton(order, '');
        });
    });
};

window.updateFocusButtonsLockState = function () {
    const wrap = window.getFocusQuestionWrap();
    if (!wrap) return;

    const currentPartNum = window.getCurrentActivePartNum();

    wrap.querySelectorAll('[data-focus-order]').forEach((btn) => {
        const order = parseInt(btn.dataset.focusOrder, 10);
        if (!Number.isFinite(order)) return;

        if (window.AUDIO_CONTROL === 0) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
            return;
        }

        const partNum = window.getPartNumByOrder(order);
        const allowed = partNum != null && currentPartNum != null && partNum === currentPartNum;

        btn.disabled = !allowed;
        btn.classList.toggle('opacity-60', !allowed);
        btn.classList.toggle('cursor-not-allowed', !allowed);
    });
};

window.generateFocusQuestions = function () {
    const orders = window.collectAllQuestionOrders();
    if (!orders.length) return;

    window.buildFocusNumbersUI(orders);
    window.updateFocusButtonsLockState();

    const first = orders[0];
    window.updateFocusNumberUnderline(window.getFocusQuestionWrap(), first);
};

// hook: part o‘zgarganda lock state update qilish uchun
window.onListeningPartChanged = function () {
    window.updateFocusButtonsLockState();
};

document.addEventListener('DOMContentLoaded', () => {
    window.generateFocusQuestions();
});
