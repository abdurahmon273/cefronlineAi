
document.addEventListener('DOMContentLoaded', () => {

    let WritingDraft=localStorage.getItem(window.STORAGE_KEY)

    if (WritingDraft) {
        showDraftAlert("Saved data is available. Would you like to continue where you left off?");
    }

    document.getElementById("acceptDraftBtn").onclick = function() {
        hideDraftAlert();
        fillAllEditors();

        window.updateAllWordCounts?.();
    };


    document.getElementById("cancelDraftBtn").onclick = function() {
        try { localStorage.removeItem(window.STORAGE_KEY); } catch (e) {}
        try { localStorage.removeItem(window.STORAGE_TIME_KEY); } catch (e) {}

        window.clearAllWritingEditors?.();

        window.resetWritingTimer?.();

        hideDraftAlert();
    };

    const STORAGE_KEY = window.STORAGE_KEY;
    const readDraft = () => {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; }
        catch { return {}; }
    };

    const writeDraft = (obj) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(obj));
    };

    let saveTimer = null;
    const updateDraftKey = (editorId, html) => {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => {
            const draft = readDraft();
            draft[editorId] = html;
            if(html=='<p><br></p>') return
            console.log('-',html,'-jodid')
            draft.updatedAt = Date.now();
            writeDraft(draft);
        }, 600);
    };

    ['editor-1','editor-2','editor-3'].forEach((id) => {
        const ed = window.Jodit?.instances?.[id];
        if (!ed) return;

        // page load bo‘lganda ham count chiqsin
        window.updateWordCountUI(id);

        // typing / paste / delete bo‘lganda
        const onAnyChange = () => {
            updateDraftKey(id, ed.value);
            window.updateWordCountUI(id);
        };

        ed.events.on('change', onAnyChange);
        ed.events.on('keyup', onAnyChange);
    });

});

window.WRITING_PARTS_CONFIG = [
    { id: 'editor-1', btn: 'part-1-1-button', label: 'Part 1.1', max: 50 },
    { id: 'editor-2', btn: 'part-1-2-button', label: 'Part 1.2', max: 120 },
    { id: 'editor-3', btn: 'part-2-button',   label: 'Part 2',   max: 180 },
];

window.getWritingPartStatus = function (written, max) {
    if (!written || written <= 0) return { key: 'empty',    text: 'Empty',    badge: 'bg-gray-200 text-gray-700' };
    if (written >= max)           return { key: 'done',     text: 'Done',     badge: 'bg-green-100 text-green-700' };
    return                         { key: 'progress', text: 'Progress', badge: 'bg-yellow-100 text-yellow-800' };
};

window.isWritingPartVisible = function (editorId) {
    const ta = document.getElementById(editorId);
    const card = ta?.closest('.bg-white.border-2');
    if (!card) return true;
    return !card.classList.contains('hidden');
};


window.fillAllEditors = function () {
    const draft = window.getWritingDraft();

    ['editor-1','editor-2','editor-3'].forEach((id) => {
        const ed = window.Jodit?.instances?.[id];
        if (!ed) return;

        ed.value = draft[id] || '';

        // editor qiymati qo‘yilgandan keyin count ham yangilansin
        window.updateWordCountUI(id);
    });
};

window.renderWritingFinishModal = function () {
    const listEl = document.getElementById('finishPartsList');
    const totalBadge = document.getElementById('finishTotalBadge');
    if (!listEl) return;

    listEl.innerHTML = '';

    let doneCount = 0;
    let totalCount = 0;

    window.WRITING_PARTS_CONFIG.forEach((p) => {
        if (!window.isWritingPartVisible(p.id)) return;

        totalCount++;

        const countElId =
            p.id === 'editor-1' ? 'count-1' :
                p.id === 'editor-2' ? 'count-2' :
                    p.id === 'editor-3' ? 'count-3' : null;

        const written = countElId ? parseInt(document.getElementById(countElId)?.textContent || '0', 10) : 0;
        const st = window.getWritingPartStatus(written, p.max);
        if (st.key === 'done') doneCount++;

        const row = document.createElement('div');
        row.className = 'border border-gray-200 rounded-2xl px-4 py-4 flex items-center justify-between gap-4';

        row.innerHTML = `
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <span class="text-base font-extrabold text-gray-900">${p.label}</span>
                    <span class="text-xs font-extrabold px-3 py-1 rounded-full ${st.badge}">
                        ${st.text}
                    </span>
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-600">
                    ${written}/${p.max} words
                </div>
            </div>

            <button type="button"
                    data-focus-editor="${p.id}"
                    class="shrink-0 px-4 py-2 rounded-xl border border-gray-200 text-sm font-extrabold text-gray-700 hover:bg-gray-50">
                Go
            </button>
        `;

        listEl.appendChild(row);
    });

    if (totalBadge) totalBadge.textContent = `${doneCount}/${totalCount} Done`;

    // Go => modal yopib o‘sha partga focus + scroll
    listEl.querySelectorAll('button[data-focus-editor]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const editorId = btn.getAttribute('data-focus-editor');
            if (!editorId) return;

            window.hideFinishModal?.();

            const card = document.getElementById(editorId)?.closest('.bg-white.border-2');
            const container = document.getElementById('textAreaDiv');

            if (container && card) {
                const containerRect = container.getBoundingClientRect();
                const childRect = card.getBoundingClientRect();
                const deltaTop  = childRect.top - containerRect.top;
                const targetTop = container.scrollTop + deltaTop - 10;
                container.scrollTo({ top: targetTop, behavior: 'smooth' });
            }

            const ed = window.Jodit?.instances?.[editorId];
            ed?.focus?.();
        });
    });
};


window.showFinishModal = function () {
    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    // counts yangilab olamiz
    window.updateAllWordCounts?.();

    // parts list render
    window.renderWritingFinishModal?.();

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');

        card.classList.remove('scale-95', 'translate-y-2');
        card.classList.add('scale-100', 'translate-y-0');
    });
};

window.hideFinishModal = function () {
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
    }, 180);
};


window.clearAllWritingEditors = function () {
    ['editor-1', 'editor-2', 'editor-3'].forEach((id) => {
        const ed = window.Jodit?.instances?.[id];
        if (ed) ed.value = '';
        const ta = document.getElementById(id);
        if (ta) ta.value = '';
    });

    // word countlarni ham 0 qilib qo‘yamiz
    const c1 = document.getElementById('count-1');
    const c2 = document.getElementById('count-2');
    const c3 = document.getElementById('count-3');
    if (c1) c1.textContent = '0';
    if (c2) c2.textContent = '0';
    if (c3) c3.textContent = '0';
};


// Modal buttons bind
document.addEventListener('DOMContentLoaded', () => {
    // ✅ With AI default: tanlangan (1)
    const withAiToggle = document.getElementById('finishWithAiToggle');
    if (withAiToggle) withAiToggle.checked = true;
    const withAiInput = document.getElementById('writing-with-ai-input');
    if (withAiInput) withAiInput.value = '1';

    document.getElementById('finishCancelBtn')?.addEventListener('click', () => window.hideFinishModal());
    document.getElementById('finishCloseX')?.addEventListener('click', () => window.hideFinishModal());

    // backdrop click close
    document.getElementById('finishModal')?.addEventListener('click', (e) => {
        if (e.target && e.target.id === 'finishModal') window.hideFinishModal();
    });

    document.getElementById('finishAcceptBtn')?.addEventListener('click', () => {
        window.acceptFinishModal?.();
    });
});



window.acceptFinishModal = function () {
    // With AI toggle (footer)
    const withAi = document.getElementById('finishWithAiToggle')?.checked ? 1 : 0;

    const keyByEditor = {
        'editor-1': 'part-1-1',
        'editor-2': 'part-1-2',
        'editor-3': 'part-2',
    };

    const payload = {
        exercise_id: document.getElementById('exercise_id')?.value || null,
        with_ai: withAi,
        updatedAt: Date.now(),
        parts: {},
        counts: {}
    };

    window.WRITING_PARTS_CONFIG.forEach((p) => {
        if (!window.isWritingPartVisible(p.id)) return;

        const partKey = keyByEditor[p.id] || p.id;

        const ed = window.Jodit?.instances?.[p.id];
        const html = ed ? ed.value : '';

        const plainText = window.writingHtmlToPlainTextWithNewlines
            ? window.writingHtmlToPlainTextWithNewlines(html)
            : (window.writingHtmlToText ? window.writingHtmlToText(html) : '');

        payload.parts[partKey] = plainText;

        const countElId =
            p.id === 'editor-1' ? 'count-1' :
                p.id === 'editor-2' ? 'count-2' :
                    p.id === 'editor-3' ? 'count-3' : null;

        const written = countElId ? parseInt(document.getElementById(countElId)?.textContent || '0', 10) : 0;
        payload.counts[partKey] = written;
    });

    const draftInput = document.getElementById('writing-draft-input');
    const aiInput    = document.getElementById('writing-with-ai-input');
    const form       = document.getElementById('writing-form');

    if (!draftInput || !aiInput || !form) return;

    draftInput.value = JSON.stringify(payload);
    aiInput.value = String(withAi);

    try { localStorage.removeItem(window.STORAGE_KEY); } catch (e) {}
    try { localStorage.removeItem(window.STORAGE_TIME_KEY); } catch (e) {}

    form.submit();
};




window.getWritingDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_KEY)) || {}; }
    catch { return {}; }
}

window.openFinishModal =function (){
    showFinishModal()
}
