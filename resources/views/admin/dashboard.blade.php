@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Administration</h1>

    @php
    $sectionMeta = [
        'users.index' => ['icon' => '👤', 'desc' => 'Manage user accounts and roles'],
        'permissions.index' => ['icon' => '🔐', 'desc' => 'Assign permissions to roles'],
        'logs.index' => ['icon' => '📜', 'desc' => 'Review system activity logs'],
        'menus.index' => ['icon' => '🧩', 'desc' => 'Configure the navigation menus'],
    ];
    @endphp

    @if($sections->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">You do not have access to any admin sections.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($sections as $section)
            @php $meta = $sectionMeta[$section->route_name] ?? ['icon' => '⚙️', 'desc' => $section->label]; @endphp
            <a href="{{ $section->route_name ? route($section->route_name) : '#' }}"
                class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <div class="flex items-start gap-3">
                    <span class="text-3xl">{{ $meta['icon'] }}</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $section->label }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $meta['desc'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
