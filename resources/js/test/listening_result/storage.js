// Storage is NOT used in result mode.
// We export dummy functions to prevent errors if other modules try to import/call them.

window.getListeningDraft = function () { return {}; };
window.saveListeningDraft = function () { };
window.clearListeningDraft = function () { };
