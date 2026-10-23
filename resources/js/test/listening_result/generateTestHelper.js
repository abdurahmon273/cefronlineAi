// resources/js/test/listening_result/generateTestHelper.js

window.escapeHtml = function (str) {
    return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
};

window.normalizeText = function (s) {
    return (s ?? '').toString().replace(/\r\n/g, '\n');
};

window.buildOrderSpan = function (order) {
    const span = document.createElement('span');
    span.className = 'font-semibold mr-2';
    span.textContent = `${order})`;
    return span;
};

// ============================================
// RESULT & FEEDBACK UI (READING STYLE)
// ============================================

window.applyResultClassToEl = function (el, status, isCorrect) {
    if (!el) return;
    el.classList.remove('border-green-500', 'border-red-500', 'border-gray-300', 'border-gray-400');
    el.classList.remove('ring-1', 'ring-green-300', 'ring-red-300', 'ring-gray-200');

    if (status !== 'answered') {
        el.classList.add('border-gray-300', 'ring-1', 'ring-gray-200');
        return;
    }
    if (Number(isCorrect) === 1) el.classList.add('border-green-500', 'ring-1', 'ring-green-300');
    else if (Number(isCorrect) === 0) el.classList.add('border-red-500', 'ring-1', 'ring-red-300');
    else el.classList.add('border-gray-300', 'ring-1', 'ring-gray-200');
};

window.removeOldFeedback = function (wrap) {
    if (!wrap) return;
    wrap.querySelectorAll('[data-feedback="1"]').forEach(el => el.remove());
};

window.buildFeedbackEl = function ({ status, is_correct, correctText, inline = false }) {
    const box = document.createElement('div');
    box.dataset.feedback = '1';

    box.className = inline
        ? 'ml-2 inline-flex flex-wrap items-center gap-2 text-[11px] sm:text-xs align-middle'
        : 'mt-2 text-xs sm:text-sm';

    const safeCorrect = window.escapeHtml(correctText ?? '');

    // NOT ANSWERED
    if (status !== 'answered') {
        box.innerHTML = `
          <span class="text-gray-500 font-semibold">Not answered</span>
          <span class="text-gray-600">|</span>
          <span class="text-gray-700 font-semibold">Correct answer:</span>
          <span class="text-green-600 font-bold">${safeCorrect}</span>
        `;
        return box;
    }

    // CORRECT
    if (Number(is_correct) === 1) {
        box.innerHTML = `<span class="text-green-600 font-bold">Correct</span>`;
        return box;
    }

    // INCORRECT
    box.innerHTML = `
      <span class="text-red-600 font-bold">Incorrect</span>
      <span class="text-gray-600">|</span>
      <span class="text-gray-700 font-semibold">Correct answer:</span>
      <span class="text-green-600 font-bold">${safeCorrect}</span>
    `;
    return box;
};


// ============================================
// GENERATORS (EXACT LISTENING STRUCTURE)
// ============================================

// --- PART 1 ---
window.createPartOneCard = function ({ order, testId, a1, a2, a3 }) {
    const wrap = document.createElement('div');
    wrap.className = 'bg-white shadow-md border-2 rounded-[26px] p-3';

    wrap.dataset.answerid = String(testId);
    wrap.dataset.questiontype = 'radio';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-1-${testId}-radio`;

    const name = `part-1-${testId}-radio`;
    wrap.innerHTML = `
        <div class="text-gray-900 font-semibold">${order}.</div>
        <div class="mt-2 text-gray-900 leading-relaxed space-y-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_1" class="mt-1 accent-blue-900" disabled>
                <p>A) ${window.escapeHtml(a1)}</p>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_2" class="mt-1 accent-blue-900" disabled>
                <p>B) ${window.escapeHtml(a2)}</p>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_3" class="mt-1 accent-blue-900" disabled>
                <p>C) ${window.escapeHtml(a3)}</p>
            </label>
        </div>
        <div class="mt-2" data-feedbackWrap="1"></div>
    `;
    return wrap;
};

// --- PART 2 ---
window.createPartTwoInput = function (countNumber) {
    // NOTE: We call our Result Input creator here to match expected call signature if generateTest uses it
    // But we need to handle arguments. generateTest calls createPartTwoInput(countNumber)
    // We need to know ID/orders to make it useful.
    // Standard generateTest uses dataset.count to map later.
    // In Result mode, we might need to map differently or just render inputs and fill later.

    const input = document.createElement('input');
    input.type = 'text';
    input.className = [
        'inline-block',
        'h-9', 'sm:h-10',
        'w-[220px]', 'sm:w-[260px]',
        'border-2', 'border-gray-400', 'rounded-lg',
        'bg-white', 'px-2',
        'text-base', 'sm:text-lg',
        'outline-none',
        'transition'
    ].join(' ');

    input.dataset.count = String(countNumber);
    input.disabled = true;
    return input;
};

window.renderPartTwoTextInto = function (wrapEl, rawText) {
    if (!wrapEl) return;

    wrapEl.innerHTML = '';
    const text = String(rawText ?? '').replace(/\r/g, '').trim();
    if (!text) return;

    const lines = text.split('\n').map(l => l.trim()).filter(Boolean);

    lines.forEach((line) => {
        const row = document.createElement('div');

        // ✅ PART 2 style: inline, center, wrap
        row.className = 'flex flex-wrap items-center gap-2';

        const parts = line.split(/(\[\d+\])/g).filter(Boolean);

        parts.forEach((chunk) => {
            const m = chunk.match(/^\[(\d+)\]$/);

            if (m) {
                const order = m[1];

                // ✅ "1)" prefix (Part 2 ga o'xshash)
                row.appendChild(window.buildOrderSpan(order));

                // ✅ input
                const input = window.createPartTwoInput(order);
                row.appendChild(input);

                // ✅ inline feedback holder (applyResultPart2 shu yerga yozadi)
                const holder = document.createElement('span');
                holder.dataset.inlineFeedbackForCount = order;
                holder.className = 'inline-flex items-center ml-2';
                row.appendChild(holder);

            } else {
                const span = document.createElement('span');
                span.textContent = chunk;
                row.appendChild(span);
            }
        });

        wrapEl.appendChild(row);
    });
};

// --- PART 3 ---
window.buildPartThreeOptionLabel = function ({ letterArray, letterKey, answerText }) {
    const txt = String(answerText ?? '').trim();
    return ` ${txt}`.trim();
};

window.createPartThreeCard = function ({ questionId, title, order, answers, letterArray }) {
    const wrap = document.createElement('div');
    wrap.className = 'bg-white shadow-md rounded-[26px] p-5 sm:p-6';
    wrap.dataset.answerid = String(questionId);
    wrap.dataset.questiontype = 'select';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-3-${questionId}-select`;

    const titleDiv = document.createElement('p');
    titleDiv.className = 'text-gray-900 font-bold text-lg';
    titleDiv.textContent = title ?? '';
    wrap.appendChild(titleDiv);

    const checkDiv = document.createElement('div');
    checkDiv.className = 'mt-4';
    wrap.appendChild(checkDiv);

    const select = document.createElement('select');
    select.className = 'w-full h-11 bg-white shadow-md rounded-xl px-3 font-semibold text-gray-900 outline-none';
    select.id = `part-3-${questionId}-select-input`;
    select.disabled = true;

    const opt0 = document.createElement('option');
    opt0.textContent = 'Select...';
    select.appendChild(opt0);



    (answers || []).forEach(a => {
        const opt = document.createElement('option');
        opt.value = String(a.letter_key);
        opt.textContent = window.buildPartThreeOptionLabel({ letterArray, letterKey: a.letter_key, answerText: a.answer });
        opt.dataset.answerid = String(a.id); // Helper
        select.appendChild(opt);
    });

    checkDiv.appendChild(select);
    select.value
    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-2';
    fbWrap.dataset.feedbackWrap = '1';
    checkDiv.appendChild(fbWrap);

    return wrap;
};

// --- PART 4 ---
window.createPartFourOptionText = function ({ letterArray, letterKey }) {
    const letters = Array.isArray(letterArray) ? letterArray : [];
    const idx = Number(letterKey);
    return String(letters[idx] ?? '').trim();
};

window.createPartFourQuestionCard = function ({ questionId, title, order, answers, letterArray }) {
    const wrap = document.createElement('div');
    wrap.className = 'bg-white shadow-md rounded-[22px] p-4 sm:p-5';
    wrap.dataset.answerid = String(questionId);
    wrap.dataset.questiontype = 'select';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-4-${questionId}-select`;

    const row = document.createElement('div');
    row.className = 'flex flex-col sm:flex-row items-center sm:items-center gap-3';
    wrap.appendChild(row);

    const left = document.createElement('p');
    left.className = 'w-full sm:w-1/2 text-gray-900 font-bold text-lg';
    left.textContent = title ?? '';
    row.appendChild(left);

    const right = document.createElement('div');
    right.className = 'w-full sm:w-1/2';
    row.appendChild(right);

    const select = document.createElement('select');
    select.className = 'w-full h-11 bg-white shadow-md rounded-xl px-3 font-semibold text-gray-900 outline-none';
    select.id = `part-4-${questionId}-select-input`;
    select.disabled = true;

    const opt0 = document.createElement('option');
    opt0.textContent = 'Select...';
    select.appendChild(opt0);

    (answers || []).forEach(a => {
        const opt = document.createElement('option');
        opt.value = String(a.letter_key);
        opt.textContent = window.createPartFourOptionText({ letterArray, letterKey: a.letter_key });
        select.appendChild(opt);
    });

    right.appendChild(select);

    const fbWrap = document.createElement('div');
    fbWrap.className = 'mt-1';
    fbWrap.dataset.feedbackWrap = '1';
    right.appendChild(fbWrap);

    return wrap;
};

// --- PART 5 ---
window.getLeadingNumber = function (text) {
    const m = String(text ?? '').match(/^(\d+)/);
    return m ? m[1] : '';
};

window.createPartFiveCard = function ({ order, testId, question, a1, a2, a3 }) {
    const wrap = document.createElement('div');
    wrap.className = 'bg-white border-2 shadow-md rounded-[26px] p-4';
    wrap.dataset.answerid = String(testId);
    wrap.dataset.questiontype = 'radio';
    wrap.dataset.questionorder = String(order);
    wrap.id = `part-5-${testId}-radio`;

    const name = `part-5-${testId}-radio`;
    wrap.innerHTML = `
        <div class="text-gray-900 font-semibold">${window.escapeHtml(question)}</div>
        <div class="mt-2 text-gray-900 leading-relaxed space-y-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_1" class="mt-1 accent-blue-900" disabled>
                <p>A) ${window.escapeHtml(a1)}</p>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_2" class="mt-1 accent-blue-900" disabled>
                <p>B) ${window.escapeHtml(a2)}</p>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="${name}" value="answer_3" class="mt-1 accent-blue-900" disabled>
                <p>C) ${window.escapeHtml(a3)}</p>
            </label>
        </div>
        <div class="mt-2" data-feedbackWrap="1"></div>
    `;
    return wrap;
};


// --- PART 6 ---
window.createPartSixInput = function (countNumber) {
    return window.createPartTwoInput(countNumber);
};

window.renderPartSixTextInto = function (wrapEl, rawText) {
    if (!wrapEl) return;

    wrapEl.innerHTML = '';
    const text = String(rawText ?? '').replace(/\r/g, '').trim();
    if (!text) return;

    const lines = text.split('\n').map(l => l.trim()).filter(Boolean);

    lines.forEach((line) => {
        const row = document.createElement('div');

        // ✅ PART 6 ham PART 2 kabi center bo'ladi
        row.className = 'flex flex-wrap items-center gap-2';

        const parts = line.split(/(\[\d+\])/g).filter(Boolean);

        parts.forEach((chunk) => {
            const m = chunk.match(/^\[(\d+)\]$/);

            if (m) {
                const order = m[1];

                // ✅ "1)" prefix
                row.appendChild(window.buildOrderSpan(order));

                // ✅ input (partSix input -> partTwo inputga teng)
                const input = window.createPartSixInput(order);
                row.appendChild(input);

                // ✅ inline feedback holder
                const holder = document.createElement('span');
                holder.dataset.inlineFeedbackForCount = order;
                holder.className = 'inline-flex items-center ml-2';
                row.appendChild(holder);

            } else {
                const span = document.createElement('span');
                span.textContent = chunk;
                row.appendChild(span);
            }
        });

        wrapEl.appendChild(row);
    });
};

// ============================================
// APPLY RESULT LOGIC
// ============================================

window.applyResultModeAll = function (partsData) {
    window.applyResultPart1(partsData.partOne);
    window.applyResultPart2(partsData.partTwo);
    window.applyResultPart3(partsData.partThree);
    window.applyResultPart4(partsData.partFour);
    window.applyResultPart5(partsData.partFive);
    window.applyResultPart6(partsData.partSix);

    window.__RESULT_STATE_BY_ORDER__ = window.buildResultStateByOrder(partsData);
    if (window.updateFocusButtonsByResult) window.updateFocusButtonsByResult();
    if (window.updateFocusStatsUI) window.updateFocusStatsUI();
};

window.applyResultPart1 = function (d) {
    (d?.tests || []).forEach(t => {
        const card = document.getElementById(`part-1-${t.id}-radio`);
        if (!card) return;

        // Prefill
        if (t.user_answer_key) {
            const r = card.querySelector(`input[value="${t.user_answer_key}"]`);
            if (r) r.checked = true;
        }

        // Feedback
        const correctText = t.correct_answer_text || t[t.correct_answer_key] || '';
        window.applyResultClassToEl(card, t.status, t.is_correct);
        const fb = card.querySelector('[data-feedbackWrap="1"]');
        if (fb) {
            window.removeOldFeedback(fb);
            fb.appendChild(window.buildFeedbackEl({ status: t.status, is_correct: t.is_correct, correctText }));
        }
    });
};

window.applyResultPart2 = function (d) {
    (d?.answers || []).forEach(a => {
        // Map count to ID? Element created by generateTest uses dataset.count
        // We need to find input by count or ID. renderPartTwoTextInto assigns ID later?
        // NO, generic render doesn't assign ID! We must assign it in generateTest.js or here.
        // Let's assume generateTest.js assigns IDs after render.

        const input = document.getElementById(`part-2-${a.id}-input`);
        if (!input) return;

        // Prefill
        input.value = a.user_answer || '';

        // Feedback
        window.applyResultClassToEl(input, a.status, a.is_correct);
        // Find holder by count or ID
        const holder = input.nextElementSibling; // Created right after input in renderPartTwoTextInto? Yes.
        // Actually in my code above: row.appendChild(input); row.appendChild(holder);

        if (holder) {
            window.removeOldFeedback(holder);
            holder.appendChild(window.buildFeedbackEl({ status: a.status, is_correct: a.is_correct, correctText: a.correct_answer, inline: true }));
        }
    });
};

window.applyResultSelectGeneric = function (partNum, d) {
    (d?.questions || []).forEach((q, i) => {
        const sel = document.getElementById(`part-${partNum}-${q.id}-select-input`);
        const card = document.getElementById(`part-${partNum}-${q.id}-select`); // Wrapper
        if (!sel || !card) return;

        const row = d?.result_rows?.[i];
        if (!row) return; // Should align with questions 1-to-1?

        // Prefill
        if (row.answer_id) sel.value = row.answer_id;

        // Feedback
        window.applyResultClassToEl(sel, row.status, row.is_correct);

        let correctText = row.correct_answer || '';
        // Logic to resolve ID to text if needed... (skipped for brevity)

        const fb = card.querySelector('[data-feedbackWrap="1"]');
        if (fb) {
            window.removeOldFeedback(fb);
            fb.appendChild(window.buildFeedbackEl({ status: row.status, is_correct: row.is_correct, correctText }));
        }
    });
};

window.applyResultPart3 = function (d) {
    (d?.questions || []).forEach((q, i) => {
        const sel = document.getElementById(`part-3-${q.id}-select-input`);
        const card = document.getElementById(`part-3-${q.id}-select`);
        if (!sel || !card) return;

        // 1️⃣ Result row ni topamiz
        let row =
            d?.result_rows?.[i] &&
            String(d.result_rows[i].question_id) === String(q.id)
                ? d.result_rows[i]
                : (d?.result_rows || []).find(r =>
                    String(r.question_id) === String(q.id)
                );

        // 2️⃣ AGAR TOPILMASA — NOT ANSWERED ROW YARATAMIZ
        if (!row) {
            row = {
                status: 'not_answered',
                is_correct: null,
                user_answer_key: null,
                answer_id: null,
                correct_answer_id: q.correct_answer_id,
                correct_answer: ''
            };
        }

        // 3️⃣ USER ANSWER (agar bo‘lsa)
        let valToSelect = '';
        if (row.user_answer_key != null) {
            valToSelect = String(row.user_answer_key);
        } else if (row.answer_id) {
            const userAnsObj = (d.answers || []).find(
                a => String(a.id) === String(row.answer_id)
            );
            if (userAnsObj && userAnsObj.letter_key != null) {
                valToSelect = String(userAnsObj.letter_key);
            }
        }

        if (valToSelect !== '') {
            sel.value = valToSelect;
        }

        // 4️⃣ CORRECT ANSWER TEXT
        let correctText = '';
        const correctId = row.correct_answer_id || q.correct_answer_id;

        if (correctId) {
            const correctAnsObj = (d.answers || []).find(
                a => String(a.id) === String(correctId)
            );

            if (correctAnsObj) {
                correctText = window.buildPartThreeOptionLabel({
                    letterArray: d.letter_array,
                    letterKey: correctAnsObj.letter_key,
                    answerText: correctAnsObj.answer
                });
            }
        }

        if (!correctText && row.correct_answer) {
            correctText = row.correct_answer;
        }

        // 5️⃣ VISUAL CLASS
        window.applyResultClassToEl(sel, row.status, row.is_correct);

        // 6️⃣ FEEDBACK — HAR DOIM
        const fb = card.querySelector('[data-feedback-wrap="1"]');
        if (fb) {
            window.removeOldFeedback(fb);
            fb.appendChild(
                window.buildFeedbackEl({
                    status: row.status,
                    is_correct: row.is_correct,
                    correctText
                })
            );
        }
    });
};


window.applyResultPart4 = function (d) {
    (d?.questions || []).forEach((q, i) => {
        const sel = document.getElementById(`part-4-${q.id}-select-input`);
        const card = document.getElementById(`part-4-${q.id}-select`);
        if (!sel || !card) return;

        // 1️⃣ Result row ni topish
        let row =
            d?.result_rows?.[i] &&
            String(d.result_rows[i].question_id) === String(q.id)
                ? d.result_rows[i]
                : (d?.result_rows || []).find(r =>
                    String(r.question_id) === String(q.id)
                );

        // 2️⃣ AGAR TOPILMASA — NOT ANSWERED YARATAMIZ
        if (!row) {
            row = {
                status: 'not_answered',
                is_correct: null,
                user_answer_key: null,
                answer_id: null,
                correct_answer_id: q.correct_answer_id,
                correct_answer: ''
            };
        }

        // 3️⃣ USER ANSWER (agar bo‘lsa)
        let valToSelect = '';
        if (row.user_answer_key != null) {
            valToSelect = String(row.user_answer_key);
        } else if (row.answer_id) {
            const userAnsObj = (d.answers || []).find(
                a => String(a.id) === String(row.answer_id)
            );
            if (userAnsObj && userAnsObj.letter_key != null) {
                valToSelect = String(userAnsObj.letter_key);
            }
        }

        if (valToSelect !== '') {
            sel.value = valToSelect;
        }

        // 4️⃣ CORRECT ANSWER TEXT
        let correctText = '';
        const correctId = row.correct_answer_id || q.correct_answer_id;

        if (correctId) {
            const correctAnsObj = (d.answers || []).find(
                a => String(a.id) === String(correctId)
            );
            if (correctAnsObj) {
                correctText = window.createPartFourOptionText({
                    letterArray: d.letter_array,
                    letterKey: correctAnsObj.letter_key
                });
            }
        }

        if (!correctText && row.correct_answer) {
            correctText = row.correct_answer;
        }

        // 5️⃣ VISUAL CLASS
        window.applyResultClassToEl(sel, row.status, row.is_correct);

        // 6️⃣ FEEDBACK — HAR DOIM
        const fb = card.querySelector('[data-feedback-wrap="1"]');
        if (fb) {
            window.removeOldFeedback(fb);
            fb.appendChild(
                window.buildFeedbackEl({
                    status: row.status,
                    is_correct: row.is_correct,
                    correctText
                })
            );
        }
    });
};


window.applyResultPart5 = function (d) {
    (d?.tests || []).forEach(t => {
        const card = document.getElementById(`part-5-${t.id}-radio`);
        if (!card) return;

        if (t.user_answer_key) {
            const r = card.querySelector(`input[value="${t.user_answer_key}"]`);
            if (r) r.checked = true;
        }

        const correctText = t.correct_answer_text || t[t.correct_answer_key] || '';
        window.applyResultClassToEl(card, t.status, t.is_correct);
        const fb = card.querySelector('[data-feedbackWrap="1"]');
        if (fb) {
            window.removeOldFeedback(fb);
            fb.appendChild(window.buildFeedbackEl({ status: t.status, is_correct: t.is_correct, correctText }));
        }
    });
};

window.applyResultPart6 = function (d) {
    (d?.answers || []).forEach(a => {
        const input = document.getElementById(`part-6-${a.id}-input`);
        if (!input) return;

        input.value = a.user_answer || '';
        window.applyResultClassToEl(input, a.status, a.is_correct);

        const holder = input.nextElementSibling;
        if (holder) {
            window.removeOldFeedback(holder);
            holder.appendChild(window.buildFeedbackEl({ status: a.status, is_correct: a.is_correct, correctText: a.correct_answer, inline: true }));
        }
    });
};

// State Builder
window.buildResultStateByOrder = function (partsData) {
    const map = {};
    document.querySelectorAll('[data-questionorder]').forEach(el => {
        const order = parseInt(el.dataset.questionorder, 10);
        if (!order) return;

        let status = 'not_answered';
        let is_correct = null;

        // Determine which element has the result class (border-green-500, etc.)
        let checkEl = el; // Default to wrapper (common for radio cards)

        const qType = el.dataset.questiontype;
        if (qType === 'input' || qType === 'select') {
            // For inputs/selects, usually the input itself has the class
            checkEl = el.querySelector('input, select') || el;
        }

        if (checkEl.classList.contains('border-green-500')) {
            map[order] = { status: 'answered', is_correct: 1 };
        } else if (checkEl.classList.contains('border-red-500')) {
            map[order] = { status: 'answered', is_correct: 0 };
        } else {
            map[order] = { status: 'not_answered', is_correct: null };
        }
    });
    return map;
};
