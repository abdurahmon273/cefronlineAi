// ====== helpers ======

window.resolveAssetUrl = function (url) {
    const u = (url ?? '').toString().trim();
    if (!u) return '';
    if (u.startsWith('http://') || u.startsWith('https://')) return u;
    return u;
};

window.getNextSpeakingGlobalOrderStart = function (fallbackStart = 1) {
    const nodes = document.querySelectorAll('[data-questionorder]');
    let maxOrder = 0;
    nodes.forEach(el => {
        const n = parseInt(el.dataset.questionorder || '0', 10);
        if (Number.isFinite(n) && n > maxOrder) maxOrder = n;
    });
    return maxOrder > 0 ? (maxOrder + 1) : fallbackStart;
};

window.normalizeText = function (s) {
    return (s ?? '').toString().replace(/\r\n/g, '\n');
};

window.splitLinesToBullets = function (text) {
    const t = window.normalizeText(text).trim();
    if (!t) return [];
    return t.split('\n').map(x => x.trim()).filter(Boolean);
};

window.parseForAgainst = function (raw) {
    const lines = window.splitLinesToBullets(raw);
    const out = { forList: [], againstList: [] };

    let mode = null;
    lines.forEach(line => {
        const up = line.toUpperCase();

        if (up === 'FOR') { mode = 'for'; return; }
        if (up === 'AGAINST') { mode = 'against'; return; }

        const clean = line.replace(/^[•\-\*\u2022]\s*/g, '').trim();
        if (!clean) return;

        if (mode === 'for') out.forList.push(clean);
        else if (mode === 'against') out.againstList.push(clean);
        else out.forList.push(clean);
    });

    return out;
};

window.buildForAgainstColumn = function (title, items) {
    const col = document.createElement('div');
    col.className = 'flex-1 p-6';

    const h3 = document.createElement('p');
    h3.className = ' font-bold text-gray-900 mb-4';
    h3.textContent = title;

    const ul = document.createElement('ul');
    ul.className = 'list-disc pl-5 space-y-3  text-gray-800';

    (Array.isArray(items) ? items : []).forEach(t => {
        const li = document.createElement('li');
        const p = document.createElement('p');
        p.innerText=t;
        li.appendChild(p);
        ul.appendChild(li);
    });

    col.appendChild(h3);
    col.appendChild(ul);

    return col;
};

// ====== SHOW/HIDE (✅ faqat 1 savol ko‘rinishi uchun) ======

window.getAllSpeakingQuestionNodes = function () {
    return Array.from(document.querySelectorAll('[data-questiontype="speaking"][data-questionorder]'));
};

window.hideAllSpeakingQuestions = function () {
    const nodes = window.getAllSpeakingQuestionNodes();
    nodes.forEach(el => el.classList.add('hidden'));
};

window.showOnlyThisSpeakingQuestion = function (el) {
    if (!el) return;
    window.hideAllSpeakingQuestions();
    el.classList.remove('hidden');
};

// ====== PART 1 block ======

window.buildSpeakingPart1QuestionBlock = function ({
                                                       order,
                                                       questionId,
                                                       question,
                                                       audioLink,
                                                       recordTime,
                                                       waitTime,
                                                       images,
                                                       part1Set // ✅ "first" | "second"
                                                   }) {
    const box = document.createElement('div');

    box.className = `question_${order} py-2`;
    box.dataset.questionorder = String(order);
    box.dataset.questiontype = 'speaking';
    box.dataset.partnum = '1';
    box.dataset.part1set = String(part1Set || 'one_first'); // ✅ qo‘shildi
    box.dataset.questionid = String(questionId || 0);
    box.dataset.recordtime = String(recordTime || 0);
    box.dataset.waittime = String(waitTime || 0);
    box.id = `sp-q-1-${questionId || order}`;

    const h2 = document.createElement('p');
    h2.className = 'text-center font-bold text-gray-900';
    h2.textContent = (question ?? '').toString();
    box.appendChild(h2);

    const imgs = Array.isArray(images) ? images : [];
    if (imgs.length > 0) {
        const imgWrapOuter = document.createElement('div');
        imgWrapOuter.className = 'flex-1 p-4 sm:p-6 overflow-y-auto';

        const imgWrap = document.createElement('div');
        imgWrap.className = 'flex gap-6 justify-center flex-wrap';

        imgs.slice(0, 2).forEach((img) => {
            const el = document.createElement('img');
            el.src = window.resolveAssetUrl(img?.url ?? '');
            el.alt = 'Speaking image';
            el.className = 'w-64 h-48 object-cover rounded shadow-sm border border-gray-100';
            imgWrap.appendChild(el);
        });

        imgWrapOuter.appendChild(imgWrap);
        box.appendChild(imgWrapOuter);
    }

    // Audio hidden
    const a = (audioLink ?? '').toString().trim();
    const audioWrap = document.createElement('div');
    audioWrap.className = 'hidden';
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.className = 'w-full';
    audio.src = a;
    audioWrap.appendChild(audio);
    box.appendChild(audioWrap);

    return box;
};

// ====== PART 2 block ======

window.buildSpeakingPart2Block = function ({ globalOrder, questionId, questionText, audioLink, recordTime, waitTime, imageUrl }) {
    const card = document.createElement('div');
    card.className = 'rounded-lg bg-white flex flex-col h-full';

    card.dataset.questionorder = String(globalOrder);
    card.dataset.questiontype = 'speaking';
    card.dataset.partnum = '2';
    card.dataset.questionid = String(questionId || 0);
    card.dataset.recordtime = String(recordTime || 0);
    card.dataset.waittime = String(waitTime || 0);
    card.id = `sp-q-2-${questionId || globalOrder}`;

    const top = document.createElement('div');
    top.className = 'p-6';

    const items = window.splitLinesToBullets(questionText);


    if (items.length > 1) {
        const ul = document.createElement('ul');
        ul.className = 'list-none space-y-2  text-gray-800 ';
        items.slice(0).forEach(t => {
            const li = document.createElement('li');
            const p = document.createElement('p');
            p.innerText=t
            li.appendChild(p) ;
            ul.appendChild(li);
        });
        top.appendChild(ul);
    }

    const audioWrap = document.createElement('div');
    audioWrap.className = 'hidden';
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.className = 'w-full';
    audio.src = (audioLink ?? '').toString().trim();
    audioWrap.appendChild(audio);
    top.appendChild(audioWrap);

    // ✅ image TOP (center emas)
    const imgSection = document.createElement('div');
    imgSection.className = 'flex-1 p-6 flex flex-col items-center justify-start';

    const imgSrc = window.resolveAssetUrl(imageUrl || '');
    if (imgSrc) {
        const img = document.createElement('img');
        img.alt = 'Speaking Part 2 image';
        img.src = imgSrc;
        img.className = 'max-h-[320px] w-auto rounded-lg shadow-sm border border-gray-100 object-contain';
        imgSection.appendChild(img);
    } else {
        imgSection.className = 'hidden';
    }

    card.appendChild(top);
    card.appendChild(imgSection);

    return card;
};

// ====== PART 3 block ======

window.buildSpeakingPart3Block = function ({ globalOrder, partNum, questionId, theme, questionText, audioLink, waitTime, time }) {
    const root = document.createElement('div');
    root.className = 'rounded-lg bg-white flex flex-col overflow-hidden';

    root.dataset.questionorder = String(globalOrder);
    root.dataset.questiontype = 'speaking';
    root.dataset.partnum = String(partNum || 3);
    root.dataset.questionid = String(questionId || 0);
    root.dataset.recordtime = String(time || 0);
    root.dataset.waittime = String(waitTime || 0);
    root.id = `sp-q-3-${questionId || globalOrder}`;

    const head = document.createElement('div');
    head.className = 'bg-white border-b border-gray-300 p-4 text-center';

    const h2 = document.createElement('p');
    h2.className = ' font-bold text-gray-900';
    h2.textContent = (theme ?? '').toString();
    head.appendChild(h2);

    // ✅ Audio hidden
    const audioWrap = document.createElement('div');
    audioWrap.className = 'hidden';
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.className = 'w-full';
    audio.src = (audioLink ?? '').toString().trim();
    audioWrap.appendChild(audio);
    head.appendChild(audioWrap);

    const body = document.createElement('div');
    body.className = 'flex flex-1 flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-gray-300';

    const parsed = window.parseForAgainst(questionText);
    body.appendChild(window.buildForAgainstColumn('For', parsed.forList));
    body.appendChild(window.buildForAgainstColumn('Against', parsed.againstList));

    root.appendChild(head);
    root.appendChild(body);

    return root;
};

// ====== QUEUE BUILDER ======

window.buildSpeakingQueueFromDOM = function () {
    const nodes = window.getAllSpeakingQuestionNodes();

    nodes.sort((a, b) => {
        const oa = parseInt(a.dataset.questionorder || '0', 10);
        const ob = parseInt(b.dataset.questionorder || '0', 10);
        return oa - ob;
    });

    // ✅ shared controls (Part1/Part2) oldindan yaratib qo‘yamiz
    window.ensureSpeakingSharedControls?.();

    const part1Controls = document.querySelector('#spPart1ControlsHost [data-sp-controls="1"]');
    const part2Controls = document.querySelector('#spPart2ControlsHost [data-sp-controls="1"]');

    const queue = nodes.map((el, i) => {
        const partNum = Number(el.dataset.partnum || 0);
        const panelId = partNum === 1 ? 'spPart1Container' : (partNum === 2 ? 'spPart2Container' : 'spPart3Container');
        const audioEl = el.querySelector('audio');
        const order = Number(el.dataset.questionorder || (i + 1));

        // ✅ controls: part1/2 shared host, part3 esa o‘zi (sibling)
        let controlsEl = null;

        if (partNum === 1) controlsEl = part1Controls;
        else if (partNum === 2) controlsEl = part2Controls;
        else {
            controlsEl = document.querySelector(`#partThreeQuestionDiv [data-sp-controls="1"][data-target-order="${order}"]`);
        }

        const part1Set = (el.dataset.part1set || ''); // "one_first" yoki "one_second"

        return {
            seq: i + 1,
            globalOrder: order,
            partNum,
            part1Set,
            partSub: part1Set, // ✅ ADD THIS (storage.js shu qiymatni ishlatadi)
            panelId,
            el,
            questionId: Number(el.dataset.questionid || 0),
            waitTime: Number(el.dataset.waittime || 0),
            recordTime: Number(el.dataset.recordtime || 0),
            audioEl,
            audioLink: (audioEl?.src || ''),
            controlsEl,
        };
    });

    window.__SPEAKING__ = window.__SPEAKING__ || {};
    window.__SPEAKING__.queue = queue;

    return queue;
};

// ====== CONTROLS (Start / Next) ======

window.createSpeakingControls = function ({ targetOrder } = {}) {
    const wrap = document.createElement('div');
    wrap.dataset.spControls = '1';
    wrap.dataset.targetOrder = String(targetOrder || 0);

    wrap.className = [
        'flex',
        'items-center',
        'justify-center',
        'gap-3',
        'select-none'
    ].join(' ');

    const startBtn = document.createElement('button');
    startBtn.type = 'button';
    startBtn.className = 'primary-btn px-4 py-2 rounded-lg font-semibold text-sm hidden';
    startBtn.textContent = 'Start';
    startBtn.dataset.spAction = 'start';

    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'primary-btn px-4 py-2 rounded-lg font-semibold text-sm hidden';
    nextBtn.textContent = 'Next Question';
    nextBtn.dataset.spAction = 'next';

    const finishBtn = document.createElement('button');
    finishBtn.type = 'button';
    finishBtn.className = 'primary-btn px-4 py-2 rounded-lg font-semibold text-sm hidden';
    finishBtn.textContent = 'Finish Test';
    finishBtn.dataset.spAction = 'finish';

    const onClick = (e) => {
        const action = e.currentTarget?.dataset?.spAction;
        const order = parseInt(wrap.dataset.targetOrder || '0', 10);
        if (!order || !action) return;

        if (action === 'start') window.speakingControlStartByOrder?.(order);
        if (action === 'next') window.speakingControlNextByOrder?.(order);
        if (action === 'finish') window.speakingControlFinishByOrder?.(order);
    };

    startBtn.addEventListener('click', onClick);
    nextBtn.addEventListener('click', onClick);
    finishBtn.addEventListener('click', onClick);

    wrap.appendChild(startBtn);
    wrap.appendChild(nextBtn);
    wrap.appendChild(finishBtn);

    return wrap;
};
// ✅ NEW: sleep helper
window.sleepMs = function (ms) {
    return new Promise(res => setTimeout(res, Math.max(0, Number(ms || 0))));
};

// ✅ NEW: lock Next/Finish buttons for N ms (UI only)
window.lockSpeakingNextButtonsForMs = function (controlsEl, ms = 2000) {
    if (!controlsEl) return;

    const nextBtn  = controlsEl.querySelector('button[data-sp-action="next"]');
    const finBtn   = controlsEl.querySelector('button[data-sp-action="finish"]');

    const lockBtn = (btn) => {
        if (!btn) return;
        btn.disabled = true;

        // ✅ 2 sekund "bold" + ko‘zga tashlanadigan holat
        btn.classList.add('opacity-80', 'cursor-not-allowed', 'font-extrabold', 'animate-pulse');
    };

    const unlockBtn = (btn) => {
        if (!btn) return;
        btn.disabled = false;

        btn.classList.remove('opacity-80', 'cursor-not-allowed', 'animate-pulse');
        // font-extrabold qolsin desangiz remove qilmang. Hozir 2 sekunddan keyin qaytarib olamiz:
        btn.classList.remove('font-extrabold');
    };

    lockBtn(nextBtn);
    lockBtn(finBtn);

    setTimeout(() => {
        unlockBtn(nextBtn);
        unlockBtn(finBtn);
    }, Math.max(0, Number(ms || 0)));
};

window.setSpeakingControlsMode = function (controlsEl, mode) {
    if (!controlsEl) return;

    const startBtn = controlsEl.querySelector('button[data-sp-action="start"]');
    const nextBtn  = controlsEl.querySelector('button[data-sp-action="next"]');
    const finBtn   = controlsEl.querySelector('button[data-sp-action="finish"]');

    if (startBtn) startBtn.classList.add('hidden');
    if (nextBtn) nextBtn.classList.add('hidden');
    if (finBtn)  finBtn.classList.add('hidden');

    if (mode === 'wait') {
        if (startBtn) startBtn.classList.remove('hidden');
    } else if (mode === 'record') {
        if (nextBtn) nextBtn.classList.remove('hidden');
    } else if (mode === 'finish') {
        if (finBtn) finBtn.classList.remove('hidden');
    }
};

window.getSpeakingControlsHostEl = function (partNum) {
    if (Number(partNum) === 1) return document.getElementById('spPart1ControlsHost');
    if (Number(partNum) === 2) return document.getElementById('spPart2ControlsHost');
    return null;
};

window.ensureSpeakingSharedControlsForPart = function (partNum) {
    const host = window.getSpeakingControlsHostEl(partNum);
    if (!host) return null;

    // allaqachon yaratilgan bo‘lsa
    const exist = host.querySelector('[data-sp-controls="1"]');
    if (exist) return exist;

    host.innerHTML = '';
    const controls = window.createSpeakingControls({ targetOrder: 0, layout: partNum === 1 ? 'p1' : 'p2' });
    host.appendChild(controls);
    return controls;
};

window.ensureSpeakingSharedControls = function () {
    // Part1/Part2 shared controls yaratib qo‘yamiz
    window.ensureSpeakingSharedControlsForPart(1);
    window.ensureSpeakingSharedControlsForPart(2);
};

window.setSpeakingControlsTargetOrder = function (controlsEl, order) {
    if (!controlsEl) return;
    controlsEl.dataset.targetOrder = String(order || 0);
};

window.updatePartOneTitle = function (item) {
    const el = document.getElementById('part-one-title-div');
    if (!el) return;

    // default PART 1.1
    const set = (item?.part1Set || item?.partSub || 'one_first').toString();
    const suffix = (set === 'one_second') ? '1.2' : '1.1';

    el.textContent = `PART ${suffix}`;
};
