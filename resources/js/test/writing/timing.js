document.addEventListener('DOMContentLoaded', () => {
    window.generateTimer?.();
});

// Global timer state (old interval osilib qolmasin)
window.WRITING_TIMER = window.WRITING_TIMER || {
    intervalId: null,
    endTime: 0,
    finished: false,
};

window.generateTimer = function () {
    const exerciseId = Number(document.getElementById('exercise_id')?.value || 0);
    const t1 = Number(document.getElementById('task_one_time_limit')?.value || 0);
    const t2 = Number(document.getElementById('task_two_time_limit')?.value || 0);

    const totalMinutes = t1 + t2;
    const totalMs = totalMinutes * 60 * 1000;

    const timeBig = document.getElementById('timeBig');
    const timeSmall = document.getElementById('timeSmall');

    const pad2 = (n) => String(n).padStart(2, '0');
    const formatMMSS = (ms) => {
        const s = Math.max(0, Math.floor(ms / 1000));
        const m = Math.floor(s / 60);
        const r = s % 60;
        return `${pad2(m)}:${pad2(r)}`;
    };

    // Avvalgi interval bo‘lsa tozalaymiz (eng muhim fix)
    if (window.WRITING_TIMER.intervalId) {
        clearInterval(window.WRITING_TIMER.intervalId);
        window.WRITING_TIMER.intervalId = null;
    }
    window.WRITING_TIMER.finished = false;

    // limit bo‘lmasa
    if (!totalMs || totalMs <= 0) {
        window.WRITING_TIMER.endTime = 0;
        if (timeBig) timeBig.textContent = '00:00';
        if (timeSmall) timeSmall.textContent = '';
        return;
    }

    // endTime ni localStorage’dan olamiz yoki yangi yaratamiz
    const now = Date.now();
    let endTime = Number(localStorage.getItem(window.STORAGE_TIME_KEY) || 0);

    if (!endTime || endTime <= now) {
        endTime = now + totalMs;
        localStorage.setItem(window.STORAGE_TIME_KEY, String(endTime));
    }

    window.WRITING_TIMER.endTime = endTime;

    // Tick funksiyasi endTime param bilan ishlasin (sizda bug bor edi: outer endTime ishlatib yuborilgan)
    window.tick = function (thisEndTime) {
        const left = Number(thisEndTime || 0) - Date.now();

        if (timeBig) timeBig.textContent = formatMMSS(left);
        if (timeSmall) timeSmall.textContent = '';

        if (left <= 0 && !window.WRITING_TIMER.finished) {
            window.WRITING_TIMER.finished = true;

            if (window.WRITING_TIMER.intervalId) {
                clearInterval(window.WRITING_TIMER.intervalId);
                window.WRITING_TIMER.intervalId = null;
            }

            if (timeBig) timeBig.textContent = '00:00';
            alert('Vaqt tugadi');
        }
    };

    window.tick(endTime);
    window.WRITING_TIMER.intervalId = setInterval(() => window.tick(endTime), 1000);

    window.addEventListener('beforeunload', () => {
        if (window.WRITING_TIMER.intervalId) clearInterval(window.WRITING_TIMER.intervalId);
    });
};

// Cancel bosilganda ishlatamiz: endTime’ni o‘chiradi + timer’ni yangidan ishga tushiradi
window.resetWritingTimer = function () {
    // localStorage endTime ni o‘chiramiz
    try { localStorage.removeItem(window.STORAGE_TIME_KEY); } catch (e) {}

    // UI + interval reset
    if (window.WRITING_TIMER?.intervalId) {
        clearInterval(window.WRITING_TIMER.intervalId);
        window.WRITING_TIMER.intervalId = null;
    }
    if (window.WRITING_TIMER) {
        window.WRITING_TIMER.endTime = 0;
        window.WRITING_TIMER.finished = false;
    }

    // qaytadan yaratadi
    window.generateTimer?.();
};
