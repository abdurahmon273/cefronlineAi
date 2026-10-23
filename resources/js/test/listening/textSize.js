const QuestionDiv = document.getElementById('questionDiv');

const defaultSize = 7;
let currentSize = defaultSize;

function clearTextClasses(el) {
    if (!el) return;
    for (let i = 1; i <= 9; i++) {
        el.classList.remove(`text-${i}`);
    }
}

function clearAnswerTextClasses(el) {
    if (!el) return;
    for (let i = 1; i <= 9; i++) {
        el.classList.remove(`answer-text-${i}`);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(window.LISTENING_TEXT_SIZE_KEY);

    if (saved) {
        const draft = window.getListeningTextSizeDraft?.() || {};
        if (draft.textSize) currentSize = draft.textSize;
        else currentSize = defaultSize;
    } else {
        currentSize = defaultSize;
    }

    generateAllElements(currentSize);
});

window.reRenderTextP = function () {
    generateAllElements(currentSize);
};

window.minusText = function () {
    if (currentSize > 1) currentSize -= 1;
    else currentSize = 1;

    generateAllElements(currentSize);
};

window.plusText = function () {
    if (currentSize < 9) currentSize += 1;
    else currentSize = 9;

    generateAllElements(currentSize);
};

function generateAllElements(thisSize) {
    const className = `text-${thisSize}`;
    if (!QuestionDiv) return;

    const questionPs = QuestionDiv.querySelectorAll('p');
    const questionSpans = QuestionDiv.querySelectorAll('span');

    questionSpans.forEach(span => {
        span.removeAttribute('style');
        clearTextClasses(span);
        span.classList.add(className);
    });

    questionPs.forEach(p => {
        p.removeAttribute('style');
        clearTextClasses(p);
        p.classList.add(className);
    });

    const answerTextEls = document.querySelectorAll('.answer-text');
    answerTextEls.forEach(el => {
        el.removeAttribute('style');
        clearAnswerTextClasses(el);
        el.classList.add(`answer-text-${thisSize}`);
    });

    updateTextSizeLocalStorage(thisSize);
}

window.updateTextSizeLocalStorage = function (value) {
    let draft = window.getListeningTextSizeDraft?.();

    if (!draft || typeof draft !== 'object') draft = {};
    draft.textSize = value;

    localStorage.setItem(window.LISTENING_TEXT_SIZE_KEY, JSON.stringify(draft));
};


window.getListeningTextSizeDraft = function () {
    try {
        return JSON.parse(
            localStorage.getItem(window.LISTENING_TEXT_SIZE_KEY)
        ) || {};
    } catch {
        return {};
    }
};
