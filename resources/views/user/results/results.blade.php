@extends('layouts.user')

@section('content')
    @php
        $asset = fn($p) => asset("assets/{$p}");
        $assetsIcon = fn($p) => asset("assets/{$p}");
    @endphp

    {{-- Main Container --}}
    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                        {{-- Header --}}
                        <div class="mb-4 flex items-center gap-4">
                            <h1 class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Results</h1>
                        </div>

                        <div class="mb-6">
                            <nav class="flex space-x-1 bg-gray-100 rounded-lg p-1" aria-label="Tabs">

                                <button data-tab="listening" class="tab-button  bg-white text-orange-600 shadow-sm    flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                                    Listening
                                </button>
                                <button data-tab="reading" class="tab-button  text-gray-500 hover:text-gray-700    flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                                    Reading
                                </button>
                                <button data-tab="writing" class="tab-button  text-gray-500 hover:text-gray-700    flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                                    Writing
                                </button>
                                <button data-tab="speaking" class="tab-button  text-gray-500 hover:text-gray-700   flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                                    Speaking
                                </button>

                                <button data-tab="full-exam" class="tab-button text-gray-500 hover:text-gray-700 flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                                    Full exam
                                </button>
                            </nav>
                        </div>




                        {{-- LISTENING --}}
                        <div id="tab-listening" class="tab-content ">
                            <livewire:user.results.listening-index-livewire></livewire:user.results.listening-index-livewire>
                        </div>

                        {{-- READING --}}
                        <div id="tab-reading" class="tab-content hidden">
                            <livewire:user.results.reading-index-livewire></livewire:user.results.reading-index-livewire>

                        </div>

                        {{-- WRITING --}}
                        <div id="tab-writing" class="tab-content hidden">

                            <livewire:user.results.writing-index-livewire></livewire:user.results.writing-index-livewire>
                        </div>

                        {{-- SPEAKING --}}
                        <div id="tab-speaking" class="tab-content hidden">
                            <livewire:user.results.speaking-index-livewire></livewire:user.results.speaking-index-livewire>
                        </div>

                        {{-- Full Mock --}}
                        <div id="tab-full-exam" class="tab-content hidden">
                            <livewire:user.results.full-exam-index-livewire></livewire:user.results.full-exam-index-livewire>
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
        (() => {
            const STORAGE_KEY = 'cefr-results-active-tab';

            const tabButtons  = Array.from(document.querySelectorAll('.tab-button'));
            const tabContents = Array.from(document.querySelectorAll('.tab-content'));

            if (!tabButtons.length || !tabContents.length) return;

            const setActive = (tabId) => {
                tabButtons.forEach(btn => {
                    const isActive = btn.dataset.tab === tabId;

                    btn.classList.toggle('bg-white', isActive);
                    btn.classList.toggle('text-orange-600', isActive);
                    btn.classList.toggle('shadow-sm', isActive);

                    btn.classList.toggle('text-gray-500', !isActive);
                    btn.classList.toggle('hover:text-gray-700', !isActive);
                });

                tabContents.forEach(content => content.classList.add('hidden'));

                const selected = document.getElementById(`tab-${tabId}`);
                if (selected) selected.classList.remove('hidden');
            };

            const getDefaultTab = () => {
                const activeBtn = tabButtons.find(b => b.classList.contains('bg-white') && b.classList.contains('text-orange-600'));
                return activeBtn?.dataset.tab || tabButtons[0].dataset.tab;
            };

            const saved = localStorage.getItem(STORAGE_KEY);
            const exists = saved && tabButtons.some(b => b.dataset.tab === saved);

            setActive(exists ? saved : getDefaultTab());

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.dataset.tab;
                    localStorage.setItem(STORAGE_KEY, tabId);
                    setActive(tabId);
                    Livewire.dispatch('resetSelection');
                    Livewire.dispatch('tabChanged');
                });
            });
        })();
    </script>

@endsection
