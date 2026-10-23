
const QuestionDiv=document.getElementById('questionDiv')

const defaultSize=5;

let currentSize=defaultSize

function clearAnswerTextClasses(el) {
    for (let i = 1; i <= 9; i++) {
        el.classList.remove(`answer-text-${i}`);
    }
}


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


})



window.reRenderTextP = function () {

    generateAllElements(currentSize)
}

window.minusText = function () {
    if (currentSize>1){
        currentSize=currentSize-1
    }else {
        currentSize=1;
    }

    generateAllElements(currentSize)
}

window.plusText = function (){
    if (currentSize<9){
       currentSize=currentSize+1
    }else {
        currentSize=9
    }

    generateAllElements(currentSize)
}

function clearTextClasses(el) {
    for (let i = 1; i <= 9; i++) {
        el.classList.remove(`text-${i}`);
    }
}

function generateAllElements(thisSize) {
    const className = `text-${thisSize}`;

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



window.updateTextSizeLocalStorage = function (thisSize){
    const draft = window.getReadingTextSizeDraft();

    draft['textSize']=thisSize
    localStorage.setItem(window.STORAGE_TEXT_SIZE_KEY, JSON.stringify(draft));
}



