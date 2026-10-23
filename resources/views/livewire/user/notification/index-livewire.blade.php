<div>
    @props([
        'variant' => 'signed_out', // signed_out | signed_in | menu_open
        'userName' => 'Sukhrob Khurramov',
    ])
    @php
        $signedOut = $variant === 'signed_out';
        $defaultOpen = $variant === 'menu_open';
        $asset = fn($p) => asset("{$p}");
    @endphp
    <div class="mb-4 sm:mb-6 lg:mb-8 flex items-center justify-between">
        <div class="text-lg sm:text-xl lg:text-2xl xl:text-[32px] font-extrabold text-slate-900">Notifications</div>
    </div>

    @forelse ($newNotifications as $notification)
        <div class="mb-3 sm:mb-4 p-4 sm:p-5 lg:p-6 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow max-w-4xl mx-auto cursor-pointer"
            wire:click="openNotificationModal({{ $notification->id }})">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="flex-shrink-0">
                    @if ($notification->web_image->original_url ?? false)
                        <img src="{{ $notification->web_image->original_url }}"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover" alt="Notification">
                    @else
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    @if (optional($notification->pivot)->read_at)
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 16l2 2 4-4"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-5 5v-5zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                        <div class="font-semibold text-base sm:text-lg text-gray-900">
                            {{ $notification->title }}
                        </div>
                        @if (!optional($notification->pivot)->read_at)
                            <button wire:click.stop="markAsRead({{ $notification->id }})"
                                class="ml-2 px-3 py-1 text-xs sm:text-sm bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition-colors flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </button>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-green-600 ml-2">
                                <path d="M18 6 7 17l-5-5" />
                                <path d="m22 10-7.5 7.5L13 16" />
                            </svg>
                        @endif
                    </div>
                    <div class="text-sm sm:text-base text-gray-600 leading-relaxed max-h-48 overflow-y-auto ">
                        {!! $notification->text !!}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-2 lg:py-4">
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <img src="{{ $asset('assets/bell.svg') }}" alt="Notifications"
                    class="h-24 w-24 sm:h-32 sm:w-32 lg:h-[160px] lg:w-[160px] opacity-75">
            </div>
            <div class="text-center">
                <div class="text-base sm:text-lg lg:text-xl text-slate-600 font-medium mb-2">No notifications yet</div>
                <div class="text-sm sm:text-base text-slate-500">This place is still empty</div>
            </div>
        </div>
    @endforelse

</div>
