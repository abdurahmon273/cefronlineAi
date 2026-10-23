const partOneOneButton = document.getElementById('part-1-1-button');
const partOneTwoButton = document.getElementById('part-1-2-button');
const partTwoButton    = document.getElementById('part-2-button');

const questionDiv = document.getElementById('questionDiv');
const textAreaDiv = document.getElementById('textAreaDiv');

/**
 * Smooth/precise scroll to a child card inside scrollable container
 */

/**
 * Custom smooth scroll with controllable duration (slower & softer than native)
 */
function animateScrollTop(el, to, duration = 520) {
    if (!el) return;

    const start = el.scrollTop;
    const change = to - start;
    const startTime = performance.now();

    // easeInOutCubic (soft)
    const ease = (t) => (t < 0.5)
        ? 4 * t * t * t
        : 1 - Math.pow(-2 * t + 2, 3) / 2;

    function step(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);

        el.scrollTop = start + change * ease(progress);

        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}
/**
 * Smooth/precise scroll to a child card inside scrollable container
 * - slower & softer animation (custom duration)
 */
function scrollContainerToChild(container, child, opts = {}) {
    if (!container || !child) return;

    const duration = typeof opts.duration === 'number' ? opts.duration : 560; // ✅ biroz sekinroq
    const offset   = typeof opts.offset === 'number' ? opts.offset : 8;

    const containerRect = container.getBoundingClientRect();
    const childRect = child.getBoundingClientRect();

    const deltaTop  = childRect.top - containerRect.top;
    const targetTop = container.scrollTop + deltaTop - offset;

    const maxTop  = container.scrollHeight - container.clientHeight;
    const safeTop = Math.max(0, Math.min(targetTop, maxTop));

    // tiny nudge fix (some browsers ignore same scrollTop)
    const current = container.scrollTop;
    if (Math.abs(current - safeTop) < 1) {
        container.scrollTop = Math.max(0, Math.min(safeTop + 1, maxTop));
    }

    // ✅ custom smooth (slower)
    animateScrollTop(container, safeTop, duration);
}

/**
 * Find editor card wrapper
 * IMPORTANT: card classi "bg-white border-2 ..." bo‘lib qolishi kerak
 */
function getEditorCard(editorId) {
    const ta = document.getElementById(editorId);
    if (!ta) return null;
    return ta.closest('.bg-white.border-2');
}

function scrollQuestionTop() {
    if (!questionDiv) return;

    questionDiv.scrollTo({ top: 0, behavior: 'smooth' });

    // ✅ focus (tabindex bo‘lgani uchun ishlaydi)
    requestAnimationFrame(() => questionDiv.focus({ preventScroll: true }));
}

function scrollQuestionBottom() {
    if (!questionDiv) return;

    // ✅ pastdagi padding-bottom hisobga kirishi uchun scrollHeight ishlaydi
    questionDiv.scrollTo({ top: questionDiv.scrollHeight, behavior: 'smooth' });

    // ✅ focus
    requestAnimationFrame(() => questionDiv.focus({ preventScroll: true }));
}

/**
 * Focus Jodit instance
 */
function focusJodit(editorId) {
    const ed = window.Jodit?.instances?.[editorId];
    if (!ed) return;

    // Jodit focus sometimes needs a tiny delay
    requestAnimationFrame(() => ed.focus());
}

/**
 * =========================
 * Active UI (button styles)
 * =========================
 *
 * Active bo‘lsa:
 *  - bg-[#06b6d4]
 *  - text-white
 *  - scale kattaroq
 */
function setActivePart(activeId) {
    const ids = ['part-1-1-button', 'part-1-2-button', 'part-2-button'];

    ids.forEach((id) => {
        const btn = document.getElementById(id);
        if (!btn) return;

        const isActive = id === activeId;

        // marker class
        btn.classList.toggle('is-active', isActive);

        if (isActive) {
            // ACTIVE styles
            btn.classList.add(
                'bg-[#06b6d4]',
                'text-white',
                'shadow-lg',
                'scale-110',
                'ring-2',
                'ring-[#06b6d4]/30'
            );

            // remove inactive styles
            btn.classList.remove(
                'bg-white',
                'hover:bg-gray-50',
                'shadow-sm',
                'text-gray-700',
                'scale-100'
            );
        } else {
            // INACTIVE styles
            btn.classList.add(
                'bg-white',
                'text-gray-700',
                'shadow-sm',
                'hover:bg-gray-50',
                'scale-100'
            );

            // remove active styles
            btn.classList.remove(
                'bg-[#06b6d4]',
                'text-white',
                'shadow-lg',
                'scale-110',
                'ring-2',
                'ring-[#06b6d4]/30'
            );
        }
    });
}

/**
 * ======================================
 * Part mavjudligini backend inputdan tekshirish
 * ======================================
 * - task_one bo‘lmasa Part 1 buttonlari yashiriladi
 * - task_two bo‘lmasa Part 2 button yashiriladi
 */
function hideUnavailableParts() {
    const taskOneEl = document.getElementById('task_one');
    const taskTwoEl = document.getElementById('task_two');

    const hasTaskOne = !!(taskOneEl && String(taskOneEl.value || '').trim() && taskOneEl.value !== 'null' && taskOneEl.value !== '""');
    const hasTaskTwo = !!(taskTwoEl && String(taskTwoEl.value || '').trim() && taskTwoEl.value !== 'null' && taskTwoEl.value !== '""');

    // Part 1.1 + Part 1.2
    if (!hasTaskOne) {
        partOneOneButton?.classList.add('hidden');
        partOneTwoButton?.classList.add('hidden');
    } else {
        partOneOneButton?.classList.remove('hidden');
        partOneTwoButton?.classList.remove('hidden');
    }

    // Part 2
    if (!hasTaskTwo) {
        partTwoButton?.classList.add('hidden');
    } else {
        partTwoButton?.classList.remove('hidden');
    }

    // default active
    if (hasTaskOne) setActivePart('part-1-1-button');
    else if (hasTaskTwo) setActivePart('part-2-button');

    return { hasTaskOne, hasTaskTwo };
}

/**
 * =========================
 * AUTO ACTIVE (focus/typing)
 * =========================
 * Qaysi editor focus bo‘lsa yoki typing bo‘lsa -> button active
 */
function setActiveByEditor(editorId) {
    if (editorId === 'editor-1') setActivePart('part-1-1-button');
    if (editorId === 'editor-2') setActivePart('part-1-2-button');
    if (editorId === 'editor-3') setActivePart('part-2-button');
}

/**
 * Wait until Jodit instance exists, then attach events
 */
function waitForJodit(editorId, cb, timeoutMs = 8000) {
    const start = Date.now();
    const timer = setInterval(() => {
        const ed = window.Jodit?.instances?.[editorId];
        if (ed) {
            clearInterval(timer);
            cb(ed);
            return;
        }
        if (Date.now() - start > timeoutMs) {
            clearInterval(timer);
        }
    }, 120);
}

/**
 * Attach focus/change listeners for each editor
 */
function attachEditorAutoActive(editorId) {
    // 1) Jodit bo‘lsa: events orqali
    waitForJodit(editorId, (ed) => {
        try {
            ed.events?.on?.('focus', () => setActiveByEditor(editorId));
            ed.events?.on?.('change', () => setActiveByEditor(editorId));
            ed.events?.on?.('keyup', () => setActiveByEditor(editorId));
        } catch (e) {
            // ignore
        }

        // fallback: contenteditable focusin/input
        try {
            const root = ed?.editor || ed?.container;
            if (root) {
                root.addEventListener('focusin', () => setActiveByEditor(editorId));
                root.addEventListener('input',   () => setActiveByEditor(editorId));
                root.addEventListener('keyup',   () => setActiveByEditor(editorId));
            }
        } catch (e) {
            // ignore
        }
    });

    // 2) Fallback: textarea focus/input (Jodit bo‘lmasa ham)
    const ta = document.getElementById(editorId);
    if (ta) {
        ta.addEventListener('focus', () => setActiveByEditor(editorId));
        ta.addEventListener('input', () => setActiveByEditor(editorId));
        ta.addEventListener('keyup', () => setActiveByEditor(editorId));
    }
}

/**
 * CLICK handlers (button bosilganda scroll + focus + active)
 */
function bindClickHandlers() {
    partOneOneButton?.addEventListener('click', () => {
        const card = getEditorCard('editor-1');
        if (card) scrollContainerToChild(textAreaDiv, card, { duration: 650, offset: 10 });

        scrollQuestionTop();
        focusJodit('editor-1');

        setActivePart('part-1-1-button');
    });

    partOneTwoButton?.addEventListener('click', () => {
        const card = getEditorCard('editor-2');
        if (card) scrollContainerToChild(textAreaDiv, card, { duration: 650, offset: 10 });

        scrollQuestionTop();
        focusJodit('editor-2');

        setActivePart('part-1-2-button');
    });

    partTwoButton?.addEventListener('click', () => {
        const card = getEditorCard('editor-3');
        if (card) scrollContainerToChild(textAreaDiv, card, { duration: 650, offset: 10 });

        scrollQuestionBottom();
        focusJodit('editor-3');

        setActivePart('part-2-button');
    });
}

/**
 * Init
 */
document.addEventListener('DOMContentLoaded', () => {
    if (questionDiv && !questionDiv.hasAttribute('tabindex')) {
        questionDiv.setAttribute('tabindex', '0');
    }

    hideUnavailableParts();
    bindClickHandlers();

    // Auto active on focus/typing
    attachEditorAutoActive('editor-1');
    attachEditorAutoActive('editor-2');
    attachEditorAutoActive('editor-3');
});
