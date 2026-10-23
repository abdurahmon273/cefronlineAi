
const exerciseId=document.getElementById('exercise_id').value
window.STORAGE_KEY='cefr-reading-draft'+exerciseId
window.STORAGE_TEXT_SIZE_KEY='cefr-text-size-draft'
window.STORAGE_TIME_KEY='cefr-reading-end-time-'+exerciseId

import '../camera.js'
import './textSize.js'
import './storage.js'
import './generateTest.js'
import './generateTestHelper.js'
import './custom_select.js'
import './focusQuestion.js'
import './timing.js'
import '../colorPen.js'



window.openFinishModal = function () {
    window.regenerateFinishModal(window.availableParts || []);

    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95', 'translate-y-2');
        card.classList.add('scale-100', 'translate-y-0');
    });

    // bind (safe)
    const cancelBtn = document.getElementById('finishCancelBtn');
    const acceptBtn = document.getElementById('finishAcceptBtn');

    if (cancelBtn && !cancelBtn.dataset.bound) {
        cancelBtn.dataset.bound = '1';
        cancelBtn.addEventListener('click', window.cancelFinishModal);
    }
    if (acceptBtn && !acceptBtn.dataset.bound) {
        acceptBtn.dataset.bound = '1';
        acceptBtn.addEventListener('click', window.finishTest);
    }
};

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


// --------------------------------------------------------------------------------------------------------
window.acceptFinishModal = function () {
    window.finishTest();
};

window.finishTest = function () {
    const draft = window.getReadingDraft ? window.getReadingDraft() : {};

    const input = document.getElementById('reading-draft-input');
    if (input) input.value = JSON.stringify(draft || {});

    if (window.STORAGE_KEY) {
        localStorage.removeItem(window.STORAGE_KEY);
    }

    // if (window.STORAGE_TEXT_SIZE_KEY) {
    //     localStorage.removeItem(window.STORAGE_TEXT_SIZE_KEY);
    // }

    if (window.STORAGE_TIME_KEY) {
        localStorage.removeItem(window.STORAGE_TIME_KEY);
    }

    const form = document.getElementById('reading-form');
    if (form) form.submit();
};

