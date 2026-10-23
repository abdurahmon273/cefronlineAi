<div>

    <div
        class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#FD4D00] to-[#E64500] p-4 sm:p-6 lg:p-8 text-white">
        <div class="relative z-10">
            <div class="text-lg sm:text-xl lg:text-[24px] font-extrabold mb-3 sm:mb-4">Premium subscription benefits</div>
            <div class="space-y-0">
                @foreach ($facilities as $feature)
                    <div class="flex items-center gap-1">
                        <img class="h-[24px] w-[24px]" src="{{ asset('assets/check.svg') }}" alt="check">
                        <span class="text-[12px] font-normal">{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="absolute right-2 bottom-[-40px] hidden lg:block">
            <img class="w-[300px] h-[250px]" src="{{ asset('assets/premium-banner.svg') }}" alt="premium">
        </div>
        <div class="absolute right-[-250px] bottom-[-150px] hidden lg:block">
            <img class="" src="{{ asset('assets/effects/banner-smoke.svg') }}" alt="premium">
        </div>
        <div class="absolute left-[-250px] top-[-250px] hidden lg:block">
            <img class="" src="{{ asset('assets/effects/banner-smoke.svg') }}" alt="premium">
        </div>
        <div class="absolute left-[-250px] top-[-250px] hidden lg:block">
            <img class="" src="{{ asset('assets/effects/banner-smoke.svg') }}" alt="premium">
        </div>
    </div>

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
                            <span class="text-green-600 font-normal ml-1">{{ $appliedCodeName }} - {{$appliedCodeDiscount}}% discount applied</span>
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

    <div class="relative">
        <div>
            <div class="grid gap-3 mt-4 sm:gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <div
                        class="relative rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 pt-10 shadow-sm hover:shadow-md transition-shadow">
                        @if ((float) $plan['discount'] > 0 || $plan['credit_price'])
                            <div class="absolute top-2 right-2 flex flex-col-2 items-end gap-1">
                                @if ((float) $plan['discount'] > 0)
                                    <div
                                        class="rounded-full bg-[#FD4D00] px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold text-white shadow-[0_0_10px_rgba(253,77,0,0.6)] hover:shadow-[0_0_18px_rgba(253,77,0,0.9)] transition duration-300 animate-[pulse_2s_ease-in-out_infinite]">
                                        {{ rtrim(rtrim(number_format($plan['discount'], 2, '.', ''), '0'), '.') }}%
                                    </div>
                                @endif
                                @if ($plan['credit_price'])
                                    <div
                                        class="flex items-center gap-1 rounded-full bg-[#FD4D00]    px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-bot-icon lucide-bot w-4 h-4">
                                            <path d="M12 8V4H8" />
                                            <rect width="16" height="12" x="4" y="8" rx="2" />
                                            <path d="M2 14h2" />
                                            <path d="M20 14h2" />
                                            <path d="M15 13v2" />
                                            <path d="M9 13v2" />
                                        </svg>
                                        {{ number_format($plan['credit_price'],0) }} uzs
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="flex items-start justify-between mb-3 sm:mb-4">
                            <div class="text-base sm:text-lg font-extrabold text-slate-900">{{ $plan['name'] }}</div>

                        </div>
                        <div class="flex flex-wrap items-baseline justify-between gap-y-2 mb-4">
                            <div class="flex flex-col items-baseline gap-x-2 relative">
                                @if ((float) $plan['discount'] > 0)
                                    <div
                                        class="text-base sm:text-lg font-semibold text-[#FD4D00] whitespace-nowrap animate-bounce">
                                        {{ number_format($plan['discounted_price'], 0, '.', ',') }} UZS
                                    </div>

                                    <div
                                        class="absolute text-xs sm:text-sm text-slate-400 line-through whitespace-nowrap animate-bounce"
                                        style="top: 1.2rem">
                                        {{ number_format($plan['price'], 0, '.', ',') }} UZS
                                    </div>
                                @else
                                    <div class="text-base sm:text-lg font-semibold text-[#FD4D00] whitespace-nowrap">
                                        {{ number_format($plan['price'], 0, '.', ',') }} UZS
                                    </div>
                                @endif
                            </div>
                            <div class="text-sm text-slate-500 whitespace-nowrap">
                                {{ $plan['day'] }} days
                            </div>
                        </div>
                            @if(auth()->check())
                                @if($plan['discount'] == 100)
                                    <button type="button" wire:key="plan-btn-{{ $plan['id'] }}-free"
                                            wire:click="freePlanActivate({{ $plan['id'] }})"
                                            class=" w-full rounded-[12px] bg-orange-50 px-4 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-[#FD4D00] hover:text-white transition-colors">
                                        Buy
                                    </button>
                                @else
                                    <button type="button" wire:key="plan-btn-{{ $plan['id'] }}-paid"
                                            data-subscription-btn data-plan-id="{{ $plan['id'] }}"
                                            data-plan-price="{{ $plan['discounted_price'] }}"
                                            wire:click="selectPlan({{ $plan['id'] }})"
                                            class="subscribe-btn w-full rounded-[12px] bg-orange-50 px-4 py-3 text-sm font-semibold text-[#FD4D00] hover:bg-[#FD4D00] hover:text-white transition-colors">
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
        </div>

        <input type="hidden" id="selected-plan-id" value="{{ $selectedPlanId }}">
    </div>

    <div id="payment-modal" wire:ignore.self
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        data-payment-modal>
        <div
            class="relative w-full max-w-md rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <button data-payment-close
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-900 hover:bg-slate-200 transition-colors">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <div class="mb-4 sm:mb-6 pr-8 sm:pr-12">
                <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Payment Method</div>
            </div>

            <div class="mb-6 flex gap-3 py-1 justify-center">
                <button wire:click="purchase('payme')"
                    class="flex-1 rounded-2xl border-2 border-slate-200 bg-white px-4 py-6 hover:border-slate-300 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ asset('assets/payme.svg') }}" alt="payme">
                    </div>
                </button>

                <button wire:click="purchase('click')"
                    class="flex-1 rounded-2xl border-2 border-slate-200 bg-white px-4 py-6 hover:border-slate-300 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-center">
                        <img class="h-8 w-auto" src="{{ asset('assets/click.svg') }}" alt="click">
                    </div>
                </button>
            </div>

        </div>
    </div>

</div>


<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-payment-plan', (data) => {
            window.open(data.url, '_blank');
        });
    });
</script>
