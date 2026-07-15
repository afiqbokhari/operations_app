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

    {{-- Search Results Modal --}}
    <div x-data="{ searchOpen: false, searchQuery: '', searchBookings: [], searchEvents: [] }"
        @keydown.escape.window="searchOpen = false" x-show="searchOpen" x-cloak
        class="fixed inset-0 z-[9999] flex items-start justify-center pt-20"
        x-on:open-search.window="searchOpen = true; searchQuery = $event.detail.query; fetch('/api/search?q=' + $event.detail.query + '&type=' + ($event.detail.type || '') + '&date_from=' + ($event.detail.date_from || '') + '&date_to=' + ($event.detail.date_to || '') + '&status=' + ($event.detail.status || '')).then(r => r.json()).then(data => { searchBookings = data.filter(x => x.type === 'Hearing'); searchEvents = data.filter(x => x.type === 'Event'); })">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="searchOpen = false"></div>
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] overflow-y-auto z-10 mx-4">
            <div
                class="flex items-center justify-between p-4 border-b dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-800 rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Search Results for "<span
                        x-text="searchQuery"></span>"</h3>
                <button @click="searchOpen = false"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">✕</button>
            </div>
            <div class="p-4">
                <template x-if="searchBookings.length > 0">
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hearings (<span
                                x-text="searchBookings.length"></span>)</h4>
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2">ID</th>
                                    <th class="px-3 py-2">Date</th>
                                    <th class="px-3 py-2">Room</th>
                                    <th class="px-3 py-2">Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="b in searchBookings" :key="b.url">
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                        @click="window.location=b.url">
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white"
                                            x-text="b.title.split(' - ')[0]"></td>
                                        <td class="px-3 py-2 text-xs" x-text="b.subtitle.split(' | ')[1] || ''"></td>
                                        <td class="px-3 py-2" x-text="b.subtitle.split(' | ')[0] || ''"></td>
                                        <td class="px-3 py-2" x-text="b.title.split(' - ').slice(1).join(' - ') || ''">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template x-if="searchEvents.length > 0">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Events (<span
                                x-text="searchEvents.length"></span>)</h4>
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2">Event</th>
                                    <th class="px-3 py-2">Date</th>
                                    <th class="px-3 py-2">Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="e in searchEvents" :key="e.url">
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                        @click="window.location=e.url">
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white"
                                            x-text="e.title"></td>
                                        <td class="px-3 py-2 text-xs" x-text="e.subtitle.split(' | ')[1] || ''"></td>
                                        <td class="px-3 py-2" x-text="e.subtitle.split(' | ')[0] || ''"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template x-if="searchBookings.length === 0 && searchEvents.length === 0">
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No results found.</p>
                </template>
            </div>
        </div>
    </div>

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-xl font-bold text-gray-800 dark:text-white">AIAC Operations</a>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    @php
                    $currentModule = session('module', 'bookings');
                    $navMenus = \App\Models\Menu::where('is_active', true)
                        ->where('module', $currentModule)
                        ->whereNull('parent_id')->orderBy('order')->get();
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
                    <div class="flex items-center gap-1 relative"
                        x-data="{ query: '', results: [], open: false, showAdvanced: false, type: '', date_from: '', date_to: '', room: '', status: '' }">
                        {{-- Search Input --}}
                        <div class="relative">
                            <input type="text" x-model="query"
                                @input.debounce.300ms="if(!showAdvanced) { if(query.length >= 2) { fetch('/api/search?q=' + query + '&type=' + type + '&date_from=' + date_from + '&date_to=' + date_to + '&room=' + room + '&status=' + status).then(r => r.json()).then(data => { results = data; open = data.length > 0; }) } else { results = []; open = false; } }"
                                @keydown.enter="$dispatch('open-search', { query: query, type: type, date_from: date_from, date_to: date_to, status: status })"
                                @click.away="open = false" placeholder="Search..."
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1.5 w-40 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            {{-- Dropdown --}}
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
                        {{-- Filter Toggle --}}
                        <button @click="showAdvanced = !showAdvanced"
                            class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </button>
                        {{-- Advanced Panel --}}
                        <div x-show="showAdvanced" x-cloak style="position: absolute; top: 40px; left: 0;"
                            class="w-80 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-[999] p-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Type</label>
                                    <select x-model="type"
                                        class="w-full text-xs border rounded p-1.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="">All</option>
                                        <option value="hearing">Hearings</option>
                                        <option value="event">Events</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                                    <select x-model="status"
                                        class="w-full text-xs border rounded p-1.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="">All</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="tentative">Tentative</option>
                                        <option value="approved">Approved</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date From</label>
                                    <input type="date" x-model="date_from"
                                        @change="if(date_to < date_from) date_to = date_from"
                                        class="w-full text-xs border rounded p-1.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date To</label>
                                    <input type="date" x-model="date_to" :min="date_from"
                                        class="w-full text-xs border rounded p-1.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                            </div>
                            <a @click.prevent="$dispatch('open-search', { query: query, type: type, date_from: date_from, date_to: date_to, status: status })"
                                class="block mt-3 text-center text-xs bg-blue-600 text-white py-1.5 rounded hover:bg-blue-700 cursor-pointer">
                                Search
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('module.switch') }}" method="POST" class="flex items-center">
                        @csrf
                        <input type="hidden" name="module" value="{{ $currentModule === 'bookings' ? 'front_desk' : 'bookings' }}">
                        <button class="px-3 py-1 text-xs font-medium rounded border 
                            {{ $currentModule === 'bookings' ? 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-700 border-green-300 dark:bg-green-900 dark:text-green-300' }}">
                            {{ $currentModule === 'bookings' ? 'Bookings' : 'Front Desk' }}
                        </button>
                    </form>
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