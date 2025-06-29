<!DOCTYPE html>
<html lang="en" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UiTM Inventory System')</title>

    <link rel="icon" type="image/png" href="https://www.ibec.unimas.my/images/2020/09/24/uitm.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3490dc',
                        secondary: '#6c757d',
                        success: '#38c172',
                        danger: '#e3342f'
                    },
                    animation: {
                        'fade-in': 'fadeIn 2s ease-out',
                        'slide-in': 'slideIn 1.2s ease-out',
                        'float': 'floatText 4s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        floatText: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' }
                        }
                    }
                }
            }
        };

        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .text-outline {
            text-shadow:
                2px 2px 4px rgba(0,0,0,0.8),
               -2px 2px 4px rgba(0,0,0,0.8),
                2px -2px 4px rgba(0,0,0,0.8),
               -2px -2px 4px rgba(0,0,0,0.8);
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300 ease-in-out min-h-screen">

@auth
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<div class="min-h-screen flex relative">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed z-40 inset-y-0 left-0 w-64 bg-gradient-to-b from-gray-800 to-gray-900 text-white dark:from-gray-900 dark:to-black transform -translate-x-full lg:translate-x-0 lg:relative lg:transform-none transition-transform duration-300 ease-in-out">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-university"></i>
                <span>UiTM Inventory</span>
            </div>
        </div>
        <nav class="flex flex-col p-4 space-y-2 text-sm overflow-y-auto max-h-[calc(100vh-5rem)]">
            @php
                $menuItems = [
                    ['route' => 'dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard'],
                    ['route' => 'assets.index', 'icon' => 'fa-laptop', 'label' => 'Assets'],
                    ['route' => 'users.index', 'icon' => 'fa-users', 'label' => 'Users'],
                    ['route' => 'logs.index', 'icon' => 'fa-history', 'label' => 'Logs'],
                ];
            @endphp

            @foreach($menuItems as $item)
                @if(Route::has($item['route']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-4 py-2 rounded hover:bg-white/10 {{ request()->routeIs($item['route'] . (str_contains($item['route'], '.') ? '*' : '')) ? 'bg-white/10 font-semibold border-l-4 border-primary' : '' }}">
                        <i class="fas {{ $item['icon'] }} w-5"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <!-- Main Content -->
    <div id="mainContent" class="flex-1 flex flex-col transition-all duration-300 ease-in-out ml-0">
        <!-- Topbar -->
        <div class="bg-white dark:bg-gray-800 shadow px-6 py-4 flex justify-between items-center relative z-10">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle" class="block lg:hidden text-gray-700 dark:text-white focus:outline-none z-50">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold">@yield('title', 'UiTM Inventory System')</h1>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <button onclick="toggleDarkMode()" class="px-3 py-1 border rounded text-gray-700 dark:text-white dark:border-white border-gray-700">
                    🌙 Dark Mode
                </button>

                <a href="{{ route('profile.index') }}" class="font-medium text-gray-800 dark:text-gray-200 hover:underline">
                    {{ explode(' ', Auth::user()->name)[0] }}
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Scrollable Main Area -->
        <main class="flex-1 overflow-y-auto px-6 py-4">
            @if(session('success') || session('error'))
                <div class="mb-4 px-4 py-3 rounded border-l-4 {{ session('success') ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' }}">
                    <i class="fas fa-{{ session('success') ? 'check' : 'exclamation' }}-circle mr-2"></i>
                    {{ session('success') ?? session('error') }}
                </div>
            @endif

            <div class="transition-opacity duration-500 ease-in opacity-0 animate-fadein">
                @yield('content')
            </div>
        </main>
    </div>
</div>
@endauth

<!-- Guest View -->
@guest
<div class="h-screen flex flex-col items-center justify-end px-4 pb-10 bg-no-repeat bg-cover bg-center" style="background-image: url('https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiFhIkpkNyI6sJ2B1znxjavoqbOg50JcgYku-7MJWVwsliJ_jivHedI36T3nHhAqRlAQnQ62PWAfY2_CNpPTz5aItno-IFIzEcHv7gm4d11fot_Lr6EXtrU-OKsWEEFY9YsNCjtRvkbUpQ/s1600/5.jpg');">
    <button onclick="toggleDarkMode()" class="absolute top-4 right-4 text-sm px-3 py-1 border rounded text-gray-700 dark:text-white dark:border-white border-gray-700">
        🌙 Toggle Dark Mode
    </button>

    <div class="text-center absolute top-20 w-full animate-float">
        <img src="https://www.ibec.unimas.my/images/2020/09/24/uitm.png" alt="UiTM Logo" class="mx-auto h-16 md:h-20 mb-2 drop-shadow-xl">
        <h1 class="text-3xl md:text-5xl font-bold text-white text-outline">Teaching and Learning</h1>
        <h2 class="text-lg md:text-2xl font-medium text-white text-outline mt-1">Inventory Management Web Application</h2>
        <h3 class="text-md md:text-lg text-white text-outline mt-1">UiTM Perlis</h3>
    </div>

    @yield('content')
</div>
@endguest

<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleButton = document.getElementById('sidebarToggle');

        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            toggleButton.classList.add('hidden');
        };

        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            toggleButton.classList.remove('hidden');
        };

        toggleButton?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', (e) => {
            if (e.target === overlay) closeSidebar();
        });

        document.querySelectorAll('.animate-fadein').forEach(el => {
            setTimeout(() => el.classList.remove('opacity-0'), 50);
        });
    });
</script>

<style>
    @keyframes fadein {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .animate-fadein {
        animation: fadein 0.5s ease-in forwards;
    }

    @keyframes pulse-slow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
        }
    }

    .animate-pulse-slow {
        animation: pulse-slow 2.5s ease-in-out infinite;
    }

    .highlight-fair {
        background-color: #FEF3C7;
        border-left: 4px solid #FBBF24;
    }

    .highlight-poor {
        background-color: #FEE2E2;
        border-left: 4px solid #EF4444;
    }
</style>

@stack('scripts')
</body>
</html>
