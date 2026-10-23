const QuestionDiv = document.getElementById('questionDiv');

const defaultSize = 7;
let currentSize = defaultSize;

document.addEventListener('DOMContentLoaded', () => {

    let readingDraft=localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)

    if (readingDraft) {
        const draft = window.getReadingTextSizeDraft();

        if (draft.textSize){
            currentSize=draft.textSize
        }
    }else {
        currentSize=defaultSize
    }

    generateAllElements(currentSize)


});




window.reRenderTextP = function () {
    let readingDraft=localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)

    if (readingDraft) {
        const draft = window.getReadingTextSizeDraft();

        if (draft.textSize){
            generateAllElements(draft.textSize)
        }
    }else {
        generateAllElements(defaultSize)
    }
}


window.minusText = function () {
    currentSize = Math.max(1, currentSize - 1);
    generateAllElements(currentSize);
};

window.plusText = function () {
    currentSize = Math.min(9, currentSize + 1);
    generateAllElements(currentSize);
};

function clearTextClasses(el) {
    for (let i = 1; i <= 9; i++) el.classList.remove(`text-${i}`);
}

function generateAllElements(thisSize) {
    const className = `text-${thisSize}`;

    const questionPs = QuestionDiv.querySelectorAll('p');
    const questionSpans = QuestionDiv.querySelectorAll('span');
    const answerTextEls = document.querySelectorAll('.answer-text');


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

    answerTextEls.forEach(el => {
        el.removeAttribute('style');
        clearAnswerTextClasses(el);
        el.classList.add(`answer-text-${thisSize}`);
    });

    updateTextSizeLocalStorage(thisSize);

}

window.getReadingTextSize = function () {
    return currentSize;
};


window.updateTextSizeLocalStorage = function (thisSize){
    const draft = window.getReadingTextSizeDraft();

    draft['textSize']=thisSize
    localStorage.setItem(window.STORAGE_TEXT_SIZE_KEY, JSON.stringify(draft));
}



window.getReadingTextSizeDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)) || {}; }
    catch { return {}; }
}
