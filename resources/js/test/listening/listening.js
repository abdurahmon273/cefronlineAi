// resources/js/test/listening/listening.js

const exerciseId = document.getElementById('exercise_id')?.value || '';
const isMock = document.getElementById('is_mock')?.value || 0;
window.__LISTENING_ALLOW_UNLOAD__ = false;

console.log(isMock)
window.LISTENING_STORAGE_KEY = 'cefr-listening-draft' + exerciseId;
window.LISTENING_TEXT_SIZE_KEY = 'cefr-text-size-draft';
window.LISTENING_AUDIO_DRAFT_KEY = 'cefr-listening-audio-draft' + exerciseId;

import '../camera.js';
import './textSize.js';
import './storage.js';
import './generateTest.js';
import './generateTestHelper.js';
import './focusQuestion.js';
import './audio.js';
import '../reading/custom_select.js';
import '../colorPen.js';



window.cancelFinishModal = function () {
    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    card.classList.remove('scale-100', 'translate-y-0');
    card.classList.add('scale-95', 'translate-y-2');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
};

// ===============================
// FINISH TEST
// ===============================
window.finishListeningTest = function () {
    // ✅ submit ketayotganda mock beforeunload confirm chiqmasin
    window.__LISTENING_ALLOW_UNLOAD__ = true;

    const draft = window.getListeningDraft?.() || {};

    const input = document.getElementById('listening-draft-input');
    if (input) input.value = JSON.stringify(draft || {});

    // draftlarni tozalaymiz
    localStorage.removeItem(window.LISTENING_STORAGE_KEY);
    localStorage.removeItem(window.LISTENING_TEXT_SIZE_KEY);
    localStorage.removeItem(window.LISTENING_AUDIO_DRAFT_KEY);

    const form = document.getElementById('listening-form');
    if (form) form.submit();
};

// ===============================
// LISTENING FINISH MODAL (2XL + ACCORDION + SHOW)
// 6 parts: part_one .. part_six
// ===============================

// ✅ NEW: MOCK helper
window.isListeningMockMode = function () {
    const v = document.getElementById('is_mock')?.value || 0;
    return Number(v) === 1; // str/int farqi yo'q
};

// ✅ NEW: modalni mock holatga moslab qo'yish
window.applyFinishModalMockState = function () {
    const isMock = window.isListeningMockMode();

    const modal = document.getElementById('finishModal');
    if (modal) modal.dataset.mockLocked = isMock ? '1' : '0';

    const cancelBtn = document.getElementById('finishCancelBtn');
    if (cancelBtn) cancelBtn.classList.toggle('hidden', isMock);

    // X close button (agar sizda bo'lsa)
    const closeBtn =
        document.getElementById('finishCloseBtn') ||
        document.querySelector('[data-finish-close="1"]') ||
        null;
    if (closeBtn) closeBtn.classList.toggle('hidden', isMock);

    // ✅ backdrop click / ESC ni mockda bloklash
    if (modal && !modal.dataset.boundLock) {
        modal.dataset.boundLock = '1';

        // backdrop click: card tashqarisini bosganda
        modal.addEventListener('click', (e) => {
            if (modal.dataset.mockLocked === '1') return;

            const card = document.getElementById('finishModalCard');
            if (card && !card.contains(e.target)) {
                window.closeListeningFinishModal();
            }
        });

        // ESC
        window.addEventListener('keydown', (e) => {
            if (modal.dataset.mockLocked === '1') return;
            if (e.key === 'Escape') window.closeListeningFinishModal();
        });
    }
};


// ---------- OPEN / CLOSE ----------
window.openListeningFinishModal = function () {
    window.regenerateListeningFinishModal(window.availableParts || []);

    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    // ✅ mock state apply (X/Cancel yashiradi + lock qiladi)
    window.applyFinishModalMockState();

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95', 'translate-y-2');
        card.classList.add('scale-100', 'translate-y-0');
    });

    const cancelBtn = document.getElementById('finishCancelBtn');
    const acceptBtn = document.getElementById('finishAcceptBtn');

    // cancel faqat mock=0 da ishlasin
    if (cancelBtn && !cancelBtn.dataset.bound) {
        cancelBtn.dataset.bound = '1';
        cancelBtn.addEventListener('click', () => {
            if (window.isListeningMockMode()) return;
            window.closeListeningFinishModal();
        });
    }

    // accept har doim ishlaydi
    if (acceptBtn && !acceptBtn.dataset.bound) {
        acceptBtn.dataset.bound = '1';
        acceptBtn.addEventListener('click', window.finishListeningTest);
    }

    // X close (agar bor bo'lsa) mock=0 da ishlasin
    const closeBtn =
        document.getElementById('finishCloseBtn') ||
        document.querySelector('[data-finish-close="1"]') ||
        null;

    if (closeBtn && !closeBtn.dataset.bound) {
        closeBtn.dataset.bound = '1';
        closeBtn.addEventListener('click', () => {
            if (window.isListeningMockMode()) return;
            window.closeListeningFinishModal();
        });
    }
};

window.closeListeningFinishModal = function () {
    // ✅ MOCK: modal yopilmasin
    if (window.isListeningMockMode()) return;

    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    card.classList.remove('scale-100', 'translate-y-0');
    card.classList.add('scale-95', 'translate-y-2');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
};

// Keep compatibility if somewhere calls old name
window.cancelListeningFinishModal = window.closeListeningFinishModal;


// ===============================
// UI BUILDERS
// ===============================

window.makeFinishQuestionRow = function ({ order, answered, displayValue, onShow }) {
    const row = document.createElement('div');

    row.className = [
        'flex items-center justify-between rounded-xl px-3 py-2 border',
        answered ? 'border-gray-200 bg-white' : 'border-amber-200 bg-amber-50'
    ].join(' ');

    const left = document.createElement('div');
    left.className = 'flex items-center gap-3 min-w-0';

    const ord = document.createElement('span');
    ord.className = 'font-semibold text-gray-700';
    ord.textContent = `${order}.`;

    const val = document.createElement('span');
    val.className = ['text-sm truncate', answered ? 'text-gray-900' : 'text-amber-700'].join(' ');
    val.textContent = answered ? (displayValue || 'Answered') : 'Not answered';

    left.appendChild(ord);
    left.appendChild(val);

    row.appendChild(left);

    // ✅ MOCK=1 bo'lsa Show button bo'lmaydi
    if (!window.isListeningMockMode()) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = [
            'text-xs px-2.5 py-1.5 rounded-lg font-semibold transition',
            answered
                ? 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                : 'bg-cyan-500 text-white hover:bg-cyan-600 shadow-sm'
        ].join(' ');
        btn.textContent = 'Show';

        btn.addEventListener('click', () => onShow?.(order));
        row.appendChild(btn);
    }

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
// REGENERATE MODAL BODY (6 parts)
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

window.regenerateListeningFinishModal = function (availableParts) {
    const body = document.getElementById('finishModalBody');
    if (!body) return;

    body.innerHTML = '';

    const draft = window.getListeningDraft?.() || {};

    const keyMap = {
        1: 'part_one',
        2: 'part_two',
        3: 'part_three',
        4: 'part_four',
        5: 'part_five',
        6: 'part_six',
    };

    (availableParts || []).forEach((p, idx) => {
        const panel = document.getElementById(p.panelId);
        if (!panel) return;

        const orders = window.getUniqueOrdersInPanel(panel);
        const total = orders.length;

        const partDraft = draft?.[keyMap[p.num]];
        const answeredCount = window.countAnsweredInDraftPart(partDraft);

        const items = orders.map(order => {
            const q = Object.values(partDraft?.question_data || {}).find(x => Number(x?.order) === Number(order));

            const type = q?.type || (document.querySelector(`[data-questionorder="${order}"]`)?.dataset?.questiontype ?? '');
            const val  = q?.value;

            const answered = !!q && window.isAnsweredValue(type, val);

            const displayValue = answered
                ? window.getDisplayValueFromDraftAndDOM({ order, type, value: val })
                : '';

            return window.makeFinishQuestionRow({
                order,
                answered,
                displayValue,
                onShow: (ord) => {
                    // mockda show yo'q, mock=0 da ishlaydi
                    window.closeListeningFinishModal();
                    setTimeout(() => window.focusQuestionByOrder(ord), 220);
                }
            });
        });

        const partEl = window.makeFinishAccordionPart({
            partNum: p.num,
            total,
            answered: answeredCount,
            items,
            openByDefault: idx === 0
        });

        body.appendChild(partEl);
    });

    // ✅ regenerate'dan keyin ham mock lock apply
    window.applyFinishModalMockState();
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
