document.addEventListener('DOMContentLoaded', () => {

    const LINE_HEIGHT_PX = 20;
    const WORKAREA_PADDING_PX = 16;

    const editors = [
        { selector: '#editor-1', lines: 10, heightExtra: 0 },
        { selector: '#editor-2', lines: 22, heightExtra: 0 },
        { selector: '#editor-3', lines: 22, heightExtra: 0 },
    ];

    const makeHeight = (lines) => (lines * LINE_HEIGHT_PX) + WORKAREA_PADDING_PX;

    const commonConfig = {
        toolbarAdaptive: false,
        toolbarSticky: false,
        askBeforePasteHTML: false,
        askBeforePasteFromWord: false,
        defaultActionOnPaste: "insert_clear_html",
        processPasteHTML: true,
        processPasteFromWord: true,
        enter: "P",
        buttons: [
            'bold',
            'italic',
            'underline',
            '|',
            'ul',
            'ol',
            '|',
            'left',
            'center',
            'right',
            'justify',
        ],

        showCharsCounter: false,
        showWordsCounter: false,
        showXPathInStatusbar: false,
        addNewLine: false,
        addNewLineOnDBLClick: false,
        disablePlugins: ['addNewLine'],
        style: {
            fontSize: '14px',
            lineHeight: '20px'
        },

        // Resize bo'lmasin
        // resize: false,
    };

    editors.forEach((item) => {
        const h = makeHeight(item.lines) + (item.heightExtra || 0);

        Jodit.make(item.selector, {
            ...commonConfig,
            height: h,
            minHeight: h,
            // maxHeight: h
        });
    });

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('button');
        if (!btn || !btn.id) return;

        const m = btn.id.match(/^(editor-\d+)-(undo|redo)$/);
        if (!m) return;

        e.preventDefault();

        const editorId = m[1];     // "editor-1"
        const action = m[2];       // "undo" | "redo"

        const ed = Jodit.instances[editorId];
        if (!ed) return;

        // Jodit command orqali (eng ishonchli)
        ed.execCommand(action);
    });

});



window.WRITING_EDITORS_META = [
    { id: 'editor-1', lines: 10 },
    { id: 'editor-2', lines: 22 },
    { id: 'editor-3', lines: 22 },
];

// textSize 1..9 => font px (sizning css text-1..text-9 bilan mos)
window.getWritingFontPxBySize = function (size) {
    const map = {
        1: 12,
        2: 14,
        3: 16,
        4: 18,
        5: 20,
        6: 22,
        7: 24,
        8: 26,
        9: 28,
    };
    return map[size] || 16;
};

// line-height px: font kattalashsa line-height ham kattalashsin
window.getWritingLineHeightPxBySize = function (size) {
    const font = window.getWritingFontPxBySize(size);

    // 16px => 20px (sizning default config)
    // kattaroq bo'lsa proporsional: font + 4
    // kichik bo'lsa ham minimum 16
    return Math.max(16, font + 4);
};

// Jodit container/workplace heightni set qilish
window.setJoditHeightPx = function (ed, heightPx) {
    if (!ed || !ed.container) return;

    // outer container
    ed.container.style.height = `${heightPx}px`;
    ed.container.style.minHeight = `${heightPx}px`;

    // workplace (inside)
    const workplace = ed.container.querySelector('.jodit-workplace');
    if (workplace) {
        workplace.style.height = `${heightPx}px`;
        workplace.style.minHeight = `${heightPx}px`;
    }
};

// textSize ga qarab hamma editor heightlarini qayta hisoblash
window.updateWritingEditorsHeightByTextSize = function (size) {
    const lineHeightPx = window.getWritingLineHeightPxBySize(size);

    // padding: sizda WORKAREA_PADDING_PX = 16 bor edi
    const WORKAREA_PADDING_PX = 16;

    window.WRITING_EDITORS_META.forEach(({ id, lines }) => {
        const ed = window.Jodit?.instances?.[id];
        if (!ed) return;

        // editor rootda ham line-height ni set qilamiz (qatorlar bir xil bo'lishi uchun)
        try {
            if (ed.editor) {
                ed.editor.style.lineHeight = `${lineHeightPx}px`;
                ed.editor.style.fontSize = `${window.getWritingFontPxBySize(size)}px`;
            }
        } catch (_) {}

        const heightPx = (lines * lineHeightPx) + WORKAREA_PADDING_PX;
        window.setJoditHeightPx(ed, heightPx);
    });
};

// Jodit hali init bo'lmagan bo'lsa kutib turib qo'llash
window.updateWritingEditorsHeightByTextSizeSafe = function (size, timeoutMs = 5000) {
    const start = Date.now();
    const timer = setInterval(() => {
        const ok =
            window.Jodit?.instances?.['editor-1'] ||
            window.Jodit?.instances?.['editor-2'] ||
            window.Jodit?.instances?.['editor-3'];

        if (ok) {
            clearInterval(timer);
            window.updateWritingEditorsHeightByTextSize(size);
            return;
        }

        if (Date.now() - start > timeoutMs) {
            clearInterval(timer);
        }
    }, 120);
};


// HTML -> plain text (Jodit value html bo‘ladi)
window.writingHtmlToText = function (html) {
    if (!html) return '';
    const div = document.createElement('div');
    div.innerHTML = String(html);

    // line breaklar ham so‘z ajratishda yordam beradi
    const text = (div.textContent || div.innerText || '');
    return text;
};

// so‘z soni: bo‘sh joylarni tozalab hisoblaydi
window.countWords = function (text) {
    const t = String(text || '')
        .replace(/\u00A0/g, ' ')      // nbsp
        .replace(/\s+/g, ' ')        // multi spaces
        .trim();

    if (!t) return 0;
    return t.split(' ').length;
};

// UI ga count yozish
window.updateWordCountUI = function (editorId) {
    const elId =
        editorId === 'editor-1' ? 'count-1' :
            editorId === 'editor-2' ? 'count-2' :
                editorId === 'editor-3' ? 'count-3' : null;

    if (!elId) return;

    const countEl = document.getElementById(elId);
    if (!countEl) return;

    // editor hidden bo‘lsa (task yo‘q) hisoblamaymiz
    const ta = document.getElementById(editorId);
    const card = ta?.closest('.bg-white.border-2');
    if (card && card.classList.contains('hidden')) return;

    const ed = window.Jodit?.instances?.[editorId];
    const html = ed ? ed.value : (ta?.value || '');

    const plain = window.writingHtmlToText(html);
    const words = window.countWords(plain);

    countEl.textContent = String(words);
};

// hammasini bir martada update qilish
window.updateAllWordCounts = function () {
    ['editor-1','editor-2','editor-3'].forEach((id) => window.updateWordCountUI(id));
};

/**
 * HTML -> plain text with \n (paragraph/linebreak friendly)
 * Jo'natishda "<p>..</p>" lar "\n" bo'lib ketadi.
 */
window.writingHtmlToPlainTextWithNewlines = function (html) {
    const div = document.createElement('div');
    div.innerHTML = String(html || '');

    // br => \n
    div.querySelectorAll('br').forEach((br) => br.replaceWith('\n'));

    // p, div, li lar tugaganda \n qo'shamiz
    const blockTags = div.querySelectorAll('p, div, li');
    blockTags.forEach((el) => {
        // oxiriga newline
        el.appendChild(document.createTextNode('\n'));
    });

    // textContent ni olamiz
    let text = (div.textContent || '').replace(/\u00A0/g, ' ');

    // ortiqcha \n larni tartiblaymiz
    text = text
        .replace(/\r/g, '')
        .replace(/[ \t]+\n/g, '\n')      // newline oldidan space
        .replace(/\n{3,}/g, '\n\n')      // 3+ newline => 2 newline
        .trim();

    return text;
};
