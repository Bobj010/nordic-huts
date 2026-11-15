<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NORDIC HUTS — Эко-глэмпинг в Карелии')</title>

    <!-- Google Fonts: Noto Serif + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (v4 Play CDN with custom theme config) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        'primary': '#2D4A3E',
                        'primary-dark': '#233B31',
                        'primary-light': '#3D6354',
                        'secondary': '#C05621',
                        'secondary-dark': '#A14009',
                        'secondary-light': '#D97706',
                        'background': '#FAFAF9',
                        'surface': '#FFFFFF',
                        'linen': '#F5EFEB',
                        'linen-light': '#FAF7F2',
                        'border-subtle': '#E7E5E4',
                        'text-main': '#1C1917',
                        'text-muted': '#78716C',
                        'text-light': '#A8A29E',
                    },
                    fontFamily: {
                        'serif': ['"Noto Serif"', 'serif'],
                        'sans': ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    boxShadow: {
                        'elevation-1': '0 4px 20px -2px rgba(28, 25, 23, 0.05), 0 2px 6px -1px rgba(28, 25, 23, 0.02)',
                        'elevation-2': '0 12px 32px -4px rgba(28, 25, 23, 0.08), 0 4px 12px -2px rgba(28, 25, 23, 0.03)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for lightweight reactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 24;
            font-size: 20px;
            line-height: 1;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-background text-text-main font-sans antialiased selection:bg-primary selection:text-white flex flex-col min-h-screen">

    <!-- DEMO TEACHER QUICK-LOGIN BAR (Sticky Top Header Anchor) -->
    <div class="bg-linen-light border-b border-border-subtle/80 py-2 px-4 text-xs">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 text-text-muted font-medium">
                <span class="material-symbols-outlined text-primary text-base">school</span>
                <span>Быстрый вход для преподавателя в 1 клик:</span>
            </div>
            <div class="flex items-center flex-wrap gap-1.5">
                <a href="{{ route('quick-login', 'guest') }}" 
                   class="px-2.5 py-1 rounded transition-all {{ !auth()->check() ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface text-text-main border border-border-subtle hover:bg-linen' }}">
                    Гость
                </a>
                <a href="{{ route('quick-login', 'client') }}" 
                   class="px-2.5 py-1 rounded transition-all {{ auth()->check() && auth()->user()->isClient() ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface text-text-main border border-border-subtle hover:bg-linen' }}">
                    Клиент (Анна)
                </a>
                <a href="{{ route('quick-login', 'manager') }}" 
                   class="px-2.5 py-1 rounded transition-all {{ auth()->check() && auth()->user()->isManager() ? 'bg-secondary text-white font-semibold shadow-xs' : 'bg-surface text-text-main border border-border-subtle hover:bg-linen' }}">
                    Менеджер (Елена)
                </a>
                <a href="{{ route('quick-login', 'admin') }}" 
                   class="px-2.5 py-1 rounded transition-all {{ auth()->check() && auth()->user()->isAdmin() ? 'bg-stone-800 text-white font-semibold shadow-xs' : 'bg-surface text-text-main border border-border-subtle hover:bg-linen' }}">
                    Админ (Александр)
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN STICKY NAVIGATION -->
    <header class="sticky top-0 z-50 bg-surface/95 backdrop-blur-md border-b border-border-subtle" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex justify-between items-center">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="material-symbols-outlined text-primary text-2xl group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 1;">forest</span>
                <div class="flex flex-col">
                    <span class="font-serif text-xl tracking-wider text-primary font-semibold uppercase leading-none">NORDIC HUTS</span>
                    <span class="text-[10px] tracking-widest text-text-muted uppercase font-sans mt-0.5">Карельский эко-глэмпинг</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="{{ route('home') }}" 
                   class="{{ request()->routeIs('home') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-text-muted hover:text-primary transition-colors' }}">
                    Главная
                </a>
                <a href="{{ route('catalog') }}" 
                   class="{{ request()->routeIs('catalog*') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-text-muted hover:text-primary transition-colors' }}">
                    Каталог домиков
                </a>
                <a href="{{ route('home') }}#about" class="text-text-muted hover:text-primary transition-colors">
                    О комплексе
                </a>
                <a href="{{ route('home') }}#reviews" class="text-text-muted hover:text-primary transition-colors">
                    Отзывы
                </a>
            </nav>

            <!-- User Auth Status / Dropdown -->
            <div class="hidden md:flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-medium text-text-main hover:text-primary transition-colors">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded text-sm font-medium transition-colors shadow-xs">
                        Регистрация
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden lg:block">
                            <div class="text-sm font-medium text-text-main leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-text-muted capitalize">
                                @if(auth()->user()->isAdmin())
                                    <span class="text-stone-800 font-semibold">Администратор</span>
                                @elseif(auth()->user()->isManager())
                                    <span class="text-secondary font-semibold">Менеджер</span>
                                @else
                                    Гость-клиент
                                @endif
                            </div>
                        </div>

                        <!-- Role specific quick link button -->
                        @if(auth()->user()->isClient())
                            <a href="{{ route('client.bookings') }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-linen hover:bg-linen/80 text-primary border border-border-subtle rounded text-xs font-medium transition-colors">
                                <span class="material-symbols-outlined text-sm">book_online</span>
                                <span>Мои бронирования</span>
                            </a>
                        @elseif(auth()->user()->isManager())
                            <a href="{{ route('manager.index') }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-secondary/10 hover:bg-secondary/20 text-secondary border border-secondary/30 rounded text-xs font-semibold transition-colors">
                                <span class="material-symbols-outlined text-sm">dashboard</span>
                                <span>Панель менеджера</span>
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white rounded text-xs font-semibold transition-colors">
                                <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                                <span>Админ-панель</span>
                            </a>
                        @endif

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="p-2 text-text-muted hover:text-secondary rounded hover:bg-linen transition-colors"
                                    title="Выйти из аккаунта">
                                <span class="material-symbols-outlined">logout</span>
                            </button>
                        </form>
                    </div>
                @endguest
            </div>

            <!-- Mobile Hamburger Toggle -->
            <div class="flex md:hidden items-center">
                <button @click="mobileMenu = !mobileMenu" type="button" class="p-2 text-text-muted hover:text-primary">
                    <span class="material-symbols-outlined text-2xl" x-text="mobileMenu ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-border-subtle bg-surface px-4 py-4 space-y-3">
            <a href="{{ route('home') }}" class="block py-2 text-sm font-medium text-text-main">Главная</a>
            <a href="{{ route('catalog') }}" class="block py-2 text-sm font-medium text-text-main">Каталог домиков</a>
            <a href="{{ route('home') }}#about" class="block py-2 text-sm font-medium text-text-main">О комплексе</a>
            <a href="{{ route('home') }}#reviews" class="block py-2 text-sm font-medium text-text-main">Отзывы</a>
            <div class="pt-3 border-t border-border-subtle flex flex-col gap-2">
                @guest
                    <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm font-medium border border-border-subtle rounded">Войти</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2 text-sm font-medium bg-primary text-white rounded">Регистрация</a>
                @else
                    <div class="text-sm font-medium text-text-main">{{ auth()->user()->name }}</div>
                    @if(auth()->user()->isClient())
                        <a href="{{ route('client.bookings') }}" class="block py-2 text-sm font-medium text-primary">Мои бронирования</a>
                    @elseif(auth()->user()->isManager())
                        <a href="{{ route('manager.index') }}" class="block py-2 text-sm font-medium text-secondary">Панель менеджера</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.index') }}" class="block py-2 text-sm font-medium text-stone-900">Админ-панель</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left py-2 text-sm text-secondary font-medium">Выйти</button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <!-- FLASH TOAST NOTIFICATIONS (Alpine.js) -->
    <div class="fixed bottom-6 right-6 z-50 space-y-3 max-w-md w-full px-4 sm:px-0">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="bg-surface border-l-4 border-primary shadow-elevation-2 rounded p-4 flex items-start gap-3 transition-all"
                 role="alert">
                <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <div class="flex-1 text-sm text-text-main font-medium">{{ session('success') }}</div>
                <button @click="show = false" class="text-text-muted hover:text-text-main"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="bg-surface border-l-4 border-secondary shadow-elevation-2 rounded p-4 flex items-start gap-3 transition-all"
                 role="alert">
                <span class="material-symbols-outlined text-secondary text-xl" style="font-variation-settings: 'FILL' 1;">error</span>
                <div class="flex-1 text-sm text-text-main font-medium">{{ session('error') }}</div>
                <button @click="show = false" class="text-text-muted hover:text-text-main"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif

        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="bg-surface border-l-4 border-stone-600 shadow-elevation-2 rounded p-4 flex items-start gap-3 transition-all"
                 role="alert">
                <span class="material-symbols-outlined text-stone-600 text-xl" style="font-variation-settings: 'FILL' 1;">info</span>
                <div class="flex-1 text-sm text-text-main font-medium">{{ session('info') }}</div>
                <button @click="show = false" class="text-text-muted hover:text-text-main"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT INJECTION -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- SCANDINAVIAN MINIMALIST FOOTER -->
    <footer class="bg-linen-light border-t border-border-subtle mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                
                <!-- Col 1: Brand & Manifesto -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">forest</span>
                        <span class="font-serif text-lg tracking-wider text-primary font-semibold uppercase">NORDIC HUTS</span>
                    </div>
                    <p class="text-sm text-text-muted leading-relaxed max-w-md">
                        Камерный эко-комплекс авторских домиков на скалистом берегу Ладожских шхер. Архитектура, растворенная в вековом хвойном лесу. Мы создали пространство тишины, чистого воздуха и теплого пара банного чана.
                    </p>
                    <div class="text-xs text-text-light">
                        Карелия, Лахденпохский район, полуостров Тарулинна.
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-primary">Навигация</h4>
                    <ul class="space-y-2 text-sm text-text-muted">
                        <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a></li>
                        <li><a href="{{ route('catalog') }}" class="hover:text-primary transition-colors">Каталог домиков</a></li>
                        <li><a href="{{ route('home') }}#about" class="hover:text-primary transition-colors">О комплексе</a></li>
                        <li><a href="{{ route('home') }}#reviews" class="hover:text-primary transition-colors">Отзывы гостей</a></li>
                    </ul>
                </div>

                <!-- Col 3: Rules & Schedule -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-primary">Условия отдыха</h4>
                    <ul class="space-y-2 text-sm text-text-muted">
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-primary">schedule</span>
                            <span>Заезд: <strong>с 15:00</strong></span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-secondary">schedule</span>
                            <span>Выезд: <strong>до 12:00</strong></span>
                        </li>
                        <li class="text-xs text-text-light pt-1">
                            Круглосуточный ресепшен и бесконтактное заселение.
                        </li>
                        <li class="pt-2">
                            <span class="text-xs text-text-muted">Связь: </span>
                            <a href="tel:+78005553535" class="text-xs font-semibold text-primary hover:underline">+7 (800) 555-35-35</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright & Academic Label -->
            <div class="mt-12 pt-8 border-t border-border-subtle/80 flex flex-col sm:flex-row items-center justify-between text-xs text-text-muted gap-4">
                <div>
                    &copy; {{ date('Y') }} Nordic Huts. Индивидуальный проект сервиса бронирования эко-глэмпинга.
                </div>
                <div class="text-text-light">
                    Стек: Laravel 12 &bull; Blade &bull; Tailwind CSS v4 &bull; Alpine.js &bull; SQLite
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
