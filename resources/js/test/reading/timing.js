document.addEventListener('DOMContentLoaded', () => {
    generateTimer();
});

window.generateTimer = function () {
    const exerciseId = Number(document.getElementById('exercise_id')?.value || 0);
    const t1 = Number(document.getElementById('time_limit')?.value || 0);

    const totalMinutes = t1;
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

    if (!totalMs || totalMs <= 0) {
        if (timeBig) timeBig.textContent = '00:00';
        if (timeSmall) timeSmall.textContent = '';
        return;
    }

    // Har test uchun alohida key bo‘lsin (muhim!)
    window.STORAGE_TIME_KEY = `reading_end_time_${exerciseId}`;

    const now = Date.now();
    let endTime = Number(localStorage.getItem(window.STORAGE_TIME_KEY) || 0);

    if (!endTime || endTime <= now) {
        endTime = now + totalMs;
        localStorage.setItem(window.STORAGE_TIME_KEY, String(endTime));
    }

    let timer = null;
    let finished = false;

    const finishOnce = () => {
        if (finished) return;
        finished = true;

        clearInterval(timer);


        if (timeBig) {
            timeBig.textContent = '00:00';
            timeBig.classList.add('text-red-600');
        }
        if (timeSmall) timeSmall.textContent = '';
        // Timer key ni tozalab yuboramiz (keyingi kirganda qayta ishlashi uchun)
        localStorage.removeItem(window.STORAGE_TIME_KEY);

    };

    const tick = () => {
        const left = endTime - Date.now();

        if (timeBig) timeBig.textContent = formatMMSS(left);
        if (timeSmall) timeSmall.textContent = '';

        if (left <= 0) {
            finishOnce();
        }
    };

    tick();
    timer = setInterval(tick, 1000);

    window.addEventListener('beforeunload', () => clearInterval(timer));
};
