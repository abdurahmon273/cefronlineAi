<div class="lg:col-span-3 home-aside">

    @if(auth()->check())
        @if(!auth()->user()->activePremium())

        <div class="highlight-element mb-4 flex flex-col text-center gap-4 rounded-2xl sm:rounded-3xl bg-[#FFE9E0] px-4 sm:px-6 py-5 sm:py-6">
            <div>
                <div class="inline-flex h-[40px] w-[40px] items-center justify-center rounded-2xl">
                    <img class="h-[32px] w-[32px]" src="{{ asset('assets/crown.svg') }}" alt="crown">
                </div>
                <div class="mt-3 text-[16px] font-semibold text-orange-600">
                    Purchase a membership<br />for more benefits
                </div>
            </div>
            <a href="{{ route('user.market') }}"
               class="rounded-[12px] h-[40px] z-0 bg-orange-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-700">
                Upgrade
            </a>
        </div>
        @endif

    @else

            <div class="highlight-element mb-4 flex flex-col text-center gap-4 rounded-2xl sm:rounded-3xl bg-[#FFE9E0] px-4 sm:px-6 py-5 sm:py-6">
                <div>
                    <div class="flex items-center justify-center">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.125 15.09V10.125C28.125 7.43968 27.0583 4.86435 25.1595 2.96554C23.2606 1.06674 20.6853 0 18 0C15.3147 0 12.7393 1.06674 10.8405 2.96554C8.94174 4.86435 7.875 7.43968 7.875 10.125C7.875 10.6223 8.07254 11.0992 8.42417 11.4508C8.77581 11.8025 9.25272 12 9.75 12C10.2473 12 10.7242 11.8025 11.0758 11.4508C11.4275 11.0992 11.625 10.6223 11.625 10.125C11.625 8.43424 12.2966 6.81274 13.4922 5.61719C14.6877 4.42165 16.3092 3.75 18 3.75C19.6908 3.75 21.3123 4.42165 22.5078 5.61719C23.7034 6.81274 24.375 8.43424 24.375 10.125V13.5C24.375 13.6989 24.296 13.8897 24.1553 14.0303C24.0147 14.171 23.8239 14.25 23.625 14.25H11.25C9.26088 14.25 7.35322 15.0402 5.9467 16.4467C4.54018 17.8532 3.75 19.7609 3.75 21.75V28.5C3.75 29.4849 3.94399 30.4602 4.3209 31.3701C4.69781 32.2801 5.25026 33.1069 5.9467 33.8033C7.35322 35.2098 9.26088 36 11.25 36H24.75C26.7391 36 28.6468 35.2098 30.0533 33.8033C31.4598 32.3968 32.25 30.4891 32.25 28.5V21.75C32.243 20.3675 31.8541 19.0139 31.1262 17.8386C30.3982 16.6633 29.3596 15.7121 28.125 15.09ZM21 24.75C21 25.3433 20.8241 25.9234 20.4944 26.4167C20.1648 26.9101 19.6962 27.2946 19.148 27.5216C18.5999 27.7487 17.9967 27.8081 17.4147 27.6924C16.8328 27.5766 16.2982 27.2909 15.8787 26.8713C15.4591 26.4518 15.1734 25.9172 15.0576 25.3353C14.9419 24.7533 15.0013 24.1501 15.2284 23.602C15.4554 23.0538 15.8399 22.5852 16.3333 22.2556C16.8266 21.9259 17.4067 21.75 18 21.75C18.7956 21.75 19.5587 22.0661 20.1213 22.6287C20.6839 23.1913 21 23.9544 21 24.75Z" fill="#FD4D00"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-[16px] font-semibold text-orange-600">
                        Register to unlock more
                        functionalities
                    </div>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('login') }}"
                       class="rounded-[12px] h-[40px] w-full z-0 border-2 border-orange-600 bg-[#FFE9E0] px-5 py-2 text-sm font-semibold text-orange-600 shadow-sm hover:bg-orange-600 hover:text-white">
                        login
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-[12px] h-[40px] w-full z-0 bg-orange-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-700">
                        Register
                    </a>
                </div>

            </div>

    @endif

        <div class=" rounded-2xl sm:rounded-3xl bg-[#E1FFED] px-4 sm:px-6 py-6 sm:py-7 text-center">

            <h3 class="text-[16px] font-semibold text-gray-900">
                Download mobile app
            </h3>

{{--            <p class="mt-2 text-sm text-gray-600">--}}
{{--                Scan the QR code to install the app on your device--}}
{{--            </p>--}}

            <div class="mt-6 flex flex-wrap justify-center gap-4">
                <div class="flex justify-center basis-full md:basis-auto">
                    <img
                        src="{{ asset('logo/playMarketQr.png') }}"
                        alt="Play Market QR"
                        class="h-28 w-28 object-contain rounded-xl shadow-sm"
                    >
                </div>

                <div class="flex justify-center basis-full md:basis-auto">
                    <img
                        src="{{ asset('logo/appStoreQr.jpg') }}"
                        alt="App Store QR"
                        class="h-28 w-28 object-contain rounded-xl shadow-sm"
                    >
                </div>
            </div>

        </div>

</div>
