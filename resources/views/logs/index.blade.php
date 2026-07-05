@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Activity Logs</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('logs.index') }}" class="flex flex-col sm:flex-row gap-3">
            <select name="action" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Actions</option>
                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
            <select name="entity_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Types</option>
                <option value="Booking" {{ request('entity_type') === 'Booking' ? 'selected' : '' }}>Booking</option>
                <option value="Room" {{ request('entity_type') === 'Room' ? 'selected' : '' }}>Room</option>
                <option value="Feature" {{ request('entity_type') === 'Feature' ? 'selected' : '' }}>Feature</option>
                <option value="Cases" {{ request('entity_type') === 'Cases' ? 'selected' : '' }}>Case</option>
                <option value="Contact" {{ request('entity_type') === 'Contact' ? 'selected' : '' }}>Contact</option>
            </select>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            <a href="{{ route('logs.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Clear</a>
        </form>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Entity</th>
                    <th class="px-4 py-3 hidden md:table-cell">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-3 text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $log->action === 'created' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                            {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs">@if($log->changes) @foreach($log->changes as $change) {!! $change !!}<br> @endforeach @else {{ $log->description }} @endif</td>
                </tr>
                @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
