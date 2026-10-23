{{-- News Modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm {{ $showNewsModal ? '' : 'hidden' }}"
    role="dialog" aria-modal="true" aria-labelledby="news-title" aria-describedby="news-description">
    <div wire:click.away="closeModal"
        class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-3xl shadow-2xl transform transition-all flex flex-col">

        {{-- Close Button --}}
        <button type="button" wire:click="closeModal" aria-label="Close news dialog"
            class="absolute top-4 right-4 z-10 p-2 bg-black/10 hover:bg-black/20 rounded-full text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Featured Image --}}
        <div class="relative h-32 sm:h-48 lg:h-64 bg-gray-100 shrink-0">
            <img src="{{ $selectedNews['web_image']['url'] ?? $selectedNews['image']['url'] ?? '' }}"
                alt="{{ $selectedNews['title'] ?? 'News image' }}" loading="lazy" decoding="async"
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8">
                <h3 id="news-title" class="text-xl sm:text-2xl lg:text-3xl font-bold text-white leading-tight">
                    {{ $selectedNews['title'] ?? '' }}
                </h3>
            </div>
        </div>

        {{-- Content --}}
        <div id="news-description" class="p-4 sm:p-5 lg:p-8 space-y-4 sm:space-y-6">
            <div class="text-gray-600 text-base sm:text-lg leading-relaxed">
                {!! $selectedNews['description'] ?? '' !!}
            </div>

            @if($selectedNews['link'] ?? false)
                <div class="flex justify-end pt-4">
                    <a href="{{ $selectedNews['link'] }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold px-4 py-2 sm:px-6 sm:py-3 rounded-xl transition-all shadow-lg hover:shadow-orange-600/30 transform hover:-translate-y-0.5">
                        <span>Enroll in a course</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
