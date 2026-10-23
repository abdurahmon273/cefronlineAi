@props([
    'active' => 'Home page',
    'items' => ['Home page', 'Lessons', 'Favorite videos', 'Practice', 'Profile'],
])

@php
    $asset = fn ($p) => asset("assets/sidebar/{$p}");
    // Map display names to their icon file names
    $iconMap = [
        'Home page' => ['default' => 'home.svg', 'active' => 'home-active.svg'],
        'Lessons' => ['default' => 'note.svg', 'active' => 'note-active.svg'],
        'Favorite videos' => ['default' => 'all-bookmark.svg', 'active' => 'all-bookmark-active.svg'],
        'Practice' => ['default' => 'note.svg', 'active' => 'note-active.svg'],
        'Profile' => ['default' => 'user.svg', 'active' => 'user-active.svg'],
        // Add more mapping here if new items are added
    ];
    $routeMap = [
        'Home page' => route('user.dashboard'),
        'Lessons' => route('user.lessons'),
        'Favorite videos' => route('user.favorite'),
        'Practice' => route('user.practices'),
        'Profile' => route('user.profile'),
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white']) }}>
    <div class="space-y-0.5 p-3">
        @foreach ($items as $item)
            @php
                $isActive = $item === $active;
                $iconInfo = $iconMap[$item] ?? ['default' => 'home.svg', 'active' => 'home-active.svg'];
                $icon = $isActive ? $iconInfo['active'] : $iconInfo['default'];
            @endphp
            <div
                class="flex items-center gap-3 rounded-xl px-4 py-2 h-12 w-[203px] text-base leading-[1.5] font-normal
                    {{ $isActive ? 'bg-[#F7F7F7] text-slate-900' : 'text-slate-500 hover:bg-[#F7F7F7]' }}"
                style="cursor: pointer;"
                @if(!$isActive)
                    data-sidebar-item="{{ $item }}"
                    tabindex="0"
                    role="button"
                @endif
            >
                <img
                    src="{{ $asset($icon) }}"
                    alt="{{ $item }}"
                    class="h-5 w-5 {{ $isActive ? 'opacity-100' : 'opacity-40' }}"
                >
                <span>{{ $item }}</span>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sidebarItems = document.querySelectorAll('[data-sidebar-item]');
        var routes = @json($routeMap);

        sidebarItems.forEach(function (el) {
            el.addEventListener('click', function () {
                var item = el.getAttribute('data-sidebar-item');
                if (routes[item]) {
                    window.location.assign(routes[item]);
                }
            });
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    var item = el.getAttribute('data-sidebar-item');
                    if (routes[item]) {
                        window.location.assign(routes[item]);
                    }
                }
            });
        });
    });
</script>
