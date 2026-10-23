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

window.getFocusableTargetEl = function (found) {
    if (!found?.el) return null;

    // ✅ select bo‘lsa: custom select button’ni topamiz
    if (found.type === 'select') {
        const nativeSelect = found.el;

        // generateTestHelper.js ichida bor bo‘lsa ishlatamiz
        const btn =
            window.getCustomSelectBtnFromNative?.(nativeSelect) ||
            nativeSelect.closest?.('[data-cs-root="1"]')?.querySelector?.('[data-cs-btn="1"]');

        return btn || nativeSelect;
    }

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
        animation: fqPulseBorder 2.0s ease-out 1;
        border-color: var(--fqColor) !important;
      }

      /* ✅ To'g'ri javob (yashil + ✓) */
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

      /* ❌ Noto'g'ri javob (qizil + ✗) */
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
        color: #7f1d1d !important;
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

    // ✅ custom select button bo‘lsa, scroll+pulse shunga tushadi
    targetEl.style.setProperty('--fqColor', '#2563eb');
    targetEl.classList.add('fq-pulse');

    // ✅ focus ham beramiz (tabindex yo‘q bo‘lsa ham)
    try { targetEl.focus?.({ preventScroll: true }); } catch (_) {}

    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

    setTimeout(() => {
        targetEl.classList.remove('fq-pulse');
        targetEl.style.removeProperty('--fqColor');
    }, 2100);
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

    // ✅ partni aniqlash: select bo‘lsa native select id’dan olamiz
    const partNum = window.getPartIndexFromFound(found);
    if (partNum != null) window.openPartByNumber(partNum);

    // ✅ focus/pulse target: select => custom btn, boshqalar => o‘zi
    const target = window.getFocusableTargetEl(found);
    window.applyFocusPulse(target);
};


window.getOrderState = function (order) {
    return window.__RESULT_STATE_BY_ORDER__?.[order] || { status: 'not_answered', is_correct: null };
};

window.ensureFocusStatsCard = function () {
    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return null;

    let stats = wrap.querySelector('[data-focus-stats="1"]');
    if (stats) return stats;

    stats = document.createElement('div');
    stats.dataset.focusStats = '1';
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
    const orders = window.collectAllQuestionOrders?.() || [];
    let correct = 0, wrong = 0, na = 0;

    orders.forEach(order => {
        const st = window.getOrderState(order);
        if (st?.status !== 'answered') { na++; return; }
        if (Number(st?.is_correct) === 1) correct++;
        else if (Number(st?.is_correct) === 0) wrong++;
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

        // ✅ dizayn aynan reading page dek
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

    window.ensureFocusStatsCard();
    window.updateFocusStatsUI();
};

window.updateFocusButtonsByResult = function () {
    window.injectFocusKeyframesOnce();

    const wrap = window.getFocusQuestionWrap?.();
    if (!wrap) return;

    wrap.querySelectorAll('[data-focus-order]').forEach(btn => {
        const order = parseInt(btn.dataset.focusOrder, 10);
        const st = window.getOrderState(order);

        btn.classList.remove('fq-correct', 'fq-wrong');

        const dot = btn.querySelector('.fq-dot');

        if (st?.status === 'answered') {
            if (Number(st?.is_correct) === 1) {
                btn.classList.add('fq-correct');
                if (dot) dot.textContent = '✓';
            }
            else if (Number(st?.is_correct) === 0) {
                btn.classList.add('fq-wrong');
                if (dot) dot.textContent = '✗';
            }
        } else {
            // not answered - empty dot
            if (dot) dot.textContent = '';
        }
    });

    window.updateFocusStatsUI?.();
};

window.generateFocusQuestions = function () {
    const orders = window.collectAllQuestionOrders();
    if (!orders.length) return;

    window.buildFocusNumbersUI(orders);
    window.updateFocusButtonsByResult();
    window.focusQuestionByOrder(orders[0]);
};
