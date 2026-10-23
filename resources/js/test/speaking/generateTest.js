document.addEventListener('DOMContentLoaded', async () => {
    const { exerciseId, partsData } = window.generatePartData();
    window.__SPEAKING__ = { exerciseId, partsData, queue: [] };

    // ✅ HAR RELOAD YANGI ATTEMPT: draft + blobs tozalansin
    try { await window.clearSpeakingDraft?.(); } catch (_) {}

    const navWrap = document.getElementById('partsNav');
    if (!navWrap) return;

    const defs = [
        {
            key: 'partOne',
            num: 1,
            btnId: 'partOneButton',
            panelId: 'spPart1Container',
            available: partsData.partOneFirst || partsData.partOneSecond
        },
        {
            key: 'partTwo',
            num: 2,
            btnId: 'partTwoButton',
            panelId: 'spPart2Container',
            available: partsData.partTwo
        },
        {
            key: 'partThree',
            num: 3,
            btnId: 'partThreeButton',
            panelId: 'spPart3Container',
            available: partsData.partThree
        },
    ];

    const available = defs.filter(d => d.available);

    navWrap.innerHTML = '';
    if (available.length === 0) return;

    // ✅ 3 tadan oshmaydi, PART1 ichida 1.1+1.2 bitta
    const btnEls = available.map((d, idx) => {
        const btn = window.createSpeakingBtn(d, idx === 0);
        navWrap.appendChild(btn);
        return btn;
    });

    window.availableSpeakingParts = available;
    window.speakingBtnElements = btnEls;

    window.generateSpeakingQuestionDiv(available, partsData);

    window.setSpeakingActive(0, available, btnEls);

    window.ensureSpeakingSharedControls?.();

    window.buildSpeakingQueueFromDOM?.();

    window.hideAllSpeakingQuestions?.();
    const q0 = window.__SPEAKING__?.queue?.[0];
    if (q0?.el) window.showOnlyThisSpeakingQuestion?.(q0.el);

    const d = window.getSpeakingDraft?.() || {};
    if (!d.version) {
        window.setSpeakingDraft({
            version: 2,
            exercise_id: exerciseId,
            created_at: new Date().toISOString(),
            current_index: 0,
            part_one: { one_first_question: [], one_second_question: [] },
            part_two: { question: null },
            part_three: { id: 0 }
        });
    }

    window.ensureSpeakingDraftBase?.();
    window.setSpeakingCurrentIndex?.(0);

    try {
        await window.preloadSpeakingAudios?.(window.__SPEAKING__?.partsData);
    } catch (e) {
        console.warn('Audio preload failed, continue anyway', e);
    }

    const ok = await window.ensureMicPermissionOrReload?.();
    if (!ok) return;

    window.startSpeakingExam?.();

});


window.getHidden = window.getHidden || function (id, fallback = null) {
    const el = document.getElementById(id);
    if (!el) return fallback;

    const raw = (el.value ?? '').trim();
    if (raw === '' || raw === 'null' || raw === 'undefined') return fallback;

    try { return JSON.parse(raw); } catch (_) { return raw; }
};

window.generatePartData = function () {
    const exerciseId = window.getHidden('exercise_id', 0);

    const audioInstructionsData = window.getHidden('audio_instructions', null);

    const partOneFirst  = window.getHidden('part_one_first', null);
    const partOneSecond = window.getHidden('part_one_second', null);
    const partTwo       = window.getHidden('part_two', null);
    const partThree     = window.getHidden('part_three', null);

    const partsData = { audioInstructionsData, partOneFirst, partOneSecond, partTwo, partThree };
    return { exerciseId, partsData };
};

window.createSpeakingBtn = function (d, isActive) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = d.btnId;

    // ✅ size: h-10
    // ✅ active bo‘lsa faqat bg + border class (part-btn-active)
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
        isActive ? 'part-btn-active' : 'part-btn hover:bg-gray-100'
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
        <span>${d.num}</span>
    `;

    return btn;
};


window.setSpeakingActive = function (activeIndex, available, btnEls) {
    // Panels
    available.forEach((d, i) => {
        const panel = document.getElementById(d.panelId);
        if (!panel) return;
        panel.classList.toggle('hidden', i !== activeIndex);
    });

    // Buttons (✅ active -> only part-btn-active)
    btnEls.forEach((btn, i) => {
        const isActive = i === activeIndex;
        btn.classList.toggle('part-btn-active', isActive);
        btn.classList.toggle('part-btn', !isActive);
        btn.classList.toggle('hover:bg-gray-100', !isActive);
    });
};


window.generateSpeakingQuestionDiv = function (available, partsData) {
    const map = {
        partOne: () => window.GeneratePartOneQuestionDiv(partsData.partOneFirst, partsData.partOneSecond),
        partTwo: () => window.GeneratePartTwoQuestionDiv(partsData.partTwo),
        partThree: () => window.GeneratePartThreeQuestionDiv(partsData.partThree),
    };

    available.forEach(p => {
        const fn = map[p.key];
        if (typeof fn === 'function') fn();
    });

    reRenderTextP()
};

// PART 1
window.GeneratePartOneQuestionDiv = function (dataFirst, dataSecond) {
    const wrap = document.getElementById('partOneQuestionDiv');
    if (!wrap) return;

    wrap.innerHTML = '';

    const q1 = Array.isArray(dataFirst?.questions) ? dataFirst.questions : [];
    const q2 = Array.isArray(dataSecond?.questions) ? dataSecond.questions : [];
    const images = Array.isArray(dataSecond?.images) ? dataSecond.images : [];

    const all = [
        ...q1.map(q => ({ ...q, __hasImages: false, __sub: 'one_first' })),
        ...q2.map(q => ({ ...q, __hasImages: images.length > 0, __sub: 'one_second' })),
    ];

    all.forEach((q, idx) => {
        const order = idx + 1;

        const block = window.buildSpeakingPart1QuestionBlock({
            order,
            part1Set: q.__sub, // ✅ "one_first" / "one_second"
            questionId: q?.id ?? 0,
            question: q?.question ?? '',
            audioLink: q?.audio_link ?? '',
            recordTime: Number(q?.record_time ?? 0),
            waitTime: Number(q?.wait_time ?? 0),
            images: q.__hasImages ? images : []
        });

        block.classList.add('hidden');
        wrap.appendChild(block);
    });
};

// PART 2
window.GeneratePartTwoQuestionDiv = function (data) {
    const wrap = document.getElementById('partTwoQuestionDiv');
    if (!wrap) return;

    wrap.innerHTML = '';

    const q = data?.question || null;
    const images = Array.isArray(data?.images) ? data.images : [];
    const imgUrl = images[0]?.url ?? '';

    const globalOrder = window.getNextSpeakingGlobalOrderStart?.(1) ?? 1;

    const block = window.buildSpeakingPart2Block({
        globalOrder,
        questionId: q?.id ?? 0,
        title: data?.title ?? 'Part 2',
        questionText: q?.question ?? '',
        audioLink: q?.audio_link ?? '',
        recordTime: Number(q?.record_time ?? 0),
        waitTime: Number(q?.wait_time ?? 0),
        imageUrl: imgUrl
    });

    block.classList.add('hidden');
    wrap.appendChild(block);
};

// PART 3
window.GeneratePartThreeQuestionDiv = function (data) {
    const wrap = document.getElementById('partThreeQuestionDiv');
    if (!wrap) return;

    wrap.innerHTML = '';

    const globalOrder = window.getNextSpeakingGlobalOrderStart?.(1) ?? 1;

    const block = window.buildSpeakingPart3Block({
        globalOrder,
        partNum: 3,
        questionId: data?.id ?? 0,
        theme: data?.theme ?? '',
        questionText: data?.question_text ?? '',
        audioLink: data?.audio_link ?? '',
        waitTime: Number(data?.wait_time ?? 0),
        time: Number(data?.time ?? 0)
    });

    block.classList.add('hidden');
    wrap.appendChild(block);

    // ✅ PART 3: ikkinchi DIV (background yo‘q), buttons shu yerda
    const controlsHolder = document.createElement('div');
    controlsHolder.className = 'mt-4'; // ozgina joy
    const controls = window.createSpeakingControls({ targetOrder: globalOrder, layout: 'p3' });
    controlsHolder.appendChild(controls);
    wrap.appendChild(controlsHolder);
};
