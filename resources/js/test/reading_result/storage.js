window.getHidden = window.getHidden || function (id, fallback = null) {
    const el = document.getElementById(id);
    if (!el) return fallback;

    const raw = (el.value ?? '').trim();
    if (raw === '' || raw === 'null' || raw === 'undefined') return fallback;

    try { return JSON.parse(raw); }
    catch (_) { return raw; }
};

window.getBackendDraft = function () {
    const d = window.getHidden('reading_draft', null);
    if (!d) return {};
    if (typeof d === 'string') {
        try { return JSON.parse(d); } catch (_) { return {}; }
    }
    if (typeof d === 'object') return d;
    return {};
};

window.getReadingDraft = function () {
    return window.getBackendDraft?.() || {};
};

window.getReadingTextSizeDraft = function (){
    try { return JSON.parse(localStorage.getItem(window.STORAGE_TEXT_SIZE_KEY)) || {}; }
    catch { return {}; }
}
