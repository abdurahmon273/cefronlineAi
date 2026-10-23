// resources/js/test/listening/storage.js

document.addEventListener('DOMContentLoaded', () => {
    const isMock = Number(document.getElementById('is_mock')?.value || 0) === 1;

    const ListeningDraft = localStorage.getItem(window.LISTENING_STORAGE_KEY);

    // ✅ MOCK: reload/refresh bo'lsa ham draftni avtomatik tiklaymiz (alert chiqmaydi)
    if (isMock) {
        if (ListeningDraft) {
            try {
                hideDraftAlert?.();
            } catch (_) {}

            // DOM render bo'lib ulgursin deb kichik timeout
            setTimeout(() => {
                window.reGenerateStorageDataToHtml?.();
            }, 50);
        }

        // mockda accept/cancel tugmalari kerak emas
        return;
    }

    // ✅ NON-MOCK: avvalgidek
    if (ListeningDraft) {
        showDraftAlert(
            "Saved data is available. Would you like to continue where you left off?"
        );
    }

    const acceptBtn = document.getElementById("acceptDraftBtn");
    const cancelBtn = document.getElementById("cancelDraftBtn");

    if (acceptBtn) {
        acceptBtn.onclick = function () {
            hideDraftAlert();
            window.reGenerateStorageDataToHtml();
        };
    }

    if (cancelBtn) {
        cancelBtn.onclick = function () {
            localStorage.removeItem(window.LISTENING_STORAGE_KEY);
            localStorage.removeItem(window.LISTENING_TEXT_SIZE_KEY);
            localStorage.removeItem(window.LISTENING_AUDIO_DRAFT_KEY);
            hideDraftAlert();
        };
    }
});


// =============================
// GET / SET DRAFT
// =============================
window.getListeningDraft = function () {
    try {
        const raw = localStorage.getItem(window.LISTENING_STORAGE_KEY);
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch {
        return {};
    }
};

window.setListeningDraft = function (draft) {
    try {
        localStorage.setItem(
            window.LISTENING_STORAGE_KEY,
            JSON.stringify(draft || {})
        );
    } catch (_) {}
};


window.getListeningAudioDraft = function () {
    try {
        return JSON.parse(
            localStorage.getItem(window.LISTENING_AUDIO_DRAFT_KEY)
        ) || {};
    } catch {
        return {};
    }
};

window.setListeningAudioDraft = function (draft) {
    try {
        localStorage.setItem(
            window.LISTENING_AUDIO_DRAFT_KEY,
            JSON.stringify(draft || {})
        );
    } catch (_) {}
};


// =============================
// DRAFT STRUCTURE HELPERS
// =============================
window.ensureListeningPartKey = function (partNum) {
    const map = {
        1: 'part_one',
        2: 'part_two',
        3: 'part_three',
        4: 'part_four',
        5: 'part_five',
        6: 'part_six',
    };

    const key = map[partNum];
    if (!key) return null;

    const draft = window.getListeningDraft();

    draft[key] = draft[key] || {};
    draft[key].question_data = draft[key].question_data || {};

    window.setListeningDraft(draft);
    return key;
};

window.writeQuestionToDraft = function (partNum, elId, payload) {
    const partKey = window.ensureListeningPartKey(partNum);
    if (!partKey) return;

    const draft = window.getListeningDraft();

    draft[partKey].question_data[elId] = {
        order: payload?.order ?? 0,
        id: payload?.id ?? 0,
        type: payload?.type ?? 'unknown',
        value: payload?.value ?? '',
    };

    window.setListeningDraft(draft);
};


// =============================
// ✅ CHANGE HELPERS (YO‘QOLIB KETGANI QAYTARILDI)
// =============================
window.changeInputGenerate = function (inputEl, partNum) {
    if (!inputEl) return;

    const elId = inputEl.id;
    const order = Number(inputEl.dataset.questionorder || 0);
    const answerId = Number(inputEl.dataset.answerid || 0);

    window.writeQuestionToDraft(partNum, elId, {
        order,
        id: answerId,
        type: 'input',
        value: inputEl.value ?? ''
    });

    if (typeof window.generateThisOrderButton === 'function') {
        window.generateThisOrderButton(order, inputEl.value);
    }
};

window.changeSelectGenerate = function (selectEl, partNum) {
    if (!selectEl) return;

    const elId = selectEl.id;
    const order = Number(selectEl.dataset.questionorder || 0);
    const answerId = Number(selectEl.dataset.answerid || 0);

    const value = selectEl.value === '' ? '' : Number(selectEl.value);

    window.writeQuestionToDraft(partNum, elId, {
        order,
        id: answerId,
        type: 'select',
        value
    });

    if (typeof window.generateThisOrderButton === 'function') {
        window.generateThisOrderButton(order, value);
    }
};

window.changeRadioInputGenerate = function (radioEl, partNum) {
    if (!radioEl || !radioEl.checked) return;

    const containerId = radioEl.name;
    const container = document.getElementById(containerId);
    if (!container) return;

    const order = Number(container.dataset.questionorder || 0);
    const answerId = Number(container.dataset.answerid || 0);

    window.writeQuestionToDraft(partNum, containerId, {
        order,
        id: answerId,
        type: 'radio',
        value: radioEl.value
    });

    if (typeof window.generateThisOrderButton === 'function') {
        window.generateThisOrderButton(order, radioEl.value);
    }
};


// =============================
// RESTORE DRAFT → HTML
// =============================
window.reGenerateStorageDataToHtml = function () {
    const draft = window.getListeningDraft();
    if (!draft) return;

    const parts = [
        'part_one',
        'part_two',
        'part_three',
        'part_four',
        'part_five',
        'part_six'
    ];

    parts.forEach(key => {
        const qd = draft[key]?.question_data;
        if (!qd) return;

        Object.keys(qd).forEach(elId => {
            const item = qd[elId];
            const el = document.getElementById(elId);
            if (!el || !item) return;

            if (item.type === 'input') {
                el.value = item.value ?? '';
                return;
            }

            if (item.type === 'select') {
                // ✅ underlying select value set
                el.value = item.value === '' ? '' : String(item.value);

                // ✅ 1) custom select UI refresh (label/text)
                if (typeof window.refreshCustomSelectUI === 'function') {
                    window.refreshCustomSelectUI(el);
                }

                // ✅ 2) fallback: trigger change (agar custom_select change eventga bog'langan bo'lsa)
                try {
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (_) {}

                return;
            }

            if (item.type === 'radio') {
                const checked = document.querySelector(
                    `input[type="radio"][name="${CSS.escape(elId)}"][value="${CSS.escape(String(item.value))}"]`
                );
                if (checked) checked.checked = true;
                return;
            }
        });
    });

    // ✅ focus buttons update
    if (typeof window.generateAllOrderButtons === 'function') {
        window.generateAllOrderButtons(draft);
    }
};


// =============================
// COUNT ANSWERED (FOR FINISH MODAL)
// =============================
window.countAnsweredInDraftPart = function (partDraft) {
    const qd = partDraft?.question_data;
    if (!qd || typeof qd !== 'object') return 0;

    const seenOrders = new Set();
    let count = 0;

    Object.values(qd).forEach(item => {
        if (!item) return;

        const order = Number(item.order || 0);
        if (!order || seenOrders.has(order)) return;

        const value = item.value;
        const filled =
            value !== null &&
            value !== undefined &&
            String(value).trim() !== '';

        if (filled) {
            seenOrders.add(order);
            count++;
        }
    });

    return count;
};
