@extends('layouts.user')

@section('content')
    <style>
        #plans-container::-webkit-scrollbar {
            width: 4px;
        }
        #plans-container::-webkit-scrollbar-thumb {
            background-color: rgba(107, 114, 128, 0.5);
            border-radius: 9999px;
        }
        #plans-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(107, 114, 128, 0.5) transparent;
        }
    </style>
    <div class="flex-1 w-full lg:w-auto">
        @php
            $asset = fn($p) => asset("assets/{$p}");
        @endphp
        <div class="mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    <div class="space-y-4 sm:space-y-6 rounded-2xl sm:rounded-[24px] bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Market</div>
                            <div class="inline-flex rounded-lg bg-gray-100 p-1">
                                <button id="tab-subscriptions"
                                    class="px-4 py-2 rounded-lg bg-[#FD4D00] text-white font-semibold">Subscriptions</button>
                                <button id="tab-credits"
                                    class="px-4 py-2 rounded-lg text-slate-700 transition">Credits</button>
                            </div>
                        </div>

                        <div id="section-subscriptions">
                            @livewire('user.premium.subscription-livewire')
                        </div>

                        <div id="section-credits" class="hidden space-y-6 rounded-2xl">
                            @livewire('user.premium.credit-livewire')
                        </div>

                    </div>
                </div>

                @include('components.membership-card')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabSubs = document.getElementById('tab-subscriptions');
            const tabCredits = document.getElementById('tab-credits');
            const sectionSubs = document.getElementById('section-subscriptions');
            const sectionCredits = document.getElementById('section-credits');

            const paymentModal = document.querySelector('[data-payment-modal]');
            const creditPaymentModal = document.querySelector('[data-credit-payment-modal]');

            const paymentClose = document.querySelector('[data-payment-close]');
            const creditPaymentClose = document.querySelector('[data-credit-payment-close]');

            const paymentOptions = document.querySelectorAll('[data-payment-option]');
            const paymentSubmit = document.querySelector('[data-payment-submit]');

            let selectedItem = null;
            let selectedPaymentMethod = null;

            const setActiveTab = (subsActive) => {
                if (sectionSubs) {
                    sectionSubs.classList.toggle('hidden', !subsActive);
                }

                if (sectionCredits) {
                    sectionCredits.classList.toggle('hidden', subsActive);
                }

                if (tabSubs) {
                    tabSubs.classList.toggle('bg-[#FD4D00]', subsActive);
                    tabSubs.classList.toggle('text-white', subsActive);
                    tabSubs.classList.toggle('text-slate-700', !subsActive);
                }

                if (tabCredits) {
                    tabCredits.classList.toggle('bg-[#FD4D00]', !subsActive);
                    tabCredits.classList.toggle('text-white', !subsActive);
                    tabCredits.classList.toggle('text-slate-700', subsActive);
                }
            };

            const openPaymentModal = () => {
                if (!paymentModal) return;

                paymentModal.classList.remove('hidden');
                paymentModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };

            const closePaymentModal = () => {
                if (!paymentModal) return;

                paymentModal.classList.add('hidden');
                paymentModal.classList.remove('flex');

                selectedPaymentMethod = null;

                paymentOptions.forEach(opt => {
                    opt.classList.remove('border-[#FD4D00]');
                    opt.classList.add('border-slate-200');
                });

                if (!creditPaymentModal || creditPaymentModal.classList.contains('hidden')) {
                    document.body.style.overflow = '';
                }
            };

            window.openCreditPaymentModal = function () {
                if (!creditPaymentModal) return;

                creditPaymentModal.classList.remove('hidden');
                creditPaymentModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };

            window.closeCreditPaymentModal = function () {
                if (!creditPaymentModal) return;

                creditPaymentModal.classList.add('hidden');
                creditPaymentModal.classList.remove('flex');

                if (!paymentModal || paymentModal.classList.contains('hidden')) {
                    document.body.style.overflow = '';
                }
            };

            const hasSubscription = {{ $hasSubscription ? 'true' : 'false' }};
            const requestedCredits = {{ request()->route('type') ? 'true' : 'false' }};

            const showSubscriptionRequiredAlert = () => {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('Please purchase a subscription first to buy credits.', 'warning', 5000);
                }
            };

            if (tabSubs && tabCredits) {
                tabSubs.addEventListener('click', () => setActiveTab(true));
                tabCredits.addEventListener('click', () => {
                    if (!hasSubscription) {
                        setActiveTab(true);
                        showSubscriptionRequiredAlert();
                        return;
                    }

                    setActiveTab(false);
                });

                setActiveTab({{$type ? 'false' : 'true' }});

                if (!hasSubscription && requestedCredits) {
                    showSubscriptionRequiredAlert();
                }
            }

            paymentOptions.forEach(opt => {
                opt.addEventListener('click', (e) => {
                    e.preventDefault();

                    const method = opt.getAttribute('data-method');
                    selectedPaymentMethod = method;

                    paymentOptions.forEach(o => {
                        o.classList.remove('border-[#FD4D00]');
                        o.classList.add('border-slate-200');
                    });

                    opt.classList.remove('border-slate-200');
                    opt.classList.add('border-[#FD4D00]');
                });
            });

            document.addEventListener('click', e => {
                const btn = e.target.closest('[data-subscription-btn]');

                if (!btn) {
                    return;
                }

                e.preventDefault();

                const planId = btn.getAttribute('data-plan-id');
                const planPrice = btn.getAttribute('data-plan-price');

                selectedItem = {
                    type: 'plan',
                    id: planId,
                    price: planPrice ? parseFloat(planPrice) : 0
                };

                openPaymentModal();
            });

            if (paymentClose) {
                paymentClose.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    closePaymentModal();
                });
            }

            if (creditPaymentClose) {
                creditPaymentClose.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    closeCreditPaymentModal();
                });
            }

            if (paymentModal) {
                paymentModal.addEventListener('click', (e) => {
                    if (e.target === paymentModal) {
                        closePaymentModal();
                    }
                });
            }

            if (creditPaymentModal) {
                creditPaymentModal.addEventListener('click', (e) => {
                    if (e.target === creditPaymentModal) {
                        closeCreditPaymentModal();
                    }
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (paymentModal && !paymentModal.classList.contains('hidden')) {
                        closePaymentModal();
                    }

                    if (creditPaymentModal && !creditPaymentModal.classList.contains('hidden')) {
                        closeCreditPaymentModal();
                    }
                }
            });

            if (paymentSubmit) {
                paymentSubmit.addEventListener('click', (e) => {
                    e.preventDefault();

                    // console.log({ selectedItem, selectedPaymentMethod });
                });
            }
        });
    </script>
@endsection
