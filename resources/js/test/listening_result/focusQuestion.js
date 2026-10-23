// resources/js/test/listening_result/focusQuestion.js
// EXACT COPY of listening/focusQuestion.js Logic for UI generation
// BUT adapted for Result colors (Green/Red)

window.FOCUS_QUESTION_COLOR = window.FOCUS_QUESTION_COLOR || '#2563eb';

window.getFocusQuestionWrap = function () {
    return document.getElementById('focusQuestionWrap');
};

window.getQuestionOrderFromEl = function (el) {
    const v = el?.dataset?.questionorder;
    const n = v ? parseInt(v, 10) : NaN;
    return Number.isFinite(n) ? n : null;
};

// =============================
// HELPER: Find target element
// =============================
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

window.getPartNumByOrder = function (order) {
    if (order >= 1 && order <= 8) return 1;
    if (order >= 9 && order <= 14) return 2;
    if (order >= 15 && order <= 18) return 3;
    if (order >= 19 && order <= 23) return 4;
    if (order >= 24 && order <= 29) return 5;
    if (order >= 30 && order <= 35) return 6;
    return null; // Fallback or dynamic
};

// =============================
// NAVIGATION
// =============================
window.focusQuestionByOrder = function (order) {
    const partNum = window.getPartNumByOrder(order);
    if (partNum == null) return;

    // Switch Part
    // We assume window.availableParts and window.setActive are global (from listening_result.js)
    const av = window.availableParts || [];
    const btnEls = window.btnElements || [];

    // Find index
    const idx = av.findIndex(p => p.num === partNum);
    if (idx !== -1) {
        window.setActive(idx, av, btnEls, { force: true });
    }

    // Scroll to Element
    setTimeout(() => {
        const found = window.findQuestionTargetsByOrder(order);
        if (found) {
            found.el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Pulse Effect
            found.el.style.transition = 'box-shadow 0.3s, border-color 0.3s';
            found.el.classList.add('ring-4', 'ring-blue-300');
            setTimeout(() => found.el.classList.remove('ring-4', 'ring-blue-300'), 2000);
        }

        // Update Underline
        window.updateFocusNumberUnderline(window.getFocusQuestionWrap(), order);
    }, 50);
};

// =============================
// UI GENERATION (The exact grid from listening)
// =============================
window.buildFocusNumbersUI = function (orders) {
    const wrap = window.getFocusQuestionWrap();
    if (!wrap) return;

    wrap.innerHTML = '';

    const card = document.createElement('div');
    card.className = 'bg-white rounded-2xl p-4';
    card.dataset.focusStats = '1';

    const title = document.createElement('div');
    title.className = 'text-gray-900 font-semibold text-sm';
    title.textContent = 'Question numbers';

    const row = document.createElement('div');
    row.className = 'mt-4 flex flex-wrap gap-2';

    orders.forEach((order) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.dataset.focusOrder = String(order);
        btn.id = `focus-btn-${order}`;

        // Modern design from reading_result
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

    // Add Stats Section for Result Page
    window.ensureFocusStatsCard();
    window.updateFocusStatsUI();
};

window.updateFocusNumberUnderline = function (wrapEl, activeOrder) {
    if (!wrapEl) return;
    wrapEl.querySelectorAll('[data-focus-order]').forEach((btn) => {
        const order = parseInt(btn.dataset.focusOrder, 10);
        const line = btn.querySelector('.fq-underline');
        const isActive = order === activeOrder;

        if (line) line.classList.toggle('hidden', !isActive);
        // We do not change bg color here to avoid wiping result colors
    });
};

window.updateFocusButtonsByResult = function () {
    const state = window.__RESULT_STATE_BY_ORDER__ || {};

    // Inject CSS for result colors
    if (!document.getElementById('fq-result-styles')) {
        const style = document.createElement('style');
        style.id = 'fq-result-styles';
        style.textContent = `
            .fq-correct {
                border-color: #22c55e !important;
                background: rgba(34,197,94,0.10) !important;
            }
            .fq-correct:hover {
                background: rgba(34,197,94,0.14) !important;
            }
            .fq-correct .fq-dot {
                border-color: #22c55e !important;
                background: rgba(34,197,94,0.22) !important;
                color: #065f46 !important;
            }
            .fq-wrong {
                border-color: #ef4444 !important;
                background: rgba(239,68,68,0.10) !important;
            }
            .fq-wrong:hover {
                background: rgba(239,68,68,0.14) !important;
            }
            .fq-wrong .fq-dot {
                border-color: #ef4444 !important;
                background: rgba(239,68,68,0.22) !important;
                color: #991b1b !important;
            }
        `;
        document.head.appendChild(style);
    }

    Object.keys(state).forEach(orderKey => {
        const btn = document.getElementById(`focus-btn-${orderKey}`);
        if (!btn) return;

        const s = state[orderKey];
        const dot = btn.querySelector('.fq-dot');

        // Remove previous result classes
        btn.classList.remove('fq-correct', 'fq-wrong');

        if (s.status !== 'answered') {
            // Not Answered -> empty dot
            if (dot) dot.textContent = '';
        } else {
            if (Number(s.is_correct) === 1) {
                // Correct -> Green with ✓
                btn.classList.add('fq-correct');
                if (dot) dot.textContent = '✓';
            } else if (Number(s.is_correct) === 0) {
                // Incorrect -> Red with ✗
                btn.classList.add('fq-wrong');
                if (dot) dot.textContent = '✗';
            } else {
                // Unknown -> empty
                if (dot) dot.textContent = '';
            }
        }
    });
};

window.ensureFocusStatsCard = function () {
    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return null;

    let stats = wrap.querySelector('[data-focus-stats-card=\"1\"]');
    if (stats) return stats;

    stats = document.createElement('div');
    stats.dataset.focusStatsCard = '1';
    stats.className = 'mt-3 bg-white rounded-2xl p-4';

    stats.innerHTML = `
      <div class="grid grid-cols-1 gap-2 text-xs sm:text-sm">
        <div class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 border-2 border-green-500"></span>
            <span class="font-semibold text-gray-800">Correct answers</span>
          </span>
          <span class="font-bold text-gray-900" data-stat-correct>0</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 border-2 border-red-500"></span>
            <span class="font-semibold text-gray-800">Wrong answers</span>
          </span>
          <span class="font-bold text-gray-900" data-stat-wrong>0</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 border-2 border-gray-300"></span>
            <span class="font-semibold text-gray-800">Not answered</span>
          </span>
          <span class="font-bold text-gray-900" data-stat-na>0</span>
        </div>
      </div>
    `;
    wrap.appendChild(stats);
    return stats;
};

window.updateFocusStatsUI = function () {
    const orders = window.collectAllQuestionOrders?.() || []; // Ensure this helper exists or reuse logic

    const state = window.__RESULT_STATE_BY_ORDER__ || {};
    let correct = 0, wrong = 0, na = 0;

    Object.values(state).forEach(s => {
        if (s.status !== 'answered') na++;
        else if (Number(s.is_correct) === 1) correct++;
        else if (Number(s.is_correct) === 0) wrong++;
        else na++;
    });

    const stats = window.ensureFocusStatsCard();
    if (!stats) return;

    const c = stats.querySelector('[data-stat-correct]');
    const w = stats.querySelector('[data-stat-wrong]');
    const n = stats.querySelector('[data-stat-na]');

    if (c) c.textContent = String(correct);
    if (w) w.textContent = String(wrong);
    if (n) n.textContent = String(na);
};


// Main Init
window.generateFocusQuestions = function () {
    const nodes = document.querySelectorAll('[data-questionorder]');
    const set = new Set();
    nodes.forEach((el) => {
        const n = window.getQuestionOrderFromEl(el);
        if (n != null) set.add(n);
    });
    const orders = Array.from(set).sort((a, b) => a - b);

    if (!orders.length) return;

    window.buildFocusNumbersUI(orders);

    // Check if result data is available and update buttons
    if (window.updateFocusButtonsByResult) window.updateFocusButtonsByResult();

    // Default 1st
    window.updateFocusNumberUnderline(window.getFocusQuestionWrap(), orders[0]);
};
