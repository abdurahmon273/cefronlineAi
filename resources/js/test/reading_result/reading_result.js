
const exerciseId=document.getElementById('exercise_id').value
window.STORAGE_TEXT_SIZE_KEY='cefr-text-size-draft'



import './generateTestHelper.js';
import './generateTest.js';
import './focusQuestion.js';
import './textSize.js';

document.addEventListener('DOMContentLoaded', () => {
    const partsData = window.__READING__?.partsData || null;
    if (!partsData) return;

    window.applyResultModeAll?.(partsData);

    window.__RESULT_STATE_BY_ORDER__ = window.buildResultStateByOrder?.(partsData) || {};

    window.generateFocusQuestions?.();
    window.updateFocusButtonsByResult?.();
    window.updateFocusStatsUI?.();
});
