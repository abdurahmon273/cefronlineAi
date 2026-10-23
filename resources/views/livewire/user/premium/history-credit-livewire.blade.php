<div class="w-full rounded-[28px] bg-white p-6 sm:p-7">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-[28px]">
            Buy credits
        </h2>

    </div>

    <div class="space-y-5">
        <div class="space-y-2">
            <label for="credit-input" class="block text-sm font-semibold text-slate-700">
                Credits <span class="text-xs font-normal text-slate-400">(min ${{$min_price}})</span>
            </label>

            <input
                type="number"
                wire:model.live="price"
                id="credit-input"
                min="{{$min_price}}"
                step="0.01"
                placeholder="0.0000"
                class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base font-semibold text-slate-900 outline-none transition focus:border-[#FD4D00] focus:bg-white focus:ring-4 focus:ring-orange-100"
            />

            @error('price')
            <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <div class="flex items-center justify-between gap-4">
                <span class="text-md font-medium text-slate-500">UZS amount</span>
                <span class="text-md  tracking-tight text-slate-600">
                    {{number_format($value,1,'.',',')}}
                </span>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <p class="mb-3 text-sm font-semibold text-slate-700">Quick select</p>

        <div class="grid grid-cols-3 gap-3">
            @foreach($defaultPriceData as $price)
                <button type="button"
                        data-preset-credit
                        class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#FD4D00] hover:bg-orange-50 hover:text-[#FD4D00]">
                    {{ $price }}
                </button>
            @endforeach
             </div>
    </div>

    <div class="mt-7">
        <button wire:click="buyNow"
            id="credits-buy-btn"
            class="flex h-14 w-full items-center justify-center rounded-2xl bg-[#FD4D00] px-4 text-base font-bold text-white transition hover:bg-[#E64500]"
        >
            Buy now
        </button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const buttons = document.querySelectorAll("[data-preset-credit]");

        buttons.forEach(btn => {
            btn.addEventListener("click", () => {

                // button text dan qiymat olish
                let value = btn.innerText.replace('$','').trim();

                // input ga yozish
                input.value = value;

                // Livewire update qilish
                input.dispatchEvent(new Event('input'));

                // active style
                buttons.forEach(b=>{
                    b.classList.remove("border-[#FD4D00]","bg-orange-50","text-[#FD4D00]");
                });

                btn.classList.add("border-[#FD4D00]","bg-orange-50","text-[#FD4D00]");

            });
        });

        const input = document.getElementById("credit-input");
        const min = 0.03;

        // minus yozishni bloklash
        input.addEventListener("keydown", (e) => {
            if (e.key === "-" || e.key === "e") {
                e.preventDefault();
            }
        });

        input.addEventListener("input", () => {
            if (input.value < 0) {
                input.value = '';
                return;
            }
        });

    });
</script>
