<!DOCTYPE html>
<html lang="en" x-data="{ dark: localStorage.getItem('dark') === 'true', open: false }" x-init="$watch('dark', val => { localStorage.setItem('dark', val); document.documentElement.classList.toggle('dark', val) })" :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIAC Operations</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('dark') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors">

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-xl font-bold text-gray-800 dark:text-white">AIAC Operations</a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="/dashboard" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Dashboard</a>
                    <a href="/schedule" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Schedule</a>
                    <a href="/rooms" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Rooms</a>
                    <a href="/logs" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Logs</a>
                    <a href="/features" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Features</a>
                    <a href="/bookings" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Bookings</a>
                    <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Cases</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <button @click="dark = !dark" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">🌓</button>
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Logout</button>
                    </form>
                </div>

                <div class="md:hidden flex items-center space-x-3">
                    <button @click="dark = !dark" class="p-2 text-gray-600 dark:text-gray-300">🌓</button>
                    <button @click="open = !open" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak class="md:hidden border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="/dashboard" @click="open = false" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                <a href="/schedule" @click="open = false" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Schedule</a>
                <a href="/rooms" @click="open = false" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Rooms</a>
                <a href="/logs" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Logs</a>
                    <a href="/features" @click="open = false" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Features</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Bookings</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Cases</a>
                <hr class="my-2 dark:border-gray-700">
                <div class="px-3 py-2">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                    @csrf
                    <button class="text-sm text-red-600 dark:text-red-400 font-medium">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        @yield('content')
    </main>
</body>
</html>
