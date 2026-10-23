<div
    x-data="{ activeTab: @entangle('tab').live, pendingTab: null }"
    x-effect="if (activeTab === pendingTab) pendingTab = null"
    class="lg:col-span-9 bg-[#fafafa] rounded-2xl sm:rounded-[24px] p-4 sm:p-6 lg:p-[32px]">
    <div class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900 mb-4 sm:mb-6">Profile settings</div>

    {{-- Tabs --}}
    <div class="mb-4 sm:mb-6 w-full max-w-[720px]">

        <div class="w-full rounded-2xl bg-[#F0F0F0] p-1 text-[16px]" data-tabs>
            <div class="grid grid-cols-2 gap-1 sm:grid-cols-4">
                <button type="button"
                        wire:click="setTab('personal')"
                        x-on:click="pendingTab = 'personal'"
                        aria-selected="{{ $tab === 'personal' ? 'true' : 'false' }}"
                        class="rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition {{ $tab === 'personal' ? 'border-none bg-white text-orange-600' : 'border-none font-regular text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                    Personal
                </button>

                <button type="button"
                        wire:click="setTab('security')"
                        x-on:click="pendingTab = 'security'"
                        aria-selected="{{ $tab === 'security' ? 'true' : 'false' }}"
                        class="rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition {{ $tab === 'security' ? 'border-none bg-white text-orange-600' : 'border-none font-regular text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                    Security
                </button>

                <button type="button"
                        wire:click="setTab('premium')"
                        x-on:click="pendingTab = 'premium'"
                        aria-selected="{{ $tab === 'premium' ? 'true' : 'false' }}"
                        class="rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition {{ $tab === 'premium' ? 'border-none bg-white text-orange-600' : 'border-none font-regular text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                    Premium
                </button>

                <button type="button"
                        wire:click="setTab('referral')"
                        x-on:click="pendingTab = 'referral'"
                        aria-selected="{{ $tab === 'referral' ? 'true' : 'false' }}"
                        class="rounded-xl border-none px-4 py-2 text-[16px] font-semibold transition {{ $tab === 'referral' ? 'border-none bg-white text-orange-600' : 'border-none font-regular text-slate-400 hover:bg-slate-50 hover:text-slate-600' }}">
                    Referral
                </button>

            </div>
        </div>
    </div>

    <div x-cloak x-show="activeTab === 'personal' || pendingTab === 'personal'" data-tab-content="personal" class="max-w-md">
        <div wire:loading.remove wire:target="setTab" class="space-y-4">

        <div class="mb-4">
            <div class="relative h-24 w-24 overflow-hidden rounded-full">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(($name ?? '') . ' ' . ($surname ?? '')) }}&background=1f2937&color=fff&size=256"
                     alt="Profile"
                     class="h-full w-full object-cover">
            </div>
        </div>

        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">Name</label>
            <input type="text"
                   wire:model.defer="name"
                   class="relative w-full h-12 rounded-2xl bg-white px-4 py-2 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4] focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none overflow-visible @error('name') border-red-500 focus:border-red-500 @enderror" />

            @error('name')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">Surname</label>
            <input type="text"
                   wire:model.defer="surname"
                   class="relative w-full h-12 rounded-2xl bg-white px-4 py-2 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4] focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none overflow-visible @error('surname') border-red-500 focus:border-red-500 @enderror" />

            @error('surname')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">Phone number or Email</label>

            <div class="flex h-12 items-center gap-2 rounded-2xl border-[1.5px] border-[#D4D4D4] bg-white px-4 py-2 focus-within:border-2 focus-within:border-[#FD4D00] @error('mobil_number') border-red-500 focus-within:border-red-500 @enderror w-full">

                <input type="text" disabled data-mask="number_or_email"
                       wire:model.defer="mobil_number_or_email"
                       class="w-full bg-transparent text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] focus:outline-none" />
            </div>

            @error('mobil_number')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2 w-full">
            <button
                type="button"
                wire:click="saveFullName"
                wire:target="saveFullName"
                wire:loading.attr="disabled"
                class="rounded-2xl bg-[#FD4D00] px-6 sm:px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#E64500] disabled:opacity-70 disabled:cursor-not-allowed w-full inline-flex items-center justify-center gap-2">

                {{-- Spinner faqat loadingda --}}
                <svg
                    wire:loading
                    wire:target="saveFullName"
                    class="h-4 w-4 animate-spin text-white"
                    viewBox="0 0 24 24"
                    fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4l3-3-3-3v4a10 10 0 00-10 10h4z"></path>
                </svg>

                {{-- Text --}}
                <span wire:loading.remove wire:target="saveFullName">Save</span>
                <span wire:loading wire:target="saveFullName">Saving...</span>
            </button>
        </div>


            @if (session()->has('success_fullname'))
                <div class="p-3 rounded-2xl bg-green-50 text-green-700 text-sm w-full">
                    {{ session('success_fullname') }}
                </div>
            @endif
        </div>

        <div wire:loading.block wire:target="setTab" x-show="pendingTab === 'personal'" class="space-y-4">
            <div class="h-24 w-24 animate-pulse rounded-full bg-slate-200"></div>
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-12 animate-pulse rounded-2xl bg-[#FFD7C7]"></div>
        </div>
    </div>

    <div x-cloak x-show="activeTab === 'security' || pendingTab === 'security'" data-tab-content="security" class="max-w-md">
        <div wire:loading.remove wire:target="setTab" class="space-y-4">


        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">Previous password</label>
            <div class="relative overflow-hidden rounded-2xl w-full">
                <input type="password"
                       wire:model.defer="password"
                       data-password
                       class="relative w-full h-12 rounded-2xl bg-white px-4 py-2 pr-12 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4] focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none @error('password') border-red-500 focus:border-red-500 @enderror" />
                <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 z-10"
                        data-password-toggle>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon>
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                    <svg class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon-open>
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>

            @error('password')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">New password</label>
            <div class="relative overflow-hidden rounded-2xl w-full">
                <input type="password"
                       wire:model.defer="new_password"
                       data-password
                       class="relative w-full h-12 rounded-2xl bg-white px-4 py-2 pr-12 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4] focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none @error('new_password') border-red-500 focus:border-red-500 @enderror" />
                <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 z-10"
                        data-password-toggle>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon>
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                    <svg class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon-open>
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>

            @error('new_password')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        {{-- Repeat New Password --}}
        <div class="relative">
            <label class="text-sm leading-none text-black font-medium z-10 whitespace-nowrap mb-2 block">Repeat new password</label>
            <div class="relative overflow-hidden rounded-2xl w-full">
                <input type="password"
                       wire:model.defer="confirm_password"
                       data-password
                       class="relative w-full h-12 rounded-2xl bg-white px-4 py-2 pr-12 text-[14px] leading-4 text-[#3A3A3A] placeholder:text-[#A1A1A1] outline-none border-[1.5px] border-[#D4D4D4] focus:border-2 focus:border-[#FD4D00] focus:text-[#3A3A3A] focus:outline-none @error('confirm_password') border-red-500 focus:border-red-500 @enderror" />
                <button type="button"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 z-10"
                        data-password-toggle>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon>
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                    <svg class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-password-icon-open>
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>

            @error('confirm_password')
            <p class="mt-1 text-sm text-red-600 w-full">{{ $message }}</p>
            @enderror
        </div>

        {{-- Save Button (loading) --}}
        <div class="pt-2 w-full">
            <button
                type="button"
                wire:click="saveNewPassword"
                wire:target="saveNewPassword"
                wire:loading.attr="disabled"
                class="rounded-2xl bg-[#FD4D00] px-6 sm:px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#E64500] disabled:opacity-70 disabled:cursor-not-allowed w-full inline-flex items-center justify-center gap-2">

                <svg
                    wire:loading
                    wire:target="saveNewPassword"
                    class="h-4 w-4 animate-spin text-white"
                    viewBox="0 0 24 24"
                    fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4l3-3-3-3v4a10 10 0 00-10 10h4z"></path>
                </svg>

                <span wire:loading.remove wire:target="saveNewPassword">Save</span>
                <span wire:loading wire:target="saveNewPassword">Saving...</span>
            </button>
        </div>
            @if (session()->has('success_password'))
                <div class="p-3 rounded-2xl bg-green-50 text-green-700 text-sm w-full">
                    {{ session('success_password') }}
                </div>
            @endif
        </div>

        <div wire:loading.block wire:target="setTab" x-show="pendingTab === 'security'" class="space-y-4">
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-20 animate-pulse rounded-[24px] bg-white"></div>
            <div class="h-12 animate-pulse rounded-2xl bg-[#FFD7C7]"></div>
        </div>
    </div>

    <div x-cloak x-show="activeTab === 'premium' || pendingTab === 'premium'" data-tab-content="premium" class="w-full max-w-none">
        <div wire:loading.remove wire:target="setTab" class="space-y-5">

        {{-- Premium summary card --}}
            <div class="w-full rounded-[28px] bg-[#FFE9E0] p-5 sm:p-6">
                @if($hasPremium)
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-2xl bg-white/70">
                                <svg class="h-8 w-8 sm:h-10 sm:w-10 text-[#FD4D00]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l2.35 4.76 5.25.76-3.8 3.7.9 5.23L12 14.77 7.3 16.45l.9-5.23-3.8-3.7 5.25-.76L12 2zm-5 17h10a1 1 0 110 2H7a1 1 0 110-2z"/>
                                </svg>
                            </div>

                            <div>
                                <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">{{$premiumToUser->plan->name}}</div>
                                <div class="mt-2 inline-flex items-center rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-[#FD4D00]">
                                    Active
                                </div>
                            </div>
                        </div>

                        <div class="text-left lg:text-right">
                            <div class="flex flex-col items-end ">
                                <div class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900">{{number_format((($premiumToUser->price/100) * (100-$premiumToUser->discount)) ,0,'.',',')}} UZS</div>
                                @if($premiumToUser->discount>0)
                                    <div class=" text-sm sm:text-sm font-extrabold line-through text-slate-700 ">{{number_format($premiumToUser->price,0,'.',',')}} UZS</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-2xl bg-white/70">
                                <svg class="h-8 w-8 sm:h-10 sm:w-10 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l2.35 4.76 5.25.76-3.8 3.7.9 5.23L12 14.77 7.3 16.45l.9-5.23-3.8-3.7 5.25-.76L12 2zm-5 17h10a1 1 0 110 2H7a1 1 0 110-2z"/>
                                </svg>
                            </div>

                            <div>
                                <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">No active plan</div>
                                <div class="mt-2 inline-flex items-center rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-slate-500">
                                    Inactive
                                </div>
                            </div>
                        </div>

                        <div class="text-left lg:text-right">
                            <div class="text-sm text-slate-500">Get premium to unlock features</div>
                            <a href="{{route('user.market')}}"
                               type="button"
                               class="mt-3 inline-flex items-center justify-center rounded-2xl bg-[#FD4D00] px-5 py-3 text-sm font-semibold text-white hover:bg-[#E64500] transition">
                                Upgrade
                            </a>
                        </div>
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3">
                    @if($hasPremium)

                        <div class="rounded-2xl bg-white/70 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">Days</div>
                            <div class="mt-1 text-sm sm:text-base font-semibold text-slate-900">
                                {{$premiumToUser->plan->day}}</div>
                        </div>
                        <div class="rounded-2xl bg-white/70 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">Expires On</div>
                            <div class="mt-1 text-sm sm:text-base font-semibold text-slate-900">
                                {{\Illuminate\Support\Carbon::parse($premiumToUser->end_time)->locale('uz')->translatedFormat('j F, H:i')}}</div>
                        </div>
                    @elseif($premiumToUser)

                        <div class="rounded-2xl bg-white/70 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">Last plan</div>
                            <div class="mt-1 text-sm sm:text-base font-semibold text-slate-900">{{$premiumToUser->plan->name}}</div>
                        </div>

                        <div class="rounded-2xl bg-white/70 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">Expired</div>
                            <div class="mt-1 text-sm sm:text-base font-semibold text-slate-900">
                                {{\Illuminate\Support\Carbon::parse($premiumToUser->end_time)->locale('uz')->translatedFormat('j F, H:i')}}
                            </div>
                        </div>
                    @endif

                    @if($lastBalanceUpdate)
                        <div class="rounded-2xl bg-white/70 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400">Last Credit Update</div>
                            <div class="mt-1 text-sm sm:text-base font-semibold text-green-500">{{number_format($lastBalanceUpdate,0,'.',' ')}} uzs</div>
                        </div>
                    @endif


                    <div class="rounded-2xl bg-white/70 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-slate-400">Balance</div>
                        <div class="mt-1 text-sm sm:text-base font-semibold text-slate-900">{{number_format($user->balance,0,'.',' ')}} uzs</div>
                    </div>
                </div>
            </div>


        {{-- Balance history --}}
        @if($persentageData['success'])
        <div>
            <div class="mb-3">
                <div class="text-lg sm:text-xl font-bold text-slate-900">Balance Used</div>
            </div>


            <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm mb-5">
                <div class="mb-3 flex items-center justify-between gap-3 text-sm sm:text-base">
                    <div class="font-semibold text-slate-900">
                        Used <span class="text-red-500">{{$persentageData['used']}}%</span>
                    </div>
                    <div class="font-semibold text-slate-900">
                        Remaining <span class="text-green-600">{{$persentageData['exists']}}    %</span>
                    </div>
                </div>

                <div class="h-5 w-full overflow-hidden rounded-xl bg-slate-100">
                    <div class="flex h-full w-full">
                        <div class="h-full bg-red-500" style="width: {{$persentageData['used']}}%;"></div>
                        <div class="h-full bg-green-500" style="width: {{$persentageData['exists']}}%;"></div>
                    </div>
                </div>
            </div>


        </div>
        @endif
        </div>

        <div wire:loading.block wire:target="setTab" x-show="pendingTab === 'premium'" class="space-y-5">
            <div class="rounded-[28px] bg-[#FFE9E0] p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="h-16 w-16 animate-pulse rounded-2xl bg-white/80 sm:h-20 sm:w-20"></div>
                        <div class="space-y-3">
                            <div class="h-8 w-48 animate-pulse rounded-xl bg-white/80"></div>
                            <div class="h-6 w-24 animate-pulse rounded-full bg-white/80"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-8 w-32 animate-pulse rounded-xl bg-white/80"></div>
                        <div class="h-5 w-24 animate-pulse rounded-xl bg-white/70"></div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <div class="h-20 animate-pulse rounded-2xl bg-white/80"></div>
                    <div class="h-20 animate-pulse rounded-2xl bg-white/80"></div>
                    <div class="h-20 animate-pulse rounded-2xl bg-white/80"></div>
                    <div class="h-20 animate-pulse rounded-2xl bg-white/80"></div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="h-6 w-32 animate-pulse rounded-xl bg-slate-200"></div>
                <div class="h-28 animate-pulse rounded-[24px] bg-white"></div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="activeTab === 'referral' || pendingTab === 'referral'" data-tab-content="referral" class="w-full max-w-none">
        <div wire:loading.remove wire:target="setTab" class="space-y-5">

        <div class="rounded-[28px] border border-[#E9D7CC] bg-gradient-to-br from-[#FFF5EE] via-[#FFF9F5] to-white p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white text-[#FD4D00] shadow-sm ring-1 ring-black/5">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                            {{ number_format($referralBalance, 0, '.', ' ') }} UZS
                        </div>
                    </div>
                </div>

                <div class="w-full sm:w-[180px]">
                    <select
                        wire:model.live="selectedReferralMonth"
                        class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-[#FD4D00]">
                        @foreach($availableReferralMonths as $month)
                            <option value="{{ $month['value'] }}" {{$month['selected'] ? 'selected' : ''}}>{{ $month['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total balance</div>
                    <div class="mt-3 text-2xl font-extrabold text-black">
                        {{ number_format($totalReferralBalance, 0, '.', ' ') }} UZS
                    </div>
                </div>

                <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Available balance</div>
                    <div class="mt-3 text-2xl font-extrabold text-blue-600">
                        {{ number_format($referralBalance, 0, '.', ' ') }} UZS
                    </div>
                </div>

                <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm ring-1 ring-black/5">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Paid balance</div>
                    <div class="mt-3 text-2xl font-extrabold text-green-600">
                        {{ number_format($paidReferralBalance, 0, '.', ' ') }} UZS
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.25fr_0.75fr]">
            <div
                x-data="{ copied: false, link: @js($referralLink) }"
                class="rounded-[24px] bg-white px-5 py-5 shadow-sm ring-1 ring-black/5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4"/>
                                <path d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 20"/>
                            </svg>
                        </div>
                        <div class="text-base font-semibold text-slate-900">Copy my referral link</div>
                    </div>

                    <button
                        type="button"
                        x-on:click="
                            $refs.referralLinkInput.select();
                            $refs.referralLinkInput.setSelectionRange(0, 99999);
                            document.execCommand('copy');
                            copied = true;
                            setTimeout(() => copied = false, 1800);
                        "
                        class="inline-flex items-center justify-center rounded-2xl bg-[#FD4D00] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#E64500]">
                        <span x-show="!copied">Copy link</span>
                        <span x-show="copied" x-cloak>Copied</span>
                    </button>
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <input
                        x-ref="referralLinkInput"
                        type="text"
                        value="{{ $referralLink }}"
                        readonly
                        class="w-full truncate bg-transparent outline-none"
                    >
                </div>
            </div>

            <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm ring-1 ring-black/5">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <div class="text-base font-semibold text-slate-900">Contact admin</div>
                </div>

                @if($adminContactLink)
                    <a
                        href="{{ $adminContactLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900">
                        Open chat
                    </a>
                @else
                    <div class="mt-4 rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-center text-sm font-medium text-slate-500">
                        Admin contact
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-[24px] bg-white px-5 py-5 shadow-sm">
            @if(count($recentReferrals))
                <div class="space-y-3">
                    @foreach($recentReferrals as $referral)
                        @php
                            $fullName = $referral['fullname'] ?? 'User';
                            $status = strtolower((string) ($referral['status'] ?? 'active'));
                            $isPaid = $status === 'paid';
                        @endphp

                        <div class="flex flex-col gap-4 rounded-[22px] border border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#FFF1EA] text-sm font-bold text-[#FD4D00]">
                                    {{ strtoupper(substr($fullName, 0, 1)) }}
                                </div>

                                <div>
                                    <div class="text-base font-semibold text-slate-900">{{ $fullName }}</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <div class="text-right">
                                    <div class="text-xs font-medium uppercase tracking-[0.14em] text-slate-400">Price</div>
                                    <div class="mt-1 text-base font-bold text-slate-900">
                                        {{ number_format($referral['price'] ?? 0, 0, '.', ' ') }} UZS
                                    </div>
                                </div>

                                <div class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] {{ $isPaid ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                                    {{ $isPaid ? 'Paid' : 'Active' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($referralCount > count($recentReferrals))
                    <div class="mt-4 flex justify-center">
                        <button
                            type="button"
                            wire:click="showMoreReferrals"
                            wire:loading.attr="disabled"
                            wire:target="showMoreReferrals"
                            class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-600 transition hover:border-slate-300 hover:text-slate-900 disabled:opacity-60">
                            <span wire:loading.remove wire:target="showMoreReferrals">Show more</span>
                            <span wire:loading wire:target="showMoreReferrals">Loading...</span>
                        </button>
                    </div>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center">
                    <div class="text-base font-semibold text-slate-900">No referrals yet</div>
                </div>
            @endif
        </div>
        </div>

        <div wire:loading.block wire:target="setTab" x-show="pendingTab === 'referral'" class="space-y-5">
            <div class="rounded-[28px] border border-[#E9D7CC] bg-gradient-to-br from-[#FFF5EE] via-[#FFF9F5] to-white p-5 sm:p-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 animate-pulse rounded-2xl bg-white sm:h-20 sm:w-20"></div>
                    <div class="h-10 w-44 animate-pulse rounded-xl bg-white"></div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="h-24 animate-pulse rounded-[24px] bg-white"></div>
                    <div class="h-24 animate-pulse rounded-[24px] bg-white"></div>
                    <div class="h-24 animate-pulse rounded-[24px] bg-white"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.25fr_0.75fr]">
                <div class="h-32 animate-pulse rounded-[24px] bg-white"></div>
                <div class="h-32 animate-pulse rounded-[24px] bg-white"></div>
            </div>

            <div class="space-y-3 rounded-[24px] bg-white px-5 py-5 shadow-sm">
                <div class="h-20 animate-pulse rounded-[22px] bg-slate-100"></div>
                <div class="h-20 animate-pulse rounded-[22px] bg-slate-100"></div>
                <div class="h-20 animate-pulse rounded-[22px] bg-slate-100"></div>
            </div>
        </div>
    </div>

</div>

@once
    <script>
        (() => {
            if (window.__profileSettingPasswordToggleInit) return;
            window.__profileSettingPasswordToggleInit = true;

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-password-toggle]');
                if (!button) return;

                const wrap = button.closest('.relative');
                const input = wrap ? wrap.querySelector('[data-password]') : null;
                if (!input) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                const iconClosed = button.querySelector('[data-password-icon]');
                const iconOpen = button.querySelector('[data-password-icon-open]');

                if (iconClosed && iconOpen) {
                    iconClosed.classList.toggle('hidden', isPassword);
                    iconOpen.classList.toggle('hidden', !isPassword);
                }
            });
        })();
    </script>
@endonce
