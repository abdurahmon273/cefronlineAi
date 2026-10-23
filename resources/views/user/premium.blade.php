@extends('layouts.user')

@section('content')
    @php
        $asset = fn($p) => asset("assets/{$p}");
    @endphp


    <div class="flex-1 w-full lg:w-auto">
        <div class="mx-auto px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                {{-- Premium Content --}}
                <div class="lg:col-span-9">
                    <div class="space-y-4 sm:space-y-6 rounded-2xl sm:rounded-[24px] bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                        {{-- Title --}}
                        <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Purchase plan</div>

                        {{-- Premium Subscription Options Card --}}
                        <div
                            class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#FD4D00] to-[#E64500] p-4 sm:p-6 lg:p-8 text-white">
                            <div class="relative z-10">
                                <div class="text-lg sm:text-xl lg:text-[24px] font-extrabold mb-3 sm:mb-4">Premium subscription options</div>
                                @php
                                    $features = [
                                        '24/7 Support',
                                        'Fast Response Times',
                                        'Multi-language Support',
                                        'Knowledgeable Staff',
                                        'Live Chat Assistance',
                                        'Service Level Agreements',
                                    ];
                                @endphp
                                <div class="space-y-0">
                                    @foreach($features as $feature)
                                        <div class="flex items-center gap-1">
                                            <img class="h-[24px] w-[24px]" src="{{ $asset('check.svg') }}" alt="check">
                                            <span class="text-[12px] font-normal">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Graphic: Hand holding microphone and book with sound waves --}}
                            <div class="absolute right-2 bottom-[-30px] hidden lg:block">
                                <img class="w-[317px] h-[317px]" src="{{ $asset('premium-banner.svg') }}" alt="premium">
                            </div>
                            <div class="absolute right-[-250px] bottom-[-150px] hidden lg:block">
                                <img class="" src="{{ $asset('effects/banner-smoke.svg') }}" alt="premium">
                            </div>
                            <div class="absolute left-[-250px] top-[-250px] hidden lg:block">
                                <img class="" src="{{ $asset('effects/banner-smoke.svg') }}" alt="premium">
                            </div>
                        </div>

                        {{-- Select Subscription Period --}}
                        <div>
                            <div class="text-xl sm:text-2xl lg:text-[24px] font-extrabold text-slate-900 mb-3 sm:mb-4">Select subscription period</div>
                            <div class="grid gap-3 sm:gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {{-- Starter Plan --}}
                                <div
                                    class="relative rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="text-base sm:text-lg font-extrabold text-slate-900">Starter</div>
                                        <div class="text-base sm:text-lg font-semibold text-[#FD4D00]">180 000 UZS</div>
                                    </div>
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="text-sm text-slate-500">60 000 UZS | month</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-slate-500">3 months</div>
                                        </div>
                                    </div>
                                    <button data-subscription-btn data-plan="starter" data-price="180000"
                                        class="w-full rounded-[12px] bg-orange-50 px-4 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-orange-100 transition-colors">
                                        Buy
                                    </button>
                                </div>

                                {{-- Premium Plan (Recommended) --}}
                                <div
                                    class="relative rounded-2xl sm:rounded-3xl bg-[#FFF4F0] p-4 sm:p-6 shadow-md hover:shadow-lg transition-shadow">
                                    {{-- Save Badge --}}
                                    <div
                                        class="absolute top-0 right-0 rounded-full bg-[#FD4D00] px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold text-white">
                                        Save 50%
                                    </div>
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="text-base sm:text-lg font-extrabold text-slate-900">Premium</div>
                                        <div class="text-base sm:text-lg font-semibold text-[#FD4D00]">300 000 UZS</div>
                                    </div>
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="text-sm text-slate-500">50 000 UZS | month</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-slate-500">6 months</div>
                                        </div>
                                    </div>
                                    <button data-subscription-btn data-plan="premium" data-price="300000"
                                        class="w-full rounded-[12px] bg-gradient-to-b from-[#FD4D00] to-[#E64500] px-4 py-3 text-sm font-semibold text-white hover:from-[#E64500] hover:to-[#D63A00] transition-colors shadow-sm">
                                        Buy
                                    </button>
                                </div>

                                {{-- Professional Plan --}}
                                <div
                                    class="relative rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="text-base sm:text-lg font-extrabold text-slate-900">Professional</div>
                                        <div class="text-base sm:text-lg font-semibold text-[#FD4D00]">480 000 UZS</div>
                                    </div>
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="text-sm text-slate-500">40 000 UZS | month</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-slate-500">12 months</div>
                                        </div>
                                    </div>
                                    <button data-subscription-btn data-plan="professional" data-price="480000"
                                        class="w-full rounded-[12px] bg-orange-50 px-4 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-orange-100 transition-colors">
                                        Buy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar - Membership Card --}}
                @include('components.membership-card')
                </div>
            </div>
        </div>
    </div>


    {{-- Payment Method Modal --}}
    <div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        data-payment-modal>
        <div class="relative w-full max-w-md rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            {{-- Close Button --}}
            <button data-payment-close
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-900 hover:bg-slate-200 transition-colors">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            {{-- Modal Title --}}
            <div class="mb-4 sm:mb-6 pr-8 sm:pr-12">
                <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Payment Method</div>
            </div>

            {{-- Payment Options --}}
            <div class="mb-6 flex gap-3 py-1">
                {{-- Payme Option --}}
                <button data-payment-option data-method="payme"
                    class="flex-1 rounded-2xl border-2 border-slate-200 bg-white px-4 py-5 hover:border-slate-300 transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ $asset('payme.svg') }}" alt="payme">
                    </div>
                </button>

                {{-- Click Option --}}
                <button data-payment-option data-method="click"
                    class="flex-1 rounded-2xl border-2 border-[#FD4D00] bg-white px-4 py-5 hover:border-[#E64500] transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ $asset('click.svg') }}" alt="click">
                    </div>
                </button>
            </div>

            {{-- Pay Button --}}
            <button data-payment-submit
                class="w-full rounded-2xl bg-[#FD4D00] px-4 py-4 text-base font-semibold text-white hover:bg-[#E64500] transition-colors">
                Pay
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (() => {
            // Topnav profile dropdown (shared behavior)
            const topnavs = document.querySelectorAll('[data-topnav]');
            const closeAll = () => {
                topnavs.forEach((nav) => {
                    const menu = nav.querySelector('[data-topnav-menu]');
                    const profile = nav.querySelector('[data-topnav-profile]');
                    if (!menu || !profile) return;
                    menu.hidden = true;
                    profile.setAttribute('aria-expanded', 'false');
                    nav.setAttribute('data-open', 'false');
                });
            };
            topnavs.forEach((nav) => {
                const menu = nav.querySelector('[data-topnav-menu]');
                const profile = nav.querySelector('[data-topnav-profile]');
                if (!menu || !profile) return;
                const initiallyOpen = nav.getAttribute('data-open') === 'true';
                menu.hidden = !initiallyOpen;
                profile.setAttribute('aria-expanded', initiallyOpen ? 'true' : 'false');
                profile.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !menu.hidden;
                    closeAll();
                    menu.hidden = isOpen;
                    profile.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    nav.setAttribute('data-open', isOpen ? 'false' : 'true');
                });
            });
            document.addEventListener('click', () => closeAll());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAll();
            });

            // Notifications Dropdown
            const notificationModal = document.querySelector('[data-notification-modal]');
            const notificationTriggers = document.querySelectorAll('[data-notification-trigger]');
            const notificationClose = document.querySelector('[data-notification-close]');

            if (notificationModal) {
                const openModal = () => {
                    notificationModal.classList.remove('hidden');
                };

                const closeModal = () => {
                    notificationModal.classList.add('hidden');
                };

                notificationTriggers.forEach((trigger) => {
                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = !notificationModal.classList.contains('hidden');
                        if (isOpen) {
                            closeModal();
                        } else {
                            openModal();
                        }
                    });
                });

                if (notificationClose) {
                    notificationClose.addEventListener('click', (e) => {
                        e.stopPropagation();
                        closeModal();
                    });
                }

                document.addEventListener('click', (e) => {
                    if (!notificationModal.contains(e.target) && !e.target.closest('[data-notification-trigger]')) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !notificationModal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            }

            // Payment Modal
            const paymentModal = document.querySelector('[data-payment-modal]');
            const paymentClose = document.querySelector('[data-payment-close]');
            const subscriptionButtons = document.querySelectorAll('[data-subscription-btn]');
            const paymentOptions = document.querySelectorAll('[data-payment-option]');
            const paymentSubmit = document.querySelector('[data-payment-submit]');
            let selectedPlan = null;
            let selectedPaymentMethod = null;

            const openPaymentModal = () => {
                if (paymentModal) {
                    paymentModal.classList.remove('hidden');
                    paymentModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                    // Set "click" as default selected
                    paymentOptions.forEach(opt => {
                        const method = opt.getAttribute('data-method');
                        if (method === 'click') {
                            opt.classList.remove('border-slate-200');
                            opt.classList.add('border-[#FD4D00]');
                            selectedPaymentMethod = 'click';
                        } else {
                            opt.classList.remove('border-[#FD4D00]');
                            opt.classList.add('border-slate-200');
                        }
                    });
                }
            };

            const closePaymentModal = () => {
                if (paymentModal) {
                    paymentModal.classList.add('hidden');
                    paymentModal.classList.remove('flex');
                    document.body.style.overflow = '';
                    // Reset selection
                    selectedPaymentMethod = null;
                    paymentOptions.forEach(opt => {
                        opt.classList.remove('border-[#FD4D00]');
                        opt.classList.add('border-slate-200');
                    });
                }
            };

            // Open modal when subscription button is clicked
            subscriptionButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    selectedPlan = {
                        plan: btn.getAttribute('data-plan'),
                        price: btn.getAttribute('data-price')
                    };
                    openPaymentModal();
                });
            });

            // Close modal
            if (paymentClose) {
                paymentClose.addEventListener('click', (e) => {
                    e.stopPropagation();
                    closePaymentModal();
                });
            }

            // Close modal when clicking outside
            if (paymentModal) {
                paymentModal.addEventListener('click', (e) => {
                    if (e.target === paymentModal) {
                        closePaymentModal();
                    }
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && paymentModal && !paymentModal.classList.contains('hidden')) {
                    closePaymentModal();
                }
            });

            // Select payment method
            paymentOptions.forEach(option => {
                option.addEventListener('click', () => {
                    // Remove selection from all options
                    paymentOptions.forEach(opt => {
                        opt.classList.remove('border-[#FD4D00]');
                        opt.classList.add('border-slate-200');
                    });
                    // Add selection to clicked option
                    option.classList.remove('border-slate-200');
                    option.classList.add('border-[#FD4D00]');
                    selectedPaymentMethod = option.getAttribute('data-method');
                });
            });

            // Submit payment
            if (paymentSubmit) {
                paymentSubmit.addEventListener('click', () => {
                    if (!selectedPaymentMethod) {
                        alert('Please select a payment method');
                        return;
                    }
                    if (!selectedPlan) {
                        alert('Please select a subscription plan');
                        return;
                    }
                    // Here you would typically submit the payment
                    console.log('Processing payment:', {
                        plan: selectedPlan.plan,
                        price: selectedPlan.price,
                        method: selectedPaymentMethod
                    });
                    // Close modal after submission (or redirect to payment gateway)
                    // closePaymentModal();
                });
            }
        })();
    </script>
@endsection
