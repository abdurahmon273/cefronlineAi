window.showDraftAlert = function (message) {
    const alertElement = document.getElementById('draftAlert');
    const messageElement = document.getElementById('AcceptCancelAlertMessage');
    if (alertElement) {
        messageElement.innerText=message
        alertElement.classList.remove('hidden');
        void alertElement.offsetWidth;
        alertElement.classList.remove('opacity-0', 'translate-y-[-20px]');
        alertElement.classList.add('opacity-100', 'translate-y-0');
    }
}

window.hideDraftAlert = function () {
    const alertElement = document.getElementById('draftAlert');
    if (alertElement) {
        alertElement.classList.remove('opacity-100', 'translate-y-0');
        alertElement.classList.add('opacity-0', 'translate-y-[-20px]');
        alertElement.addEventListener('transitionend', function handler() {
            alertElement.classList.add('hidden');
            alertElement.removeEventListener('transitionend', handler);
        }, { once: true });
    }
}

window.showFinishModal = function () {
    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    // show => flex
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // reflow (transition ishlashi uchun)
    void modal.offsetWidth;

    // overlay fade in
    modal.classList.remove('opacity-0');
    modal.classList.add('opacity-100');

    // card pop in
    card.classList.remove('scale-95', 'translate-y-2');
    card.classList.add('scale-100', 'translate-y-0');
};

window.hideFinishModal = function () {
    const modal = document.getElementById('finishModal');
    const card  = document.getElementById('finishModalCard');
    if (!modal || !card) return;

    // overlay fade out
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    // card pop out
    card.classList.remove('scale-100', 'translate-y-0');
    card.classList.add('scale-95', 'translate-y-2');

    const onEnd = (e) => {
        // faqat modal opacity transition tugaganda yopamiz
        if (e.target !== modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        modal.removeEventListener('transitionend', onEnd);
    };

    modal.addEventListener('transitionend', onEnd);
};


