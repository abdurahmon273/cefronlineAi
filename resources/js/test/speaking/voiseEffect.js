document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('voiceWaveCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    let width = 0, height = 0;
    let rafId = null;

    // ✅ record holati
    let isRecordingActive = false;

    // === VISUAL CONFIG ===
    const config = {
        lineCount: 9,
        particleSpacing: 2,
        dotSize: 0.9,

        minAmp: 0.01,
        maxAmp: 0.2,

        minSpeed: 0.012,
        maxSpeed: 0.03,

        smoothing: 0.85,
        noiseGate: 0.015,
    };

    const lines = Array.from({ length: config.lineCount }, (_, i) => ({
        freq: 0.012 + i * 0.003,
        speedDir: (i % 2 === 0 ? 1 : -1),
        phase: (i * Math.PI) / config.lineCount,
    }));

    let t = 0;

    // === AUDIO ANALYSER ===
    let audioCtx = null;
    let analyser = null;
    let data = null;
    let source = null;

    let volSmoothed = 0;
    let volInstant = 0;

    function clamp(n, a, b) { return Math.max(a, Math.min(b, n)); }

    function mapRange(x, inMin, inMax, outMin, outMax) {
        const p = (x - inMin) / (inMax - inMin);
        const pp = clamp(p, 0, 1);
        return outMin + (outMax - outMin) * pp;
    }

    function resize() {
        width = canvas.offsetWidth;
        height = canvas.offsetHeight;

        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.floor(width * dpr);
        canvas.height = Math.floor(height * dpr);

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);

        // resize bo'lganda idle chiziq qayta chizib qo'yamiz
        if (!isRecordingActive) drawIdleLine();
    }

    window.addEventListener('resize', resize);
    resize();

    function drawIdleLine() {
        ctx.clearRect(0, 0, width, height);

        const centerY = height / 2;
        ctx.beginPath();
        ctx.moveTo(0, centerY);
        ctx.lineTo(width, centerY);
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.15)';
        ctx.lineWidth = 1;
        ctx.stroke();
    }

    async function initMic() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });

            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioCtx.createAnalyser();
            analyser.fftSize = 1024;
            analyser.smoothingTimeConstant = 0.7;

            source = audioCtx.createMediaStreamSource(stream);
            source.connect(analyser);

            data = new Uint8Array(analyser.fftSize);

            // iOS/Safari: user gesture bo‘lmasa suspended bo‘lishi mumkin
            if (audioCtx.state === 'suspended') {
                const resumeOnce = async () => {
                    try { await audioCtx.resume(); } catch (_) {}
                    window.removeEventListener('click', resumeOnce);
                    window.removeEventListener('touchstart', resumeOnce);
                };
                window.addEventListener('click', resumeOnce, { once: true });
                window.addEventListener('touchstart', resumeOnce, { once: true });
            }

            return true;
        } catch (e) {
            analyser = null;
            data = null;
            return false;
        }
    }

    function computeVolumeRMS() {
        if (!analyser || !data) return 0;

        analyser.getByteTimeDomainData(data);

        let sum = 0;
        for (let i = 0; i < data.length; i++) {
            const v = (data[i] - 128) / 128;
            sum += v * v;
        }
        const rms = Math.sqrt(sum / data.length);

        const gated = rms < config.noiseGate ? 0 : rms;
        return Math.sqrt(clamp(gated, 0, 1));
    }

    function drawRecordingFrame() {
        // ❗ recording bo'lmasa animatsiya qilmaymiz
        if (!isRecordingActive) return;

        volInstant = computeVolumeRMS();
        volSmoothed = config.smoothing * volSmoothed + (1 - config.smoothing) * volInstant;

        const amp = mapRange(volSmoothed, 0, 0.35, config.minAmp, config.maxAmp);
        const speed = mapRange(volSmoothed, 0, 0.35, config.minSpeed, config.maxSpeed);

        t += speed;

        ctx.clearRect(0, 0, width, height);

        const centerY = height / 2;

        // center line (record paytida ham ko'rinsin)
        ctx.beginPath();
        ctx.moveTo(0, centerY);
        ctx.lineTo(width, centerY);
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.05)';
        ctx.lineWidth = 0.6;
        ctx.stroke();

        lines.forEach((line, i) => {
            const phase = t * (1.2 + i * 0.15) * line.speedDir + line.phase;

            for (let x = 0; x < width; x += config.particleSpacing) {
                const nx = x / width;
                const envelope = Math.pow(4 * nx * (1 - nx), 1.2);
                const energy = 0.6 + 0.9 * volSmoothed;

                const yDelta =
                    Math.sin(x * line.freq + phase + Math.sin(t + x * 0.006)) *
                    (height * amp * energy) *
                    envelope;

                const y1 = centerY + yDelta;
                const y2 = centerY - yDelta;

                const opacity = (0.06 + 0.42 * envelope * (0.5 + volSmoothed)).toFixed(3);
                ctx.fillStyle = `rgba(0,0,0, ${opacity})`;

                ctx.beginPath();
                ctx.arc(x, y1, config.dotSize, 0, Math.PI * 2);
                ctx.fill();

                ctx.beginPath();
                ctx.arc(x, y2, config.dotSize, 0, Math.PI * 2);
                ctx.fill();
            }
        });

        rafId = requestAnimationFrame(drawRecordingFrame);
    }

    function startRecordingAnimation() {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(drawRecordingFrame);
    }

    function stopRecordingAnimation() {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;

        // reset volume state
        volSmoothed = 0;
        volInstant = 0;

        // idle chiziq
        drawIdleLine();
    }

    // ✅ timing.js dan boshqariladi
    window.setVoiceEffectRecording = function (flag) {
        const next = !!flag;
        if (next === isRecordingActive) return;

        isRecordingActive = next;

        if (isRecordingActive) {
            // record boshlanganda: animatsiya
            startRecordingAnimation();
        } else {
            // record tugaganda: idle
            stopRecordingAnimation();
        }
    };

    // start: mic init, lekin default idle chiziq
    await initMic();
    drawIdleLine();

    window.addEventListener('beforeunload', () => {
        if (rafId) cancelAnimationFrame(rafId);
        try { if (audioCtx) audioCtx.close(); } catch (_) {}
    });
});
