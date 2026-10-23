<div>

    <div class="bg-white p-4 rounded-2xl mt-4 mb-2">
        <div class="text-base font-semibold text-[#FD4D00] mb-2">Have a promo code?</div>

        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" id="promo-code-input" wire:model.defer="promocode" placeholder="Enter promo code"
                   class="flex-1 rounded-lg border border-slate-300 px-3 py-2 placeholder-slate-400 focus:outline-none" />

            <button id="promo-apply-btn" type="button" wire:click="applyPromo" wire:loading.attr="disabled"
                    wire:target="applyPromo"
                    class="rounded-lg bg-[#FD4D00] text-white px-6 py-2 font-semibold hover:bg-[#e64400] transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap">
                <span wire:loading.remove wire:target="applyPromo" class="whitespace-nowrap">
                    Apply
                </span>

                <span wire:loading wire:target="applyPromo"
                      class="flex items-center gap-2 flex-nowrap whitespace-nowrap">
                    <svg class="h-4 w-4 inline animate-spin shrink-0" viewBox="0 0 24 24" fill="none"
                         aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                    </svg>
                    <span class="whitespace-nowrap">Finding...</span>
                </span>
            </button>
        </div>

        @if ($promoStatus == 1)
            <div class="mt-1">
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                         height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                    <div class="text-sm">
                        <div class="font-semibold text-red-700">
                            <span class="text-red-600">{{ $promoMessage }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($promoStatus == 2)
            <div class="mt-1">
                <div class="flex items-start gap-2 bg-green-50 border border-green-200 rounded-lg p-3 mt-3">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div class="text-sm">
                        <div class="font-semibold text-green-700">
                            {{ $promoMessage }}:
                            <span class="text-green-600 font-normal ml-1">{{ $appliedCodeCode }}, {{$appliedCodeDiscount}}% - discount applied</span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($promoStatus == 3)
            <div class="mt-1">
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                         height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                    <div class="text-sm">
                        <div class="font-semibold text-red-700">
                            <span class="text-red-600">{{ $promoMessage }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 {{count($packages) >=6 ? 'lg:grid-cols-3' : 'lg:grid-cols-2'}} gap-2 xl:gap-2">
        @foreach ($packages as $package)
            <div class="bg-white rounded-[32px] p-4 lg:p-6 shadow-sm border border-slate-50 hover:shadow-md transition-shadow relative">

                @if($package->discount>0)
                    <div class="absolute top-2 right-2 flex flex-col-2 items-end gap-1">
                        <div
                            class="rounded-full bg-[#FD4D00] px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold text-white shadow-[0_0_10px_rgba(253,77,0,0.6)] hover:shadow-[0_0_18px_rgba(253,77,0,0.9)] transition duration-300 animate-[pulse_2s_ease-in-out_infinite]">
                            {{ number_format($package->discount , 0, '.', '') }}%
                        </div>

                    </div>

                @endif
                <div class="mb-6">
                    @if($package->discount>0)


                        <div class="text-[#FD4D00] text-xl sm:text-2xl font-bold tracking-tight">
                            {{ number_format($package->price, 0, '.', ' ') }} UZS
                        </div>

                        <div
                            class="absolute text-xs sm:text-sm text-slate-400 line-through whitespace-nowrap animate-bounce"
                            style="top: 3.4rem">
                            {{ number_format($package->discountedPrice, 0, '.', ',') }} UZS
                        </div>

                    @else
                        <div class="text-[#FD4D00] text-xl sm:text-2xl font-bold tracking-tight">
                            {{ number_format($package->price, 0, '.', ' ') }} UZS
                        </div>
                    @endif

                </div>

                <div class="relative mb-6 pb-1">
                    <div class="flex items-stretch">
                        <div class="flex-1">
                            <div class="text-xl font-bold text-slate-900 mb-2">
                                {{ $package->speaking_count }}
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-60 shrink-0">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                    <line x1="12" x2="12" y1="19" y2="22"/>
                                </svg>
                                <span class="text-[12px] font-medium leading-none whitespace-nowrap">Speaking checks</span>
                            </div>
                        </div>

                        <div class="relative flex items-center justify-center">
                            <div class="w-px h-full bg-slate-100"></div>
                            <div class="absolute inset-auto bg-white border border-slate-100 rounded-full px-1.5 py-0.5 text-[10px] font-bold text-slate-400 uppercase tracking-tighter shadow-sm z-10">
                                or
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="text-xl ml-6 text-start font-bold text-slate-900 mb-2">
                                {{ $package->writing_count }}
                            </div>
                            <div class="flex items-center justify-start ml-6 gap-1.5 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-60 shrink-0">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                    <path d="m15 5 4 4"/>
                                </svg>
                                <span class="text-[12px] font-medium leading-none whitespace-nowrap">Writing checks</span>
                            </div>
                        </div>
                    </div>
                </div>

                    @if(auth()->check())
                        @if($package->discount==100)
                            <button
                                type="button"
                                wire:click="activateFreeCredit({{ $package->id }})"
                                class="w-full py-3 rounded-xl bg-orange-50 text-[#FD4D00] font-bold text-sm hover:bg-[#FD4D00] hover:text-white transition-all">
                                Buy
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="buyNow({{ $package->id }})"
                                class="w-full py-3 rounded-xl bg-orange-50 text-[#FD4D00] font-bold text-sm hover:bg-[#FD4D00] hover:text-white transition-all">
                                Buy
                            </button>
                        @endif

                    @else
                        <a href="{{route('login')}}"  class="subscribe-btn flex w-full items-center justify-center rounded-[12px] bg-orange-50 px-4 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-[#FD4D00] hover:text-white transition-colors cursor-pointer">
                            Buy
                        </a>
                    @endif

            </div>
        @endforeach
    </div>

    {{-- Bitta umumiy modal --}}
    <div id="credit-payment-modal"
         wire:ignore.self
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         data-credit-payment-modal>
        <div class="relative w-full max-w-md rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <button type="button"
                    data-credit-payment-close
                    class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-900 hover:bg-slate-200 transition-colors">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <div class="mb-4 sm:mb-6 pr-8 sm:pr-12">
                <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">
                    Payment Method
                </div>

                @if($package_id)
                    @php
                        $selectedPackage = $packages->firstWhere('id', $package_id);
                    @endphp

                    @if($selectedPackage)
                        <div class="mt-2 text-sm text-slate-500">
                            Selected package:
                            <span class="font-semibold text-slate-900">
                                {{ number_format($selectedPackage->price, 0, '.', ' ') }} UZS
                            </span>
                        </div>
                    @endif
                @endif
            </div>

            <div class="mb-6 flex gap-3 py-1 justify-center">
                <button type="button"
                        wire:click="purchaseCredit('payme')"
                        class="flex-1 rounded-2xl border-2 border-slate-200 bg-white px-4 py-6 hover:border-slate-300 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ asset('assets/payme.svg') }}" alt="payme">
                    </div>
                </button>

                <button type="button"
                        wire:click="purchaseCredit('click')"
                        class="flex-1 rounded-2xl border-2 border-slate-200 bg-white px-4 py-6 hover:border-slate-300 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ asset('assets/click.svg') }}" alt="click">
                    </div>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-credit-payment-modal', () => {
                if (typeof window.openCreditPaymentModal === 'function') {
                    window.openCreditPaymentModal();
                }
            });

            Livewire.on('close-credit-payment-modal', () => {
                if (typeof window.closeCreditPaymentModal === 'function') {
                    window.closeCreditPaymentModal();
                }
            });

            Livewire.on('open-payment', (data) => {
                if (typeof window.closeCreditPaymentModal === 'function') {
                    window.closeCreditPaymentModal();
                }

                if (data?.url) {
                    window.open(data.url, '_blank');
                }
            });
        });
    </script>
</div>
