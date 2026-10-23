window.clearAnswerTextClasses = function (el) {
    if (!el) return;
    for (let i = 1; i <= 9; i++) el.classList.remove(`answer-text-${i}`);
};

window.applyAnswerTextSizeClass = function (el, size) {
    if (!el) return;
    window.clearAnswerTextClasses(el);
    el.classList.add('answer-text', `answer-text-${size}`);
};

window.__CUSTOM_SELECT_BOUND__ = window.__CUSTOM_SELECT_BOUND__ || false;

window.bindCustomSelectGlobal = function () {
    if (window.__CUSTOM_SELECT_BOUND__) return;
    window.__CUSTOM_SELECT_BOUND__ = true;

    const closeAll = () => {
        document.querySelectorAll('[data-cs-root="1"]').forEach(root => {
            root.classList.remove('cs-open');
            const m = root.__csMenuRef;
            if (m) m.classList.add('hidden');
        });
    };

    document.addEventListener('click', (e) => {
        if (e.target.closest?.('[data-cs-menu="1"]')) return;
        if (e.target.closest?.('[data-cs-root="1"]')) return;
        closeAll();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });
};

window.buildHiddenSelect = function ({ id, answerid, order, partNum, options }) {
    const select = document.createElement('select');
    select.id = id;

    select.dataset.answerid = String(answerid);
    select.dataset.questiontype = 'select';
    select.dataset.questionorder = String(order);

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

    select.addEventListener('change', () => {
        window.changeSelectGenerate?.(select, partNum);
    });

    return select;
};

window.getCustomSelectPortal = function () {
    let portal = document.getElementById('cs-portal-layer');
    if (portal) return portal;

    portal = document.createElement('div');
    portal.id = 'cs-portal-layer';
    portal.style.position = 'fixed';
    portal.style.left = '0';
    portal.style.top = '0';
    portal.style.width = '100%';
    portal.style.height = '100%';
    portal.style.zIndex = '2147483647';
    portal.style.pointerEvents = 'none';

    document.body.appendChild(portal);
    return portal;
};

window.getViewportRect = function () {
    const vv = window.visualViewport;
    const width = vv ? vv.width : window.innerWidth;
    const height = vv ? vv.height : window.innerHeight;
    const offsetLeft = vv ? vv.offsetLeft : 0;
    const offsetTop = vv ? vv.offsetTop : 0;

    return {
        left: offsetLeft,
        top: offsetTop,
        right: offsetLeft + width,
        bottom: offsetTop + height,
        width,
        height
    };
};

window.clamp = function (v, min, max) {
    return Math.max(min, Math.min(max, v));
};

window.positionPortalMenu = function (btn, menuEl, opts = {}) {
    if (!btn || !menuEl) return;

    const gap = opts.gap ?? 8;
    const offset = opts.offset ?? 8;

    // old behavior params (fallback)
    const desiredWidthMul = opts.widthMul ?? 2;
    const minWidth = opts.minWidth ?? 180;

    // ✅ yangi flag: answerDivW=2 bo'lsa right edge gacha cho'zish
    const fillRight = !!opts.fillRight;

    const btnRect = btn.getBoundingClientRect();
    const vp = window.getViewportRect();

    // ===== WIDTH + LEFT =====
    let left;
    let width;

    if (fillRight) {
        // ✅ Chap chet = select chap cheti (lekin gapdan ichkarida)
        left = window.clamp(btnRect.left, vp.left + gap, vp.right - gap);

        // ✅ width = left dan o'ng chetgacha (gap bilan)
        width = (vp.right - gap) - left;

        // agar juda kichkina bo'lib qolsa ham chapga chiqarmaymiz (minWidth ni majburlamaymiz)
        width = Math.max(0, width);
    } else {
        // eski behavior: widthMul bo'yicha
        width = Math.round(btnRect.width * desiredWidthMul);
        width = Math.max(minWidth, width);
        width = Math.min(width, vp.width - gap * 2);

        left = btnRect.left;
        left = window.clamp(left, vp.left + gap, vp.right - gap - width);
    }

    // ===== OPEN UP / DOWN + HEIGHT =====
    const spaceBelow = (vp.bottom - gap) - (btnRect.bottom + offset);
    const spaceAbove = (btnRect.top - offset) - (vp.top + gap);

    const openUp = spaceAbove > spaceBelow;
    const avail = Math.max(140, openUp ? spaceAbove : spaceBelow);

    const listEl = menuEl.__csListEl || menuEl;
    listEl.style.maxHeight = avail + 'px';
    listEl.style.overflowY = 'auto';
    listEl.style.overscrollBehavior = 'contain';
    listEl.style.webkitOverflowScrolling = 'touch';
    listEl.style.touchAction = 'pan-y';

    menuEl.style.width = width + 'px';
    menuEl.style.left = left + 'px';

    if (openUp) {
        const effectiveH = Math.min(listEl.scrollHeight || avail, avail);
        let top = (btnRect.top - offset) - effectiveH;
        top = window.clamp(top, vp.top + gap, vp.bottom - gap - effectiveH);
        menuEl.style.top = top + 'px';
    } else {
        let top = btnRect.bottom + offset;
        top = window.clamp(top, vp.top + gap, vp.bottom - gap - avail);
        menuEl.style.top = top + 'px';
    }

    menuEl.dataset.openUp = openUp ? '1' : '0';
};


window.buildCustomSelect = function ({
                                         partNum,
                                         id,
                                         answerid,
                                         order,
                                         options,
                                         placeholder = 'Select',
                                         answerDivW = 2
                                     }) {
    window.bindCustomSelectGlobal();

    const root = document.createElement('div');
    root.dataset.csRoot = '1';
    root.className = 'relative';

    const nativeSelect = window.buildHiddenSelect({ id, answerid, order, partNum, options });

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.dataset.csBtn = '1';
    btn.className = [
        'w-full rounded-xl border border-gray-300 bg-white px-3 py-2',
        'text-left flex items-center justify-between gap-3',
        'shadow-sm hover:bg-gray-50',
        'focus:outline-none focus:ring-2 focus:ring-gray-300',
        'transition'
    ].join(' ');

    const left = document.createElement('div');
    left.className = 'min-w-0 flex-1';

    const labelSpan = document.createElement('span');
    labelSpan.dataset.csLabel = '1';
    labelSpan.className = 'block whitespace-normal break-words';
    labelSpan.textContent = placeholder;
    window.applyAnswerTextSizeClass(labelSpan, window.getReadingTextSize?.() ?? 7);
    left.appendChild(labelSpan);

    const chev = document.createElement('span');
    chev.className = 'shrink-0 text-gray-500';
    chev.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
           viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6"/>
      </svg>
    `;

    btn.appendChild(left);
    btn.appendChild(chev);

    const menu = document.createElement('div');
    menu.dataset.csMenu = '1';
    menu.className = 'rounded-2xl border border-gray-200 bg-white shadow-lg hidden';
    menu.style.position = 'fixed';
    menu.style.pointerEvents = 'auto';
    menu.style.zIndex = '2147483647';

    const list = document.createElement('div');
    list.className = 'p-1';
    list.style.overflowY = 'auto';
    list.style.overscrollBehavior = 'contain';
    list.style.webkitOverflowScrolling = 'touch';
    list.style.touchAction = 'pan-y';
    menu.__csListEl = list;

    list.addEventListener('wheel', (e) => e.stopPropagation(), { passive: true });
    list.addEventListener('touchmove', (e) => e.stopPropagation(), { passive: true });

    const makeItem = (text, value, isPlaceholder = false) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = [
            'w-full text-left rounded-xl px-3 py-2',
            'hover:bg-gray-100 active:bg-gray-200 transition',
            'whitespace-normal break-words',
            isPlaceholder ? 'text-gray-500' : 'text-gray-900'
        ].join(' ');
        window.applyAnswerTextSizeClass(item, window.getReadingTextSize?.() ?? 7);
        item.textContent = text;

        item.addEventListener('click', () => {
            nativeSelect.value = String(value ?? '');
            nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
            labelSpan.textContent = (value === '' || value == null) ? placeholder : text;
            root.classList.remove('cs-open');
            menu.classList.add('hidden');
        });

        return item;
    };

    list.appendChild(makeItem(placeholder, '', true));
    (Array.isArray(options) ? options : []).forEach(opt => {
        list.appendChild(makeItem(String(opt.label ?? ''), String(opt.value ?? '')));
    });

    menu.appendChild(list);

    const portal = window.getCustomSelectPortal();
    portal.appendChild(menu);

    function closeOthers() {
        document.querySelectorAll('[data-cs-root="1"]').forEach(r => {
            if (r !== root) {
                r.classList.remove('cs-open');
                const m = r.__csMenuRef;
                if (m) m.classList.add('hidden');
            }
        });
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        closeOthers();

        const isOpen = root.classList.toggle('cs-open');

        if (isOpen) {
            menu.classList.remove('hidden');

            const mul = (typeof answerDivW === 'number' && isFinite(answerDivW) && answerDivW > 0) ? answerDivW : 2;
            window.positionPortalMenu(btn, menu, {
                gap: 8,
                offset: 8,
                widthMul: mul,
                minWidth: 180,
                fillRight: mul === 2
            });



            if (!root.__csResizeBound) {
                root.__csResizeBound = true;

                const reposition = () => {
                    if (root.classList.contains('cs-open') && !menu.classList.contains('hidden')) {
                        const mul2 = (typeof answerDivW === 'number' && isFinite(answerDivW) && answerDivW > 0) ? answerDivW : 2;

                        window.positionPortalMenu(btn, menu, {
                            gap: 8,
                            offset: 8,
                            widthMul: mul2,
                            minWidth: 180,
                            fillRight: mul2 === 2
                        });

                    }
                };

                window.addEventListener('resize', reposition);
                window.addEventListener('scroll', reposition, true);
                window.visualViewport?.addEventListener?.('resize', reposition);
                window.visualViewport?.addEventListener?.('scroll', reposition);
            }
        } else {
            menu.classList.add('hidden');
        }
    });

    root.__csMenuRef = menu;

    nativeSelect.addEventListener('change', () => {
        const opt = nativeSelect.options?.[nativeSelect.selectedIndex];
        const txt = (opt?.textContent ?? '').toString().trim();
        if (!txt || txt.toLowerCase() === 'select') labelSpan.textContent = placeholder;
        else labelSpan.textContent = txt;
    });

    root.appendChild(nativeSelect);
    root.appendChild(btn);

    return root;
};


// ✅ NEW: refresh custom select UI from underlying <select> value
window.refreshCustomSelectUI = window.refreshCustomSelectUI || function (selectEl) {
    if (!selectEl) return;

    // 1) selectning tanlangan option textini topamiz
    const opt = selectEl.options?.[selectEl.selectedIndex];
    const selectedText = (opt?.textContent ?? '').toString().trim();

    // 2) custom UI wrapperni topishga harakat
    // (turli buildlarda turlicha bo'lishi mumkin, shuning uchun bir nechta variant)
    const root =
        selectEl.closest?.('[data-custom-select]') ||
        selectEl.closest?.('.custom-select') ||
        selectEl.parentElement;

    if (!root) return;

    const labelEl =
        root.querySelector?.('[data-custom-select-label]') ||
        root.querySelector?.('.custom-select-label') ||
        root.querySelector?.('.cs-label') ||
        root.querySelector?.('button span') ||
        null;

    if (labelEl && selectedText && selectedText.toLowerCase() !== 'select') {
        labelEl.textContent = selectedText;
    }

    const v = String(selectEl.value ?? '');
    const optionBtns = root.querySelectorAll?.('[data-value], [data-option-value]');
    if (optionBtns && optionBtns.length) {
        optionBtns.forEach(btn => {
            const bv = btn.getAttribute('data-value') ?? btn.getAttribute('data-option-value');
            const active = String(bv ?? '') === v;
            btn.classList.toggle('active', active);
            btn.classList.toggle('selected', active);
        });
    }
};

window.syncAllCustomSelectLabels = function () {
    document.querySelectorAll('select[data-questiontype="select"]').forEach(sel => {
        const root = sel.closest?.('[data-cs-root="1"]');
        if (!root) return;
        sel.dispatchEvent(new Event('change', { bubbles: true }));
    });
};

window.positionCustomSelectMenu = function (root, btn, menu) {
    if (!root || !btn || !menu) return;

    const btnRect = btn.getBoundingClientRect();
    const gap = 8;
    const desiredWidth = Math.round(btnRect.width * 2);

    let left = btnRect.left;
    let width = desiredWidth;

    const overflowRight = (left + width) - (window.innerWidth - gap);
    if (overflowRight > 0) left = Math.max(gap, left - overflowRight);
    if (left < gap) left = gap;

    menu.style.width = width + 'px';

    const rootRect = root.getBoundingClientRect();
    const localLeft = left - rootRect.left;

    menu.style.left = localLeft + 'px';
    menu.style.right = 'auto';
};
