<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>

    @php
        $asset = fn ($p) => asset("assets/{$p}");
        $authAsset = fn ($p) => asset("assets/auth/{$p}");
    @endphp

    <div class="min-h-screen overflow-hidden bg-white lg:flex">
        {{-- Left promo carousel --}}
        <div class="hidden lg:flex lg:w-1/2 lg:flex-col p-2">
            <div class="relative flex min-h-screen flex-col overflow-hidden bg-orange-50 rounded-xl">
                <div class="px-8 py-8">
                    <div class="flex items-center">
                        <div class="p-1">
                            <img class="h-8 w-auto" src="{{ asset('logo/logo.png') }}" alt="TM">
                        </div>
                    </div>
                </div>

                <div class="relative flex flex-1 flex-col px-10 pb-10 pt-2" data-auth-carousel data-interval="5000" data-initial-slide="1">
                    <div class="relative flex-1 overflow-hidden">
                        @livewire('user.auth.step-page-livewire')
                    </div>

                    {{-- Dots --}}
                    <div class="mt-6 flex items-center justify-center gap-2">
                        <button type="button" class="h-2 w-2 rounded-full bg-slate-300" data-auth-carousel-to="0" aria-label="Slide 1"></button>
                        <button type="button" class="h-2 w-2 rounded-full bg-orange-600" data-auth-carousel-to="1" aria-label="Slide 2"></button>
                        <button type="button" class="h-2 w-2 rounded-full bg-slate-300" data-auth-carousel-to="2" aria-label="Slide 3"></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right SMS code form --}}
        <div class="flex min-w-0 w-full items-center justify-center lg:w-1/2">
            <div class="w-full max-w-md min-w-0 px-4 py-10 sm:px-6 lg:px-12">
                {{-- Mobile header (since left promo is hidden on mobile) --}}
                <div class="mb-8 flex items-center justify-center lg:hidden">
                    <div class="rounded-xl bg-white/60 p-3 shadow-sm">
                        <img class="h-16 w-auto" src="{{ asset('logo/logo.png') }}" alt="TM">
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-3xl font-extrabold text-slate-900 break-words">{{$type ? 'Enter the SMS code' : 'Enter the Email verification code'}}</div>
                </div>

                @if($type)
                    <div class="mt-4 text-center text-md text-slate-600 break-words font-regular">
                        We have sent an SMS notification to<br />
                        <span class="font-regular text-slate-900">{{ $maskedPhone }}</span>
                    </div>
                @else
                    <div class="mt-4 text-center text-md text-slate-600 break-words font-regular">
                        We have sent an Code to<br />
                        <span class="font-regular text-slate-900">{{ $maskedEmail }}</span>
                    </div>
                @endif


                <form class="mt-8" action="{{ route('confirm.check') }}" method="post" data-sms-code-form>
                    @csrf
                    {{-- SMS Code Inputs --}}
                    <div class="flex items-center justify-center gap-4" data-code-inputs>
                        @for ($i = 0; $i < 4; $i++)
                            <input type="text" name="code[]" data-code-input data-index="{{ $i }}"
                                maxlength="1" inputmode="numeric" pattern="[0-9]"
                                class="h-12 w-12 flex-shrink-0 rounded-full border-2 border-slate-200 bg-white text-center text-lg font-semibold text-slate-900 shadow-sm transition-all focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-500/10"
                                autocomplete="off" />
                        @endfor
                    </div>

                    {{-- Hidden input for full code --}}
                    <input type="hidden" name="sms_code" data-code-value />
                    @error('sms_code')
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="resend-message" class="mt-4"></div>

                    {{-- Send code via Telegram button --}}
                    @if($type)

                    <div class="mt-12">
                        <a href="https://t.me/Tm_school_sms_bot" target="_blank" rel="noopener noreferrer"
                            class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#FFE9E0] px-6 py-2 text-sm font-semibold text-[#FD4D00] shadow-sm hover:bg-[#FFD5CC] transition-colors">
                            Send code via Telegram
                        </a>
                    </div>

                    @endif

                    <div class="mt-4 text-center text-sm text-slate-600">
                        Didn't receive the code?
                        <button type="button" id="resend-verification-code" class="font-medium text-orange-600 hover:underline">
                            Resend Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            // SMS Code Input Handler
            const codeInputs = document.querySelectorAll('[data-code-input]');
            const codeValueInput = document.querySelector('[data-code-value]');

            if (!codeInputs.length) return;

            codeInputs.forEach((input, index) => {
                // Store actual value in data attribute
                input.dataset.actualValue = '';

                // Only allow numbers
                input.addEventListener('input', (e) => {
                    const value = e.target.value.replace(/[^0-9]/g, '').slice(0, 1);

                    if (value) {
                        // Store actual value
                        input.dataset.actualValue = value;
                        e.target.classList.add('border-slate-300');

                        // Move to next input
                        if (index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                    } else {
                        input.dataset.actualValue = '';
                        e.target.value = '';
                        e.target.classList.remove('border-slate-300');
                    }

                    updateCodeValue();
                });

                // Handle backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace') {
                        if (input.dataset.actualValue) {
                            // Clear current
                            input.dataset.actualValue = '';
                            input.value = '';
                            input.classList.remove('border-slate-300');
                            updateCodeValue();
                        } else if (index > 0) {
                            // Move to previous and clear it
                            codeInputs[index - 1].focus();
                            codeInputs[index - 1].dataset.actualValue = '';
                            codeInputs[index - 1].value = '';
                            codeInputs[index - 1].classList.remove('border-slate-300');
                            updateCodeValue();
                        }
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 4);
                    pasted.split('').forEach((char, i) => {
                        if (codeInputs[index + i]) {
                            codeInputs[index + i].dataset.actualValue = char;
                            codeInputs[index + i].value = char;
                            codeInputs[index + i].classList.add('border-slate-300');
                        }
                    });
                    updateCodeValue();
                    // Focus last filled or next empty
                    const lastFilled = Math.min(index + pasted.length - 1, codeInputs.length - 1);
                    const nextEmpty = Math.min(index + pasted.length, codeInputs.length - 1);
                    codeInputs[nextEmpty]?.focus();
                });

                // Focus management - show orange border when focused
                input.addEventListener('focus', () => {
                    input.classList.add('border-orange-400', 'ring-4', 'ring-orange-500/10');
                });

                input.addEventListener('blur', () => {
                    input.classList.remove('border-orange-400', 'ring-4', 'ring-orange-500/10');
                });
            });

            function updateCodeValue() {
                const code = Array.from(codeInputs)
                    .map(input => input.dataset.actualValue || '')
                    .join('');
                if (codeValueInput) {
                    codeValueInput.value = code;
                }
                if (code.length === 4) {
                    document.querySelector('[data-sms-code-form]').submit();
                }
            }

            // Focus first input on load
            if (codeInputs[0]) {
                setTimeout(() => codeInputs[0].focus(), 100);
            }

            // Left auth carousel
            document.querySelectorAll('[data-auth-carousel]').forEach((root) => {
                const track = root.querySelector('[data-auth-carousel-track]');
                const dots = Array.from(root.querySelectorAll('[data-auth-carousel-to]'));
                if (!track || !root) return;
                const slidesCount = track.children.length;
                if (slidesCount <= 1) return;

                const intervalMs = Number(root.getAttribute('data-interval') || 5000);
                const initialSlide = Number(root.getAttribute('data-initial-slide') || 0);
                let index = initialSlide;
                let timer = null;

                const setActive = (next) => {
                    index = (next + slidesCount) % slidesCount;
                    const slidePct = 100 / slidesCount;
                    track.style.transform = `translate3d(${-index * slidePct}%, 0, 0)`;
                    dots.forEach((d, i) => {
                        d.classList.toggle('bg-orange-600', i === index);
                        d.classList.toggle('bg-slate-300', i !== index);
                    });
                };

                const start = () => {
                    if (!intervalMs || intervalMs < 1000) return;
                    stop();
                    timer = window.setInterval(() => setActive(index + 1), intervalMs);
                };
                const stop = () => {
                    if (timer) window.clearInterval(timer);
                    timer = null;
                };

                dots.forEach((d) => {
                    d.addEventListener('click', () => {
                        const to = Number(d.getAttribute('data-auth-carousel-to'));
                        if (Number.isNaN(to)) return;
                        setActive(to);
                        start();
                    });
                });

                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);

                setActive(initialSlide);
                start();
            });

            document.getElementById('resend-verification-code').addEventListener('click', function() {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const messageDiv = document.getElementById('resend-message');
                messageDiv.innerHTML = '';
                fetch('{{ route("resend.verification.code") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': token
                    },
                    body: '_token=' + encodeURIComponent(token),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        messageDiv.innerHTML = '<div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-700">' + data.success + '</div>';
                    } else {
                        messageDiv.innerHTML = '<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">' + data.error + '</div>';
                    }
                })
                .catch(error => {
                    messageDiv.innerHTML = '<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">Network error</div>';
                });
            });
        })();
    </script>

</body>
</html>
