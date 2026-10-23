
const TextAreaDiv=document.getElementById('textAreaDiv')
const QuestionDiv=document.getElementById('questionDiv')

const defaultSize=3;

let currentSize=defaultSize


document.addEventListener('DOMContentLoaded', () => {
    let WritingDraft=localStorage.getItem(window.STORAGE_KEY)

    if (WritingDraft) {
        const draft = window.getWritingDraft();

        if (draft.textSize){
            currentSize=draft.textSize
            generateAllElements(currentSize)
        }
    }
})



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

    // question div dagi p lar
    const questionPs = QuestionDiv.querySelectorAll('p');
    questionPs.forEach(p => {
        p.removeAttribute('style');
        clearTextClasses(p);
        p.classList.add(className);

        // line-height ham mos bo'lsin (faqat font-size emas)
        p.style.lineHeight = `${window.getWritingLineHeightPxBySize?.(thisSize) || 20}px`;
    });

    // editor ichidagi p lar
    Object.values(window.Jodit?.instances || {}).forEach(editor => {
        const root = editor.editor;
        if (!root) return;

        // rootga ham default berib qo'yamiz (yangi p lar ham mos chiqadi)
        root.style.fontSize = `${window.getWritingFontPxBySize?.(thisSize) || 16}px`;
        root.style.lineHeight = `${window.getWritingLineHeightPxBySize?.(thisSize) || 20}px`;

        const ps = root.querySelectorAll('p');
        ps.forEach(p => {
            p.removeAttribute('style');
            clearTextClasses(p);
            p.classList.add(className);

            // har p ga line-height (aniq ko'rinish uchun)
            p.style.lineHeight = `${window.getWritingLineHeightPxBySize?.(thisSize) || 20}px`;
        });
    });

    // ✅ ENG MUHIMI: textSize o'zgarsa editor height ham moslansin
    window.updateWritingEditorsHeightByTextSizeSafe?.(thisSize);

    updateTextSizeLocalStorage(thisSize);
}



window.updateTextSizeLocalStorage = function (thisSize) {
    const draft = window.getWritingDraft();
    draft.textSize = thisSize;

    localStorage.setItem(window.STORAGE_KEY, JSON.stringify(draft));
};




