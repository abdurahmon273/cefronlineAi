<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CEFR Online - Ingliz tili CEFR testlari</title>
    <meta name="description" content="CEFR Online orqali ingliz tili darajangizni aniqlang. Online testlar va tayyorgarlik platformasi.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')

</head>
<body>
<div class="min-h-screen bg-white">
    {{-- Topnav --}}
    <div class="sticky top-0 z-50 bg-white">
        <x-ui.topnav variant="{{auth()->hasUser()? 'signed_in' : 'signed_out'}}" />
        <div class="h-px bg-slate-200/70"></div>
    </div>

    <div>
        {{-- Mobile Sidebar Overlay --}}
        <div id="mobile-sidebar-overlay"
             class="fixed inset-0 z-30 bg-black/50 lg:hidden hidden transition-opacity"
             data-mobile-sidebar-overlay></div>

        {{-- Sidebar --}}
        <aside id="mobile-sidebar"
               class="fixed left-0 top-0 z-40 h-full w-[280px] -translate-x-full transform bg-white shadow-xl transition-transform duration-300 ease-in-out lg:translate-x-0 lg:shadow-none top-16"
               data-mobile-sidebar>
            @include('partials.user_menu')
        </aside>

        <div class="flex-1 lg:ml-[280px]">
            @yield('content')
        </div>
    </div>
</div>
<form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>


<div id="alert-container" class="fixed bottom-4 right-4 z-50 space-y-3 max-w-sm w-full">
</div>

@yield('scripts')


    <script src="{{ asset('vendor/sweetAlert/sweetalert2@11.js') }}"></script>

@if (session('error'))
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if( @json(session('error')) =='Please Register or Login!' || @json(session('error')) =='Please Upgrade your plan!' ){

                window.showAlert(@json(session('error')), 'warning', 5000);
                window.highlightElements()
            }else{
                if( @json(session('error')) =='Your Creadit Expired!' ){

                    window.showAlert(@json(session('error')), 'warning', 5000);
                    window.highlightCreditElement()
                }else{

                    window.showAlert(@json(session('error')), 'error', 5000);
                }
            }
        });
    </script>
@endif

@if (session('success'))
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            window.showAlert(@json(session('success')), 'success', 5000);
        });
    </script>
@endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Mobile Sidebar Toggle
            const toggle = document.querySelector('[data-mobile-menu-toggle]');
            const sidebar = document.querySelector('[data-mobile-sidebar]');
            const overlay = document.querySelector('[data-mobile-sidebar-overlay]');

            if (toggle && sidebar && overlay) {
                const open = () => {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                };

                const close = () => {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                };

                toggle.addEventListener('click', open);
                overlay.addEventListener('click', close);

                // Close on escape
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                        close();
                    }
                });

                // Close sidebar when clicking menu items on mobile
                sidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 1024) {
                            close();
                        }
                    });
                });
            }

            // Global Click Delegation for Topnav & Notifications
            document.addEventListener('click', (e) => {
                // --- Topnav Profile Dropdown ---
                const topnavProfile = e.target.closest('[data-topnav-profile]');
                if (topnavProfile) {
                    const nav = topnavProfile.closest('[data-topnav]');
                    const menu = nav ? nav.querySelector('[data-topnav-menu]') : null;
                    if (menu) {
                        e.stopPropagation();
                        e.preventDefault(); // Prevent default if it's a link

                        const wasHidden = menu.hidden;

                        // Close all open topnavs first
                        document.querySelectorAll('[data-topnav]').forEach(n => {
                            const m = n.querySelector('[data-topnav-menu]');
                            const p = n.querySelector('[data-topnav-profile]');
                            if(m) m.hidden = true;
                            if(p) p.setAttribute('aria-expanded', 'false');
                            n.setAttribute('data-open', 'false');
                        });

                        // Toggle current
                        if (wasHidden) {
                            menu.hidden = false;
                            topnavProfile.setAttribute('aria-expanded', 'true');
                            nav.setAttribute('data-open', 'true');
                        }
                        return;
                    }
                }

                // --- Notifications Dropdown ---
                const notifTrigger = e.target.closest('[data-notification-trigger]');
                if (notifTrigger) {
                    e.stopPropagation();
                    const modal = document.querySelector('[data-notification-modal]');
                    if (modal) {
                         modal.classList.toggle('hidden');
                    }
                    return;
                }

                const notifClose = e.target.closest('[data-notification-close]');
                if (notifClose) {
                    e.stopPropagation();
                    const modal = document.querySelector('[data-notification-modal]');
                    if (modal) modal.classList.add('hidden');
                    return;
                }

                // --- Close actions when clicking outside ---

                // Close Topnav if clicked outside
                if (!e.target.closest('[data-topnav-menu]') && !e.target.closest('[data-topnav-profile]')) {
                    document.querySelectorAll('[data-topnav]').forEach(nav => {
                        const menu = nav.querySelector('[data-topnav-menu]');
                        const profile = nav.querySelector('[data-topnav-profile]');
                        if (menu && !menu.hidden) {
                            menu.hidden = true;
                            if(profile) profile.setAttribute('aria-expanded', 'false');
                            nav.setAttribute('data-open', 'false');
                        }
                    });
                }

                // Close Notification if clicked outside
                const notifModal = document.querySelector('[data-notification-modal]');
                if (notifModal && !notifModal.classList.contains('hidden')) {
                    // logic: if click is NOT inside modal AND NOT on trigger
                    if (!notifModal.contains(e.target) && !e.target.closest('[data-notification-trigger]')) {
                        notifModal.classList.add('hidden');
                    }
                }
            });

            // Global Keydown Handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    // Close Topnavs
                    document.querySelectorAll('[data-topnav]').forEach(nav => {
                         const menu = nav.querySelector('[data-topnav-menu]');
                         const profile = nav.querySelector('[data-topnav-profile]');
                         if (menu) menu.hidden = true;
                         if (profile) profile.setAttribute('aria-expanded', 'false');
                         nav.setAttribute('data-open', 'false');
                    });

                    // Close Notifications
                    const notifModal = document.querySelector('[data-notification-modal]');
                    if (notifModal && !notifModal.classList.contains('hidden')) {
                        notifModal.classList.add('hidden');
                    }
                }
            });
        });
    </script>

</body>
</html>
