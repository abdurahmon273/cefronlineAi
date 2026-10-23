// resources/js/test/listening_result/generateTest.js

document.addEventListener('DOMContentLoaded', async () => {
    // Basic Data Setup
    const partsData = window.getBackendData(); // Helper below
    if (!partsData) return;

    // Render parts
    window.renderAllParts(partsData);

    // Apply Results
    if (window.applyResultModeAll) {
        window.applyResultModeAll(partsData);
    }

    // Focus Questions (Reading Style UI)
    if (window.generateFocusQuestions) {
        window.generateFocusQuestions();
    }

    // Default Active Part
    const available = [];
    ['partOne', 'partTwo', 'partThree', 'partFour', 'partFive', 'partSix'].forEach((k, i) => {
        if (partsData[k]) available.push({ key: k, num: i + 1, panelId: getPanelId(i + 1) });
    });

    window.availableParts = available;

    // Initialize Audio Engine
    if (window.PartAudio && window.PartAudio.initPartAudios && available.length > 0) {
        // init with empty config or specific if needed
        await window.PartAudio.initPartAudios(available, partsData, { audioControl: 0 });
    }
    // Set first part active
    if (available.length > 0) {
        // Mock btnElements for setActive
        const btnEls = available.map(d => document.getElementById(getBtnId(d.num)));
        window.btnElements = btnEls;
        window.setActive(0, available, btnEls, { force: true });
    }


});

function getPanelId(num) {
    return ['partOneQuestionDiv', 'partTwoQuestionDiv', 'partThreeQuestionDiv', 'partFourQuestionDiv', 'partFiveQuestionDiv', 'partSixQuestionDiv'][num - 1];
}
function getBtnId(num) {
    // Nav generated where? Nav generation usually in generateTest.js
    // I need to generate nav!
    return ['partOneButton', 'partTwoButton', 'partThreeButton', 'partFourButton', 'partFiveButton', 'partSixButton'][num - 1];
}

window.getBackendData = function () {
    const getVal = (id) => {
        const el = document.getElementById(id);
        if (!el || !el.value) return null;
        try { return JSON.parse(el.value); } catch (e) { return null; }
    };
    return {
        partOne: getVal('part_one'),
        partTwo: getVal('part_two'),
        partThree: getVal('part_three'),
        partFour: getVal('part_four'),
        partFive: getVal('part_five'),
        partSix: getVal('part_six'),
    };
};

window.renderAllParts = function (data) {
    // Nav Generation
    const navWrap = document.getElementById('partsNav');
    if (navWrap) {
        navWrap.innerHTML = '';
        ['partOne', 'partTwo', 'partThree', 'partFour', 'partFive', 'partSix'].forEach((k, i) => {
            if (data[k]) {
                const num = i + 1;
                const btn = document.createElement('button');
                btn.id = getBtnId(num);
                btn.className = [
                    'w-16',
                    'h-10',
                    'border',
                    'rounded-xl',
                    'font-semibold',
                    'flex',
                    'items-center',
                    'justify-center',
                    'gap-1',
                    'transition',
                    'active:scale-[0.99]',
                    'part-btn hover:bg-gray-100'
                ].join(' ');
                btn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="20" height="20"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         class="shrink-0">
                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>
                        <path d="M2 10h20"/>
                    </svg>
                    <span>${num}</span>
                 `;
                btn.addEventListener('click', () => {
                    const idx = window.availableParts.findIndex(x => x.num === num);
                    if (idx !== -1) window.setActive(idx, window.availableParts, window.btnElements);
                });
                navWrap.appendChild(btn);
            }
        });
    }

    if (data.partOne) window.GeneratePartOne(data.partOne);
    if (data.partTwo) window.GeneratePartTwo(data.partTwo);
    if (data.partThree) window.GeneratePartThree(data.partThree);
    if (data.partFour) window.GeneratePartFour(data.partFour);
    if (data.partFive) window.GeneratePartFive(data.partFive);
    if (data.partSix) window.GeneratePartSix(data.partSix);
};

// Part Generators using Helpers
window.GeneratePartOne = function (d) {
    const grid = document.getElementById('partOneGrid');
    if (!grid) return;
    grid.innerHTML = '';
    (d.tests || []).forEach((t, i) => {
        grid.appendChild(window.createPartOneCard({ order: i + 1, testId: t.id, a1: t.answer_1, a2: t.answer_2, a3: t.answer_3 }));
    });
};

window.GeneratePartTwo = function (d) {
    const titleEl = document.getElementById('partTwoTitle');
    const wrap = document.getElementById('partTwoTextWrap');
    if (wrap) {
        if (titleEl) titleEl.textContent = d.title || '';
        window.renderPartTwoTextInto(wrap, d.text); // Creates inputs with data-count

        // Assign IDs
        (d.answers || []).forEach(a => {
            const inp = wrap.querySelector(`input[data-count="${a.count_number}"]`);
            if (inp) {
                inp.dataset.answerid = a.id;
                inp.id = `part-2-${a.id}-input`;
                inp.dataset.questionorder = a.count_number;
            }
        });
    }
};

window.GeneratePartThree = function (d) {
    const grid = document.getElementById('partThreeGrid');
    if (!grid) return;
    grid.innerHTML = '';
    (d.questions || []).forEach((q, i) => {
        // Extract real order from title "15. Speaker 1" -> 15
        let realOrder = window.getLeadingNumber(q.title);
        if (!realOrder) {
            realOrder = i + 1; // Fallback (incorrect but prevents crash)
        }

        grid.appendChild(window.createPartThreeCard({
            questionId: q.id,
            title: q.title || `Speaker ${i + 1}`,
            order: realOrder,
            answers: d.answers,
            letterArray: d.letter_array
        }));
    });
};

window.GeneratePartFour = function (d) {
    const list = document.getElementById('partFourList');
    if (!list) return;
    list.innerHTML = '';

    // Map Image
    const img = document.getElementById('partFourMapImg');
    if (img && d.map) {
        img.src = d.map.startsWith('http') ? d.map : '/storage/' + d.map.replace(/^public\//, '');
        img.classList.remove('hidden');
        document.getElementById('partFourMapPlaceholder')?.classList.add('hidden');
    }

    (d.questions || []).forEach((q, i) => {
        let realOrder = window.getLeadingNumber(q.title);
        if (!realOrder) realOrder = i + 1; // Fallback

        list.appendChild(window.createPartFourQuestionCard({
            questionId: q.id,
            title: q.title || `Question ${i + 1}`,
            order: realOrder,
            answers: d.answers,
            letterArray: d.letter_array
        }));
    });
};

window.GeneratePartFive = function (d) {
    const grid = document.getElementById('partFiveGrid');
    if (!grid) return;
    grid.innerHTML = '';
    (d.tests || []).forEach((t, i) => {
        // Part 5 usually starts around Q24.
        // Try extracting number from question text.
        let realOrder = window.getLeadingNumber(t.question);

        // Fallback if no number in text: 24 + i (assuming Part 4 ended at 23)
        if (!realOrder) {
            realOrder = 24 + i;
        }

        grid.appendChild(window.createPartFiveCard({
            order: realOrder,
            testId: t.id,
            question: t.question,
            a1: t.answer_1, a2: t.answer_2, a3: t.answer_3
        }));
    });
};

window.GeneratePartSix = function (d) {
    const titleEl = document.getElementById('partSixTitle');
    const wrap = document.getElementById('partSixTextWrap');
    if (wrap) {
        if (titleEl) titleEl.textContent = d.title || '';
        window.renderPartSixTextInto(wrap, d.text, d.answers);

        (d.answers || []).forEach(a => {
            const inp = wrap.querySelector(`input[data-count="${a.count_number}"]`);
            if (inp) {
                inp.dataset.answerid = a.id;
                inp.id = `part-6-${a.id}-input`;
                inp.dataset.questionorder = a.count_number;
            }
        });
    }
};


