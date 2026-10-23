
document.addEventListener('DOMContentLoaded', () => {

    let ReadingDraft=localStorage.getItem(window.STORAGE_KEY)

    if (ReadingDraft) {
        showDraftAlert("Saved data is available. Would you like to continue where you left off?");
    }

    document.getElementById("acceptDraftBtn").onclick = function() {
        hideDraftAlert();

        reGenerateStorageDataToHtml()
    };

    document.getElementById("cancelDraftBtn").onclick = function() {
        localStorage.removeItem(window.STORAGE_KEY);
        localStorage.removeItem(window.STORAGE_TIME_KEY);
        localStorage.removeItem(window.STORAGE_TEXT_SIZE_KEY);

        // generateTimer()

        hideDraftAlert();
    };


});


// =============================
// - changeInputGenerate
// - changeSelectGenerate
// - changeRadioInputGenerate
// - reGenerateStorageDataToHtml
// =============================

window.changeInputGenerate = function (inputEl, partNum) {
    if (!inputEl) return;

    const answerId = Number(inputEl.dataset.answerid || 0);
    const order = Number(inputEl.dataset.questionorder || 0);
    const elId = inputEl.id;

    window.writeQuestionToDraft(partNum, elId, {
        order,
        id: answerId,
        type: 'input',
        value: (inputEl.value ?? '').toString()
    });

    generateThisOrderButton(order,inputEl.value)
};

window.changeSelectGenerate = function (selectEl, partNum) {
    if (!selectEl) return;

    const answerId = Number(selectEl.dataset.answerid || 0);
    const order = Number(selectEl.dataset.questionorder || 0);
    const elId = selectEl.id;

    const v = selectEl.value;
    const value = v === '' ? '' : Number(v);

    window.writeQuestionToDraft(partNum, elId, {
        order,
        id: answerId,
        type: 'select',
        value
    });
    generateThisOrderButton(order,value)

};

window.changeRadioInputGenerate = function (radioInputEl, partNum) {
    if (!radioInputEl || !radioInputEl.checked) return;

    // radio container id: part-X-ID-radio
    const containerId = radioInputEl.name;
    const container = document.getElementById(containerId);
    if (!container) return;

    const answerId = Number(container.dataset.answerid || 0);
    const order = Number(container.dataset.questionorder || 0);

    window.writeQuestionToDraft(partNum, containerId, {
        order,
        id: answerId,
        type: 'radio',
        value: radioInputEl.value
    });

    generateThisOrderButton(order,radioInputEl.value)
};

window.reGenerateStorageDataToHtml = function () {
    const draft = window.getReadingDraft();
    if (!draft) return;

    const parts = [
        { key: 'part_one', num: 1 },
        { key: 'part_two', num: 2 },
        { key: 'part_three', num: 3 },
        { key: 'part_four', num: 4 },
        { key: 'part_five', num: 5 },
    ];

    parts.forEach(p => {
        const partData = draft[p.key];
        const qd = partData?.question_data;
        if (!qd) return;

        Object.keys(qd).forEach(elId => {
            const item = qd[elId];
            const el = document.getElementById(elId);
            if (!el) return;

            if (item.type === 'input') {
                el.value = (item.value ?? '').toString();
                // optional: input event kerak bo'lsa:
                // el.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (item.type === 'select') {
                const v = item.value;
                el.value = (v === '' || v == null) ? '' : String(v);

                // ✅ custom select label sync (native select change listener ishlaydi)
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (item.type === 'radio') {
                // elId = container id (name)
                const checked = document.querySelector(
                    `input[type="radio"][name="${CSS.escape(elId)}"][value="${CSS.escape(String(item.value))}"]`
                );
                if (checked) {
                    checked.checked = true;
                    // optional: change event kerak bo'lsa:
                    // checked.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    });

    // ✅ focus number filled state ham tiklanadi
    window.generateAllOrderButtons?.(draft);
};




window.getReadingDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_KEY)) || {}; }
    catch { return {}; }
}

window.setReadingDraft = function (draft) {
    try { localStorage.setItem(window.STORAGE_KEY, JSON.stringify(draft || {})); }
    catch (_) {}
};

window.getReadingTextSizeDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)) || {}; }
    catch { return {}; }
}

