document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('cameraToggle');
    const video = document.getElementById('cameraPreview');
    const placeholder = document.getElementById('videoPlaceholder');

    if (toggle) {
        toggle.checked = false;
        toggle.disabled = true;
        toggle.setAttribute('disabled', 'disabled');
    }

    // video never starts
    if (video) {
        try { video.pause(); } catch (_) {}
        video.classList.add('hidden');
    }
    if (placeholder) {
        placeholder.classList.remove('hidden');
        placeholder.textContent = 'Video';
    }
});
