window.LISTENING_TEXT_SIZE_KEY = 'cefr-text-size-draft';

import './audio.js';
import './generateTestHelper.js';
import './generateTest.js';
import './focusQuestion.js';
import './storage.js';
import '../listening/textSize';

function safeJson(id) {
    try {
        const v = document.getElementById(id)?.value;
        if (!v) return null;
        return JSON.parse(v);
    } catch (_) {
        return null;
    }
}

function buildPartsData() {
    // IMPORTANT: keys must match audio.js: partOne..partSix
    return {
        partOne: safeJson('part_one'),
        partTwo: safeJson('part_two'),
        partThree: safeJson('part_three'),
        partFour: safeJson('part_four'),
        partFive: safeJson('part_five'),
        partSix: safeJson('part_six'),
    };
}

window.setActive = function (activeIndex, available, btnEls) {
    window.__ACTIVE_PART_INDEX__ = activeIndex;

    available.forEach((d, i) => {
        const panel = document.getElementById(d.panelId);
        if (!panel) return;
        panel.classList.toggle('hidden', i !== activeIndex);
    });

    btnEls.forEach((btn, i) => {
        const isActive = i === activeIndex;

        btn.classList.toggle('part-btn-active', isActive);
        btn.classList.toggle('part-btn', !isActive);
        btn.classList.toggle('hover:bg-gray-100', !isActive);

        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
    });

    const activeKey = available?.[activeIndex]?.key;

    // No autoplay in result
    if (window.PartAudio?.setActivePart) {
        window.PartAudio.setActivePart(activeKey, false);
    }
};

document.addEventListener('DOMContentLoaded', async () => {
    window.PartAudio?.bindControls?.();

    // generateTest.js usually sets window.availableParts (same as listening page)
    // wait one microtask for it to finish
    setTimeout(async () => {
        const availableParts = window.availableParts || [
            { key: 'partOne', panelId: 'partOneQuestionDiv' },
            { key: 'partTwo', panelId: 'partTwoQuestionDiv' },
            { key: 'partThree', panelId: 'partThreeQuestionDiv' },
            { key: 'partFour', panelId: 'partFourQuestionDiv' },
            { key: 'partFive', panelId: 'partFiveQuestionDiv' },
            { key: 'partSix', panelId: 'partSixQuestionDiv' },
        ];

        // INIT AUDIO SOURCES (this is the missing piece)
        const partsData = buildPartsData();
        await window.PartAudio?.initPartAudios?.(
            availableParts.map(p => ({ key: p.key })),
            partsData,
            { audioControl: 0 }
        );

        // Ensure active part is set (so state.activeAudio is not null)
        const idx = window.__ACTIVE_PART_INDEX__ ?? 0;
        const key = availableParts[idx]?.key || 'partOne';
        await window.PartAudio?.setActivePart?.(key, false);

        // If no one called setActive yet, show first panel
        if (typeof window.__ACTIVE_PART_INDEX__ !== 'number') {
            const partsNavBtns = Array.from(document.querySelectorAll('#partsNav button'));
            window.setActive?.(0, availableParts, partsNavBtns);
        }
    }, 0);
});
