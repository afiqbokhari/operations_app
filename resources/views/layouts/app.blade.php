<!DOCTYPE html>
<html lang="en" x-data="{ dark: localStorage.getItem('dark') === 'true', open: false }"
    x-init="$watch('dark', val => { localStorage.setItem('dark', val); document.documentElement.classList.toggle('dark', val) })"
    :class="{ 'dark': dark }">

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
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors">

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-xl font-bold text-gray-800 dark:text-white">AIAC Operations</a>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    @php
                    $navMenus = \App\Models\Menu::where('is_active',
                    true)->whereNull('parent_id')->orderBy('order')->get();
                    @endphp
                    @foreach($navMenus as $menu)
                    @if(!$menu->permission || \App\Models\Permission::can(auth()->user()->role, $menu->permission,
                    'view'))
                    @if($menu->children->isNotEmpty())
                    <div class="relative group">
                        <button
                            class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium px-3 py-2 rounded flex items-center">
                            {{ $menu->label }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-1 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[180px]">
                            @foreach($menu->children as $child)
                            @if(!$child->permission || \App\Models\Permission::can(auth()->user()->role,
                            $child->permission, 'view'))
                            <a href="{{ $child->route_name ? route($child->route_name) : '#' }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded first:rounded-t-lg last:rounded-b-lg">
                                {{ $child->label }}
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @else
                    <a href="{{ $menu->route_name ? route($menu->route_name) : '#' }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium px-3 py-2 rounded">
                        {{ $menu->label }}
                    </a>
                    @endif
                    @endif
                    @endforeach
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative" x-data="{ query: '', results: [], open: false }">
                        <input type="text" x-model="query"
                            @input.debounce.300ms="if(query.length >= 2) { fetch('/api/search?q=' + query).then(r => r.json()).then(data => { results = data; open = data.length > 0; }) } else { results = []; open = false; }"
                            @click.away="open = false" placeholder="Search..."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1.5 w-48 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div x-show="open"
                            class="absolute top-full left-0 mt-1 w-72 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                            <template x-for="r in results" :key="r.url">
                                <a :href="r.url"
                                    class="block px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 border-b dark:border-gray-600 last:border-0">
                                    <div class="text-xs font-medium text-gray-900 dark:text-white" x-text="r.title">
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <span x-text="r.type" class="font-medium"></span> — <span
                                            x-text="r.subtitle"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                    <button @click="dark = !dark"
                        class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">🌓</button>
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Logout</button>
                    </form>
                </div>

                <div class="md:hidden flex items-center space-x-3">
                    <button @click="dark = !dark" class="p-2 text-gray-600 dark:text-gray-300">🌓</button>
                    <button @click="open = !open"
                        class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-cloak class="md:hidden border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 space-y-1">
                @foreach($navMenus as $menu)
                @if(!$menu->permission || \App\Models\Permission::can(auth()->user()->role, $menu->permission, 'view'))
                <a href="{{ $menu->route_name ? route($menu->route_name) : '#' }}" @click="open = false"
                    class="block px-3 py-2 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    {{ $menu->label }}
                </a>
                @if($menu->children->isNotEmpty())
                @foreach($menu->children as $child)
                @if(!$child->permission || \App\Models\Permission::can(auth()->user()->role, $child->permission,
                'view'))
                <a href="{{ $child->route_name ? route($child->route_name) : '#' }}" @click="open = false"
                    class="block px-6 py-1.5 rounded text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 ml-4">
                    ↳ {{ $child->label }}
                </a>
                @endif
                @endforeach
                @endif
                @endif
                @endforeach
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