document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('cameraPreview');
    const placeholder = document.getElementById('videoPlaceholder');
    const toggle = document.getElementById('cameraToggle'); // <-- switch (checkbox)

    const STORAGE_KEY = 'cefr-camera-enabled';

    let stream = null;
    let starting = false; // parallel startCamera chaqiruvlarini to‘xtatish

    // -------------------------
    // UI helpers
    // -------------------------
    const setMessage = (msg) => {
        if (!placeholder || !video) return;
        placeholder.textContent = msg;
        placeholder.classList.remove('hidden');
        video.classList.add('hidden');
    };

    const showVideo = () => {
        if (!placeholder || !video) return;
        placeholder.classList.add('hidden');
        video.classList.remove('hidden');
    };

    // -------------------------
    // Stream helpers
    // -------------------------
    const stopStream = () => {
        if (!stream) return;
        stream.getTracks().forEach((t) => t.stop());
        stream = null;

        // video srcObject ni ham bo‘shatib qo‘yamiz
        if (video) video.srcObject = null;
    };

    // -------------------------
    // Security checks
    // -------------------------
    const isLocalhost =
        location.hostname === 'localhost' ||
        location.hostname === '127.0.0.1' ||
        location.hostname === '::1';

    const canUseCamera = () => window.isSecureContext || isLocalhost;

    // -------------------------
    // Camera start/stop
    // -------------------------
    const startCamera = async () => {
        if (starting) return;          // ketma-ket bosishlarda duplicate bo‘lmasin
        starting = true;

        try {
            if (!toggle?.checked) {
                stopStream();
                setMessage('Camera off');
                return;
            }

            if (!canUseCamera()) {
                stopStream();
                setMessage('HTTPS kerak (yoki localhost)');
                return;
            }

            if (!navigator.mediaDevices?.getUserMedia) {
                stopStream();
                setMessage('Browser camera support yo‘q');
                return;
            }

            // Eski stream bo‘lsa tozalaymiz
            stopStream();

            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' }, // xohlasangiz: 'environment'
                audio: false
            });

            video.srcObject = stream;
            showVideo();

            // iOS/Safari ba’zan manual play talab qiladi
            const p = video.play?.();
            if (p && typeof p.catch === 'function') p.catch(() => {});
        } catch (err) {
            console.error('Camera error:', err);

            stopStream();

            if (err?.name === 'NotAllowedError' || err?.name === 'SecurityError') {
                setMessage('Kameraga ruxsat berilmadi');
            } else if (err?.name === 'NotFoundError') {
                setMessage('Camera topilmadi');
            } else {
                setMessage('Camera ishlamadi');
            }
        } finally {
            starting = false;
        }
    };

    // -------------------------
    // Init: toggle state
    // -------------------------
    const saved = localStorage.getItem(STORAGE_KEY);
    if (toggle) {
        // default ON (agar oldin o‘chirib qo‘ygan bo‘lsa 0 turadi)
        // toggle.checked = saved !== '0';
        toggle.checked = false;
    }

    // Start (yoki off message)
    startCamera();

    // Toggle change -> start/stop
    toggle?.addEventListener('change', () => {
        localStorage.setItem(STORAGE_KEY, toggle.checked ? '1' : '0');
        startCamera();
    });

    // Tab o‘zgarsa: hidden bo‘lsa to‘xtat, qaytsa faqat toggle ON bo‘lsa start
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopStream();
        } else {
            // faqat yoqilgan bo‘lsa qayta ishga tushadi
            startCamera();
        }
    });

    // Page exit -> stop stream
    window.addEventListener('beforeunload', stopStream);

    stopStream();

});
