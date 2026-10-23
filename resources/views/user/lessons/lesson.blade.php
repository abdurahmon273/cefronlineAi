@extends('layouts.user')

@section('content')
    {{-- Main --}}
    <div class="flex-1">
        <div class="px-6 py-6">
            <div class="grid gap-6 lg:grid-cols-12">
                {{-- Lesson Content --}}
                @livewire('user.lessons.lesson-livewire', ['id' => $id, 'video_id' => $video_id])

                {{-- Right Sidebar - Membership Card --}}
                @include('components.membership-card')
            </div>
        </div>
    </div>
    <script>
        (function() {
            function initTabs() {
                const tabs = document.querySelectorAll('[data-tab]');
                tabs.forEach(tab => {
                    tab.onclick = function() {
                        const targetId = 'tab-' + this.getAttribute('data-tab');

                        // Reset all tabs
                        tabs.forEach(t => {
                            t.classList.remove('bg-white', 'text-orange-600');
                            t.classList.add('text-slate-400', 'hover:bg-slate-50',
                                'hover:text-slate-600');

                            const content = document.getElementById('tab-' + t.getAttribute(
                                'data-tab'));
                            if (content) content.classList.add('hidden');
                        });

                        // Set active tab
                        this.classList.add('bg-white', 'text-orange-600');
                        this.classList.remove('text-slate-400', 'hover:bg-slate-50',
                        'hover:text-slate-600');

                        const activeContent = document.getElementById(targetId);
                        if (activeContent) activeContent.classList.remove('hidden');
                    };
                });
            }

            // Run on load
            if (document.readyState === 'complete') {
                initTabs();
            } else {
                window.addEventListener('load', initTabs);
            }

            // For Livewire navigations/updates
            document.addEventListener('livewire:navigated', initTabs);
            document.addEventListener('livewire:load', initTabs);
        })();
    </script>
@endsection
