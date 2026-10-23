@extends('layouts.user')

@section('content')
    {{-- Main --}}
    <div class="flex-1 w-full min-w-0 overflow-x-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            @livewire('user.home-livewire')
        </div>
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        (() => {
            // Carousel
            document.querySelectorAll('[data-carousel]').forEach((root) => {
                const track = root.querySelector('[data-carousel-track]');
                const indicators = Array.from(root.querySelectorAll('[data-carousel-to]'));
                if (!track) return;
                const slidesCount = track.children.length;
                if (slidesCount <= 1) return;
                const intervalMs = Number(root.getAttribute('data-interval') || 4500);
                let index = 0;
                let timer = null;
                const slidePct = 100 / slidesCount;
                const setActive = (next) => {
                    index = (next + slidesCount) % slidesCount;
                    track.style.transform = `translate3d(${-index * slidePct}%, 0, 0)`;
                    indicators.forEach((btn, i) => {
                        const active = i === index;
                        btn.classList.toggle('bg-white/70', active);
                        btn.classList.toggle('bg-white/30', !active);
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
                indicators.forEach((btn) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const to = Number(btn.getAttribute('data-carousel-to'));
                        if (Number.isNaN(to) || to < 0 || to >= slidesCount) return;
                        setActive(to);
                        start();
                    });
                });
                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);
                setActive(0);
                start();
            });
        })();
    </script>

@endsection
